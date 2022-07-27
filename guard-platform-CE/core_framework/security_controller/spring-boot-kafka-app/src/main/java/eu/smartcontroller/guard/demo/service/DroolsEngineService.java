package eu.smartcontroller.guard.demo.service;

import com.google.gson.JsonArray;
import com.google.gson.JsonObject;
import com.google.gson.JsonParser;
import eu.smartcontroller.guard.demo.SpringBootKafkaAppApplication;
import eu.smartcontroller.guard.demo.cnitml.CnitMLOutput;
import eu.smartcontroller.guard.demo.controller.AlgorithmCNITMLHandler;
import eu.smartcontroller.guard.demo.controller.ContextBrokerHandler2;
import eu.smartcontroller.guard.demo.controller.KafkaService;
import eu.smartcontroller.guard.demo.controller.PgasigHandler;
import eu.smartcontroller.guard.demo.model.*;
import eu.smartcontroller.guard.demo.model.agents.Agent;
import eu.smartcontroller.guard.demo.model.algorithms.Algorithm;
import eu.smartcontroller.guard.demo.model.algorithms.AlgorithmInstance;
import eu.smartcontroller.guard.demo.model.aminer.AMinerOutput;
import eu.smartcontroller.guard.demo.model.contextBroker.*;
import eu.smartcontroller.guard.demo.model.runtimeRules.KieFileSystemExample;
import org.kie.api.runtime.KieContainer;
import org.kie.api.runtime.KieSession;
import org.kie.api.runtime.rule.QueryResults;
import org.kie.api.runtime.rule.QueryResultsRow;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;

import java.util.ArrayList;

@Service
public class DroolsEngineService {

	public static String lastSuccessfulRuleUpdate;
	public static ArrayList<Rulefile2> rulefiles = new ArrayList<>();
	public static ArrayList<AMinerOutput> aMinerOutputs = new ArrayList<>();
	public static ArrayList<CnitMLOutput> cnitMLOutputs = new ArrayList<>();

	private static KieContainer kieContainer;
	public static KieSession kieSession;

	@Autowired
	public DroolsEngineService(KieContainer kieContainer) {
		this.kieContainer = kieContainer;
		this.kieSession = kieContainer.newKieSession("rulesSession");
	}

	public VulnerabilityMeasurementPolicy addVulnerabilityMeasurementPolicyToMemory(VulnerabilityMeasurementPolicy policy) {
		kieSession.insert(policy);
		kieSession.fireAllRules();
		//kieSession.dispose();
		return policy;
	}



	///////////////////////////////////////////////////////////////////////////////
	/////////////////////// methods for the security policies /////////////////////
	///////////////////////////////////////////////////////////////////////////////

	public static Rulefile2 addRulefileToInternalMemory(Rulefile2 rulefile) {
		rulefiles.add(rulefile);
		return rulefile;
	}

	public static String addAnalysisComponentToMemory(AMinerOutput aMinerOutput) {
		aMinerOutputs.add(aMinerOutput);
		return aMinerOutput.getLogData().getAnnotatedMatchElement().getParserLogHttpHost();
	}

	public static String addAnalysisComponentToMemory2(CnitMLOutput cnitMLOutput) {

		cnitMLOutputs.add(cnitMLOutput);
		return cnitMLOutput.toString();
	}

	///////////////////////////////////////////////////////////////////////////////
	///////////////////////////////////////////////////////////////////////////////
	///////////////////////////////////////////////////////////////////////////////

