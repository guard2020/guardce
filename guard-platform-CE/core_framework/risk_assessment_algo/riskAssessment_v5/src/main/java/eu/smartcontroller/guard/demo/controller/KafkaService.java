package eu.smartcontroller.guard.demo.controller;

import eu.smartcontroller.guard.demo.model.securityPolicies.TotalVulnerabilityScore;
import eu.smartcontroller.guard.demo.model.securityPolicies.kafkamessage2.VulnerabilityDescription;
import eu.smartcontroller.guard.demo.model.securityPolicies.kafkamessage2.VulnScannerOutput;
import org.springframework.beans.factory.annotation.Value;
import org.springframework.kafka.annotation.KafkaListener;
import org.springframework.stereotype.Service;

import java.io.*;
import java.lang.annotation.Annotation;
import java.time.LocalDateTime;
import java.time.format.DateTimeFormatter;
import java.util.Properties;

import static eu.smartcontroller.guard.demo.SpringBootKafkaAppApplication.*;
import static eu.smartcontroller.guard.demo.service.DroolsEngineService.kieSession;

@Service
public class KafkaService {

    @Value(value = "${topicName}")
    private String topicName;

    // Annotation required to listen the message from Kafka server
    @KafkaListener(topics = "${topicName}",
    //@KafkaListener(topics = "vuln_scanner",
            groupId = "node", containerFactory
            = "vulnScannerOutputListener",
            id="assigned_listener_id")
    public void publish(VulnScannerOutput vulnScannerOutput)
    {
        // Print the time
        DateTimeFormatter dtf = DateTimeFormatter.ofPattern("yyyy/MM/dd HH:mm:ss SSS");
        LocalDateTime now = LocalDateTime.now();
        System.out.println("start time: " + dtf.format(now));

        // insert TotalVulnerabilityScore instance into memory
        TotalVulnerabilityScore totalVulnerabilityScore = new TotalVulnerabilityScore();
        kieSession.insert(totalVulnerabilityScore);

        /*// insert VulnerabilityDescription instances into memory
        int numberOfResults = algo5Output.getInternal().getScanTarget().getResults().size();
        for (int i = 0; i < numberOfResults; i++) {
            VulnerabilityDescription vulnerabilityDescription = new VulnerabilityDescription(algo5Output.getInternal().getScanTarget().getResults().get(i));
            //System.out.println("vulnerabilityDescription number " + (i+1) + ": " + vulnerabilityDescription);
            kieSession.insert(vulnerabilityDescription);
        }*/

        // insert VulnerabilityDescription instances into memory #1
        int numberOfResults = vulnScannerOutput.message.openVAS.report.guardApi.results.size();
        for (int i = 0; i < numberOfResults; i++) {
            VulnerabilityDescription vulnerabilityDescription = new VulnerabilityDescription(vulnScannerOutput.message.openVAS.report.guardApi.results.get(i));
            System.out.println("vulnerabilityDescription number " + (i+1) + ": " + vulnerabilityDescription);
            kieSession.insert(vulnerabilityDescription);
        }

        // insert VulnerabilityDescription instances into memory #2
        numberOfResults = vulnScannerOutput.message.openVAS.report.orthanc.results.size();
        for (int i = 0; i < numberOfResults; i++) {
            VulnerabilityDescription vulnerabilityDescription = new VulnerabilityDescription(vulnScannerOutput.message.openVAS.report.orthanc.results.get(i));
            System.out.println("vulnerabilityDescription number " + (i+1) + ": " + vulnerabilityDescription);
            kieSession.insert(vulnerabilityDescription);
        }

        // insert VulnerabilityDescription instances into memory #1
        // ...
        // ...

        // fire the Drools rules
        kieSession.fireAllRules();
    }
}