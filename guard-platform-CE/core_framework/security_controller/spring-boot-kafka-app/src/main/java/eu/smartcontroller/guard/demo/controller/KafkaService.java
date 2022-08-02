package eu.smartcontroller.guard.demo.controller;

import com.google.gson.JsonObject;
import eu.smartcontroller.guard.demo.cnitml.CnitMLOutput;
import eu.smartcontroller.guard.demo.model.aminer.AMinerOutput;
import eu.smartcontroller.guard.demo.model.contextBroker.*;
import eu.smartcontroller.guard.demo.service.DroolsEngineService;
import org.apache.kafka.common.protocol.types.Field;
import org.springframework.kafka.annotation.KafkaListener;
import org.springframework.stereotype.Service;

import java.time.LocalDateTime;
import java.time.format.DateTimeFormatter;
import java.util.ArrayList;

import static eu.smartcontroller.guard.demo.controller.KafkaProducerController.kfse;
import static eu.smartcontroller.guard.demo.service.DroolsEngineService.kieSession;

@Service
public class KafkaService {

    public static ArrayList<AMinerOutput> aMinerOutputs = new ArrayList<>();
    public static ArrayList<CnitMLOutput> cnitMLOutputs = new ArrayList<>();
    public static String ipAddressToBeBlockedAMiner = "";

    //@Value(value = "${topicName}")
    //private String topicName;

    // Annotation required to listen the message from Kafka server
    //@KafkaListener(topics = "${topicName}",
    @KafkaListener(topics = "aminer-alerts",
            groupId = "node", containerFactory
            = "aMinerOutputListener",
            id="assigned_listener_id")
            //autoStartup = "false")
    public void publish(AMinerOutput aMinerOutput)
    {
        // Print the time
        DateTimeFormatter dtf = DateTimeFormatter.ofPattern("yyyy/MM/dd HH:mm:ss SSS");
        LocalDateTime now = LocalDateTime.now();
        System.out.println("start time: " + dtf.format(now));

        if (aMinerOutput.getAnalysisComponent()!=null){
            if (aMinerOutput.getLogData().getAnnotatedMatchElement().getParserLogHttpHost() != null) {
                DroolsEngineService.addAnalysisComponentToMemory(aMinerOutput);
                ipAddressToBeBlockedAMiner = aMinerOutput.getLogData().getAnnotatedMatchElement().getParserLogHttpHost();
            }
        }

        kfse.insertAllFacts();

        DroolsEngineService.aMinerOutputs.clear();
    }

    // Annotation required to listen the message from Kafka server
    //@KafkaListener(topics = "${topicName}",
    @KafkaListener(topics = "detection-results",
            groupId = "node2", containerFactory
            = "cnitMlOutputListener",
            id="assigned_listener_id2")
    public void publish2(CnitMLOutput cnitMlOutput) throws NoSuchFieldException {
        // Print the time
        DateTimeFormatter dtf = DateTimeFormatter.ofPattern("yyyy/MM/dd HH:mm:ss SSS");
        LocalDateTime now = LocalDateTime.now();
        System.out.println("start time: " + dtf.format(now));

        DroolsEngineService.addAnalysisComponentToMemory2(cnitMlOutput);

        kfse.insertAllFacts();

        DroolsEngineService.cnitMLOutputs.clear();

    }
}