	public Pipeline addSecurityPipelineToMemory(Pipeline3 pipeline3) {
 
   this.kieSession = kieContainer.newKieSession("rulesSession");

		//System.out.println("pipeline3: " + pipeline3);

		if (pipeline3.getStatus().equals("stop")) {
			resetFirewallRules(pipeline3.getAgent_configs().get(0).getAgent_instance_id());
		}

		// create the pipeline to insert into the working memory
		Pipeline pipeline = new Pipeline();
		pipeline.setId(pipeline3.getId());
		pipeline.setStatus(pipeline3.getStatus());
		pipeline.setUuid(pipeline3.getUuid());

		// create the agents of the pipeline to insert into the working memory
		if (pipeline3.getAgent_catalog_id()!=null) {
			ArrayList<Agent> agents = new ArrayList<>();
			for (int i = 0; i < pipeline3.getAgent_configs().size(); i++) {
				Agent agent = new Agent();
				agent.setId(pipeline3.getAgent_configs().get(i).getAgent_instance_id());
				agent.setScStatus("notUpdated");
				agent.setPipelineUuid(pipeline3.getUuid());

				ArrayList<Operations> operations = new ArrayList<>();
				agent.setOperations(operations);
				Operations operation = new Operations();
				operations.add(operation);

				// set the Agent Parameters
				ArrayList<Parameter> parameters = new ArrayList<>();
				if (pipeline3.getAgent_configs().get(i).getParameters() != null) {
					for (int j = 0; j < pipeline3.getAgent_configs().get(i).getParameters().size(); j++) {
						Parameter parameter = new Parameter();
						parameter.setId(pipeline3.getAgent_configs().get(i).getParameters().get(j).getId());
						parameter.setValue(pipeline3.getAgent_configs().get(i).getParameters().get(j).getInput());
						if (parameter.getId() != "" && parameter.getValue() != "") {
							parameters.add(parameter);
						}
					}
				}
				agent.getOperations().get(0).setParameters(parameters);

				// set the Agent Resources
				ArrayList<Resource> resources = new ArrayList<>();
				if (pipeline3.getAgent_configs().get(i).getResources() != null) {
					for (int j = 0; j < pipeline3.getAgent_configs().get(i).getResources().size(); j++) {
						Resource resource = new Resource();
						resource.setContent(pipeline3.getAgent_configs().get(i).getResources().get(j).getContent());
						resource.setId(pipeline3.getAgent_configs().get(i).getResources().get(j).getId());
						if (resource.getContent() != "" && resource.getId() != "") {
							resources.add(resource);
						}
					}
				}
				agent.getOperations().get(0).setResources(resources);

				// set the Agent Actions
				ArrayList<Action> actions = new ArrayList<>();
				Action action = new Action();
				action.setId(pipeline3.getStatus());
				actions.add(action);
				agent.getOperations().get(0).setActions(actions);

				// print agent
				//System.out.println(agent.toString());

				// set the pipeline's ArrayList<Agent>
				agents.add(agent);

				// insert the agent into the working memory
				kieSession.insert(agent);
			}
			pipeline.setAgents(agents);
		}

		// create the algorithms of the pipeline to be inserted into the working memory
		if (pipeline3.getAlgorithm_catalog_id()!=null) {

			ArrayList<AlgorithmInstance> algorithmInstances = new ArrayList<>();

			AlgorithmInstance algorithmInstance = new AlgorithmInstance();

			algorithmInstance.setId(pipeline3.getAlgorithm_catalog_id());
			JsonArray jsonFromString;
			jsonFromString = ContextBrokerHandler2.getAlgorithmInstanceScriptName(pipeline3.getAlgorithm_catalog_id());
			algorithmInstance.setService(jsonFromString.get(0).getAsJsonObject().get("service").getAsString());
			algorithmInstance.setKafkaTopic(jsonFromString.get(0).getAsJsonObject().get("kafkaTopic").getAsString());
			algorithmInstance.setContainerName(jsonFromString.get(0).getAsJsonObject().get("containerName").getAsString());
			algorithmInstance.setScStatus("notUpdated");
			algorithmInstance.setPipelineUuid(pipeline3.getUuid());

			// set the pipeline's ArrayList<AlgorithmInstance>
			algorithmInstances.add(algorithmInstance);

			// insert the algorithmInstance into the working memory
			kieSession.insert(algorithmInstances.get(0));

			pipeline.setAlgorithmInstances(algorithmInstances);
		}

		// insert the created pipeline into the working memory
		kieSession.insert(pipeline);
		int allRules = kieSession.fireAllRules();
		//System.out.println("Number of rules: " + allRules);
		

		/*QueryResults results = kieSession.getQueryResults( "getObjectsOfPipelines" );
		for ( QueryResultsRow row : results ) {
			Pipeline pipeline1 = ( Pipeline ) row.get( "$result" ); //you can retrieve all the bounded variables here
			//do whatever you want with classA
			System.out.println("pipeline= " + pipeline1);
		}

		QueryResults results1 = kieSession.getQueryResults( "getObjectsOfAgents" );
		for ( QueryResultsRow row : results1 ) {
			Agent agent = ( Agent ) row.get( "$result" ); //you can retrieve all the bounded variables here
			//do whatever you want with classA
			System.out.println("agent= " + agent);
		}

		QueryResults results2 = kieSession.getQueryResults( "getObjectsOfAlgorithmInstances" );
		for ( QueryResultsRow row : results2 ) {
			AlgorithmInstance algorithmInstance = ( AlgorithmInstance ) row.get( "$result" ); //you can retrieve all the bounded variables here
			//do whatever you want with classA
			System.out.println("algorithmInstance= " + algorithmInstance);
		}*/

    kieSession.dispose();
		return pipeline;
	}

	private void resetFirewallRules(String agent_instance_id) {
		if (agent_instance_id.equals("ablocker@network-manager")) {
			String messageBody = "{\n" +
					"    \"id\": \""+ agent_instance_id + "\",\n" +
					"    \"operations\": [\n" +
					"    {\n" +
					"        \"actions\": [\n" +
					"        {\n" +
					"            \"id\": \"unblock\",\n" +
					"            \"ip\": \"" + KafkaService.ipAddressToBeBlockedAMiner + "\"\n" +
					"        }\n" +
					"        ]\n" +
					"    }\n" +
					"    ]\n" +
					"}";

			String response = ContextBrokerHandler2.updatefirewallRulesAminer(agent_instance_id, messageBody);
			System.out.println("Reset filter response: " + response);

		} else if (agent_instance_id.equals("pgafilter@130.251.17.130")) {
			String messageBody = "{\n" +
					"    \"id\": \""+ agent_instance_id + "\",\n" +
					"    \"operations\": [\n" +
					"        {\n" +
					"            \"parameters\": [\n" +
					"                {\n" +
					"                    \"id\": \"firewall-rules\",\n" +
					"                    \"value\": [\n" +
					"                    ]\n" +
					"                }\n" +
					"            ]\n" +
					"        },\n" +
					"        {\n" +
					"            \"actions\": [\n" +
					"                {\n" +
					"                    \"id\": \"restart\",\n" +
					"                    \"output_format\": \"lines\"\n" +
					"                }\n" +
					"            ]\n" +
					"        }\n" +
					"    ]\n" +
					"}";

			String response = ContextBrokerHandler2.updatefirewallRulesCnit2(agent_instance_id, messageBody);
			System.out.println("Reset filter response: " + response);

		} else {
			System.out.println("No need to reset filters! agent_instance_id: " + agent_instance_id);
		}
	}

}
