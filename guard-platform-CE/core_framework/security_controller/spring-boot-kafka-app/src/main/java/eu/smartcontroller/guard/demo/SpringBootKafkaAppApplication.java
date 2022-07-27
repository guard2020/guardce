package eu.smartcontroller.guard.demo;

import eu.smartcontroller.guard.demo.controller.KafkaProducerController;
import eu.smartcontroller.guard.demo.controller.PgasigHandler;
import eu.smartcontroller.guard.demo.model.*;
import eu.smartcontroller.guard.demo.service.DroolsEngineService;
import org.kie.api.KieServices;
import org.kie.api.runtime.KieContainer;
import org.springframework.boot.SpringApplication;
import org.springframework.boot.autoconfigure.SpringBootApplication;
import org.springframework.context.annotation.Bean;

import java.util.ArrayList;
import java.util.concurrent.Executors;
import java.util.concurrent.ScheduledExecutorService;
import java.util.concurrent.TimeUnit;

import static eu.smartcontroller.guard.demo.controller.KafkaProducerController.kfse;
import static eu.smartcontroller.guard.demo.service.DroolsEngineService.addRulefileToInternalMemory;
import static eu.smartcontroller.guard.demo.service.DroolsEngineService.kieSession;

@SpringBootApplication
public class SpringBootKafkaAppApplication {

	// env-variables
	static public String kafkaEndpoint;
	static public String topologyChangesTopic;
	static public String contextBrokerManagerEndpoint;
	static public String cnitMlAlgorithmEndpoint;
	static public String pgasigEndpoint;

	// create the internal array of pipelines
	static public PipelineArray pipelineArray = new PipelineArray();
	public static String lastSuccessfulRuleUpdate;

	public static void main(String[] args) {

		SpringApplication.run(SpringBootKafkaAppApplication.class, args);

		//lastSuccessfulRuleUpdate = "2019-01-01T20:20:20";
		lastSuccessfulRuleUpdate = "";
		KafkaProducerController.logger.info("Initial lastSuccessfulRuleUpdate = " + lastSuccessfulRuleUpdate);

		// read kafkaEndpoint(IP:port) from the corresponding env-variable
		kafkaEndpoint=System.getenv("kafkaEndpoint");
		if (kafkaEndpoint==null) {
			//kafkaEndpoint="127.0.0.1:9092";
			kafkaEndpoint="10.0.0.7:9092";
		}
		KafkaProducerController.logger.info("kafkaEndpoint: " + kafkaEndpoint);

		// read topologyChangesTopic from the corresponding env-variable
		topologyChangesTopic=System.getenv("topologyChangesTopic");
		if (topologyChangesTopic==null) {
			topologyChangesTopic="TopologyChanges";
		}
		KafkaProducerController.logger.info("topologyChangesTopic: " + topologyChangesTopic);

		// read contextBrokerManagerEndpoint from the corresponding env-variable
		contextBrokerManagerEndpoint=System.getenv("contextBrokerManagerEndpoint");
		if (contextBrokerManagerEndpoint==null) {
			contextBrokerManagerEndpoint="10.0.0.7:5000";
		}
		KafkaProducerController.logger.info("contextBrokerManagerEndpoint: " + contextBrokerManagerEndpoint);

		// read cnitMlAlgorithmEndpoint from the corresponding env-variable
		cnitMlAlgorithmEndpoint=System.getenv("cnitMlAlgorithmEndpoint");
		if (cnitMlAlgorithmEndpoint==null) {
			//cnitMlAlgorithmEndpoint="guard3.westeurope.cloudapp.azure.com:9999";
			cnitMlAlgorithmEndpoint="10.0.0.7:9999";
		}
		KafkaProducerController.logger.info("cnitMlAlgorithmEndpoint: " + cnitMlAlgorithmEndpoint);

		// read pgasigEndpoint from the corresponding env-variable
		pgasigEndpoint=System.getenv("pgasigEndpoint");
		if (pgasigEndpoint==null) {
			pgasigEndpoint="35.193.65.139:8000";
		}
		KafkaProducerController.logger.info("pgasigEndpoint: " + pgasigEndpoint);

		//kieSession.fireUntilHalt();
		/*// testing Drools rules with cron
		Time time = new Time();
		time.setValue2(1);
		kieSession.insert(time);
		kieSession.fireUntilHalt();*/

		// Every 10 seconds the new Rulefile is exported from pgasig service
		Runnable getNewRulefile = new Runnable()  {
			public void run() {
				Rulefile2 rulefile = PgasigHandler.exportRuleFile();
				if (rulefile.getContent().contains("release:")) {
					addRulefileToInternalMemory(rulefile);
					kfse.insertAllFacts();
					DroolsEngineService.rulefiles.clear();
				}
			}
		};
		ScheduledExecutorService executor = Executors.newScheduledThreadPool(1);
		executor.scheduleAtFixedRate(getNewRulefile, 0, 10, TimeUnit.SECONDS);
	}


	@Bean
	public KieContainer kieContainer() {
		return KieServices.Factory.get().getKieClasspathContainer();
	}


	static public String testMethod (String pgasigEndpoint) {
		return pgasigEndpoint;
	}

}
