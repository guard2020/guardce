package eu.smartcontroller.guard.demo.controller;

import com.google.gson.Gson;
import com.google.gson.JsonObject;
import com.google.gson.JsonParser;
import eu.smartcontroller.guard.demo.SpringBootKafkaAppApplication;
import eu.smartcontroller.guard.demo.model.*;
import eu.smartcontroller.guard.demo.model.runtimeRules.KieFileSystemExample;
import eu.smartcontroller.guard.demo.model.securityPolicies.HighLevelSecurityPolicy;
import eu.smartcontroller.guard.demo.service.DroolsEngineService;
import org.kie.api.KieServices;
import org.kie.api.builder.KieFileSystem;
import org.kie.api.runtime.KieContainer;
import org.kie.api.runtime.KieSession;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.kafka.core.KafkaTemplate;
import org.springframework.web.bind.annotation.*;

import java.io.BufferedWriter;
import java.io.FileWriter;
import java.io.IOException;
import java.util.UUID;

import static eu.smartcontroller.guard.demo.SpringBootKafkaAppApplication.*;
import static eu.smartcontroller.guard.demo.SpringBootKafkaAppApplication.pipelineArray;
import static eu.smartcontroller.guard.demo.model.runtimeRules.KieFileSystemExample.EXTERNAL_DRL_RESOURCE;
import static eu.smartcontroller.guard.demo.service.DroolsEngineService.kieSession;


@RestController
@RequestMapping("gfg")
public class KafkaProducerController {

	private KieServices ks;
	private KieContainer kContainer;
	private KieSession kSession;
	private KieFileSystem kfs;
	public static KieFileSystemExample kfse= new KieFileSystemExample();


	public static Logger logger = LoggerFactory.getLogger(KafkaProducerController.class);

	@RequestMapping("/")
	public String index() {
		logger.trace("A TRACE Message");
		logger.debug("A DEBUG Message");
		logger.info("An INFO Message");
		logger.warn("A WARN Message");
		logger.error("An ERROR Message");

		return "Howdy! Check out the Logs to see the output...";
	}


	private DroolsEngineService droolsEngineService;
	private final KafkaTemplate<String, TopologyChange> kafkaTemplate;

	@Autowired
	public KafkaProducerController(DroolsEngineService droolsEngineService, KafkaTemplate<String, TopologyChange> kafkaTemplate) {
		this.droolsEngineService = droolsEngineService;
		this.kafkaTemplate = kafkaTemplate;
	}


	// REST API endpoints
	@RequestMapping(value = "/getVulnerabilityMeasurementPolicy", method = RequestMethod.POST, consumes = "application/json", produces = "application/json")
	public VulnerabilityMeasurementPolicy getVulnerabilityMeasurementPolicy(@RequestBody VulnerabilityMeasurementPolicy policy) {
		droolsEngineService.addVulnerabilityMeasurementPolicyToMemory(policy);
		return policy;
	}

	private static final String TOPIC = topologyChangesTopic;
	@PostMapping(value = "/publishTopologyChange", consumes = "application/json", produces = "application/json")
	public String publishStudent(@RequestBody TopologyChange topologyChange) {
		kafkaTemplate.send(TOPIC, topologyChange);
		return "Published successfully";
	}

	// The new policy is added into the Drools working memory
	@PutMapping(value = "/receiveHighLevelSecurityPolicy", consumes = "application/json", produces = "application/json")
	public String receiveHighLevelSecurityPolicy(@RequestBody HighLevelSecurityPolicy highLevelSecurityPolicy) {
		logger.info("In receiveHighLevelSecurityPolicy endpoint.");
		return "In receiveHighLevelSecurityPolicy endpoint.";
	}

