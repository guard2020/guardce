package eu.smartcontroller.guard.demo.controller;

import eu.smartcontroller.guard.demo.model.securityPolicies.KieFileSystemExample;
import eu.smartcontroller.guard.demo.model.securityPolicies.TotalVulnerabilityScore;
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

import java.time.LocalDateTime;
import java.time.format.DateTimeFormatter;

import static eu.smartcontroller.guard.demo.SpringBootKafkaAppApplication.topicToPublish;


@RestController
@RequestMapping("gfg")
public class KafkaProducerController {

	private KieServices ks;
	private KieContainer kContainer;
	private KieSession kSession;
	private KieFileSystem kfs;
	public static KieFileSystemExample kfse= new KieFileSystemExample();
	private static String EXTERNAL_DRL_RESOURCE = "./externalrules/";

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
	private static KafkaTemplate<String, TotalVulnerabilityScore> kafkaTemplate;

	@Autowired
	public KafkaProducerController(DroolsEngineService droolsEngineService, KafkaTemplate<String, TotalVulnerabilityScore> kafkaTemplate) {
		this.droolsEngineService = droolsEngineService;
		this.kafkaTemplate = kafkaTemplate;
	}

	private static final String TOPIC = topicToPublish;
	public static void publishVulnerabilityScore(TotalVulnerabilityScore totalVulnerabilityScore) {
		totalVulnerabilityScore.setDescription("Qualitative and quantitative risk");
		totalVulnerabilityScore.setOrigin("Risk Assessment");
		kafkaTemplate.send(topicToPublish, totalVulnerabilityScore);

		// Print the time
		DateTimeFormatter dtf = DateTimeFormatter.ofPattern("yyyy/MM/dd HH:mm:ss SSS");
		LocalDateTime now = LocalDateTime.now();
		System.out.println("end time: " + dtf.format(now));
	}
}