	// The new pipeline is added in the local array and then in the working memory.
    // If there is already in the local array, it is updated before the insertion to the working memory.
	@PutMapping(value = "/startSecurityPipeline", consumes = "application/json", produces = "application/json")
	public String startSecurityPipeline(@RequestBody Pipeline3 pipeline3) {

		// put the security into the externalrules file
		// 1. append in the securitypolicies drl file all the imports
		appendStrToFile(EXTERNAL_DRL_RESOURCE + "securitypolicies.drl","package org.drools.example\n" +
				"import eu.smartcontroller.guard.demo.controller.KafkaProducerController\n" +
				"import eu.smartcontroller.guard.demo.model.contextBroker.ContextBrokerUpdateAgentResponse\n" +
				"import eu.smartcontroller.guard.demo.model.Rulefile2\n" +
				"import eu.smartcontroller.guard.demo.model.aminer.AMinerOutput\n" +
				"import eu.smartcontroller.guard.demo.cnitml.CnitMLOutput\n" +
				"import java.util.concurrent.TimeUnit\n", false);

		// 2. append in the securitypolicies drl file the incoming policy
		appendStrToFile(EXTERNAL_DRL_RESOURCE + "securitypolicies.drl", pipeline3.getPolicy(), true);
		logger.info("Security policy 1 " + pipeline3.getPolicy() + " is appended into externalrules folder.");

		// 3. append in the securitypolicies drl file the rule for unblocking IP 1
		appendStrToFile(EXTERNAL_DRL_RESOURCE + "securitypolicies.drl","\n\n// Triggers unBlockIpRequest()\n" +
				"rule \"unBlockIpRequestCnitML\"\n" +
				"when\n" +
				"    $cnitMLOutput: CnitMLOutput($unblock:=unblock, unblock==\"yes\")\n" +
				"then\n" +
				"    String agentId = \"pgafilter@130.251.17.130\";\n" +
				"    TimeUnit.SECONDS.sleep(120);\n" +
				"    String response = $cnitMLOutput.unBlockIpRequest(agentId);\n" +
				"    KafkaProducerController.logger.info(\"unBlockIpRequest method is called. Response: \" + response);\n" +
				"end", true);

		// 4. append in the securitypolicies drl file the rule for unblocking IP 2
		appendStrToFile(EXTERNAL_DRL_RESOURCE + "securitypolicies.drl","\n\n// Triggers unBlockIpRequest()\n" +
				"rule \"unBlockIpRequestAMiner\"\n" +
				"when\n" +
				"    $aMinerOutput: AMinerOutput($unblock:=unblock, unblock==\"yes\")\n" +
				"then\n" +
				"    String agentId = \"ablocker@network-manager\";\n" +
				"    TimeUnit.SECONDS.sleep(120);\n" +
				"    String response = $aMinerOutput.unBlockIpRequest(agentId);\n" +
				"    KafkaProducerController.logger.info(\"unBlockIpRequest method is called. Response: \" + response);\n" +
				"end", true);

		// fill in additional attributes
		//pipeline.setStatus("start");
		String uniqueID = UUID.randomUUID().toString();
		pipeline3.setUuid(uniqueID);

		//logger.info("Received Pipeline: " + pipeline3.toString());

		// check if the pipeline already exists in the internal array of pipelines.
		int index = pipelineArray.pipelineExistsInArray(pipeline3.getId());
		if (index==-1) {
            pipelineArray.addNewPipeline(pipeline3);
            droolsEngineService.addSecurityPipelineToMemory(pipelineArray.getPipelines().get(pipelineArray.getPipelines().size() - 1));

            logger.info("Request to Security Controller startSecurityPipeline API for pipeline " + pipeline3.getId() + " is ended.");
            return "Pipeline " + pipeline3.getId() + " is added into Drools working memory to be started. Send requests to /getSecurityPipelineStatus?pipelineId= to get the status of the pipeline.";
        } else {
		    if (pipelineArray.getPipelines().get(index).getStatus().equals("stopped")
                    || pipelineArray.getPipelines().get(index).getStatus().equals("notUpdated")){
                pipelineArray.getPipelines().get(index).setStatus("start");
                pipelineArray.getPipelines().get(index).setUuid(uniqueID);
				pipelineArray.getPipelines().get(index).setAgent_configs(pipeline3.getAgent_configs());
				//pipelineArray.getPipelines().get(index).setAlgorithm_configs(pipeline3.getAlgorithm_configs());
				pipelineArray.getPipelines().get(index).setAgent_catalog_id(pipeline3.getAgent_catalog_id());
				pipelineArray.getPipelines().get(index).setAlgorithm_catalog_id(pipeline3.getAlgorithm_catalog_id());
				pipelineArray.getPipelines().get(index).setPolicy(pipeline3.getPolicy());

                droolsEngineService.addSecurityPipelineToMemory(pipelineArray.getPipelines().get(index));

                logger.info("Request to Security Controller startSecurityPipeline API for pipeline " + pipeline3.getId() + " is ended.");
                return "Pipeline " + pipeline3.getId() + " is added into Drools working memory to be started. Send requests to /getSecurityPipelineStatus?pipelineId= to get the status of the pipeline.";
            } else if (pipelineArray.getPipelines().get(index).getStatus().equals("started")){
                logger.info("Request to Security Controller startSecurityPipeline API. Response: Pipeline " + pipeline3.getId() + " is already started.");
                return "Pipeline " + pipeline3.getId() + " is already started.";
            }
        }
		logger.info("Exception in startSecurityPipeline.");
		return "Exception in startSecurityPipeline.";
	}

    // The new pipeline is added in the local array and then in the working memory.
    // If there is already in the local array, it is updated before the insertion to the working memory.
	@PutMapping(value = "/stopSecurityPipeline", consumes = "application/json", produces = "application/json")
	public String stopSecurityPipeline(@RequestBody Pipeline2 pipeline2) {

		// delete the contents of securitypolicies file
		appendStrToFile(EXTERNAL_DRL_RESOURCE + "securitypolicies.drl"," ", false);

		// fill in additional attributes
		//pipeline.setStatus("stop");
		String uniqueID = UUID.randomUUID().toString();
		pipeline2.setUuid(uniqueID);

        int index = pipelineArray.pipelineExistsInArray(pipeline2.getId());
        if (index==-1) {
            logger.info("Request to Security Controller stopSecurityPipeline API. Response: Pipeline " + pipeline2.getId() + " does not exist.");
            return "Pipeline " + pipeline2.getId() + " does not exist.";
        } else {
            if (pipelineArray.getPipelines().get(index).getStatus().equals("started")
                    || pipelineArray.getPipelines().get(index).getStatus().equals("notUpdated")){
                pipelineArray.getPipelines().get(index).setStatus("stop");
                pipelineArray.getPipelines().get(index).setUuid(uniqueID);
                droolsEngineService.addSecurityPipelineToMemory(pipelineArray.getPipelines().get(index));
                logger.info("Request to Security Controller stopSecurityPipeline API for pipeline " + pipeline2.getId() + " is ended.");
                return "Pipeline " + pipeline2.getId() + " is added into Drools working memory to be stopped. Send requests to /getSecurityPipelineStatus?pipelineId= to get the status of the pipeline.";
            } else if (pipelineArray.getPipelines().get(index).getStatus().equals("stopped")){
                logger.info("Request to Security Controller stopSecurityPipeline API. Response: Pipeline " + pipeline2.getId() + " is already stopped.");
                return "Pipeline " + pipeline2.getId() + " is already stopped.";
            }
        }
		logger.info("Exception in stopSecurityPipeline.");
		return "Exception in stopSecurityPipeline.";
	}

	// get the SecurityPipeline Status
	@GetMapping(value = "/getSecurityPipelineStatus")
	public String getSecurityPipelineStatus(@RequestParam String pipelineId) {
		return pipelineArray.getPipelineStatus(pipelineId);
	}

	// remove Pipeline from the local array
	@GetMapping(value = "/removeSecurityPipeline")
	public String removeSecurityPipeline(@RequestParam String pipelineId) {
		return pipelineArray.removePipeline(pipelineId);
	}

    // Fire all rules in the working memory.
    @GetMapping(value = "/fireAll")
    public String fireAll() {
        int allRules = kieSession.fireAllRules();
        return "Number of rules: " + allRules;
    }

	// Fire all rules in the working memory.
	@GetMapping(value = "/fireAll2")
	public String fireAll2() {
		kfse.insertAllFacts();
		return "Number of rules2: ";
	}

	// TO append string into a file
	public static void appendStrToFile(String fileName, String str, boolean append)
	{
		// Try block to check for exceptions
		try {
			// Open given file in append mode by creating an
			// object of BufferedWriter class
			BufferedWriter out = new BufferedWriter(new FileWriter(fileName, append));
			// Writing on output stream
			out.write(str);
			// Closing the connection
			out.close();
		}
		// Catch block to handle the exceptions
		catch (IOException e) {
			// Display message when exception occurs
			System.out.println("exception occurred" + e);
		}
	}

}
