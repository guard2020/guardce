package eu.smartcontroller.guard.demo.model;

import eu.smartcontroller.guard.demo.controller.KafkaProducerController;
import eu.smartcontroller.guard.demo.model.agents.Agent;
import eu.smartcontroller.guard.demo.model.algorithms.Algorithm;
import eu.smartcontroller.guard.demo.model.algorithms.AlgorithmInstance;

import java.util.ArrayList;

public class Pipeline {

    private String uuid;
    private String id;
    private String status;

    // guard algorithms
    //private ArrayList<Algorithm> algorithms;
    private ArrayList<AlgorithmInstance> algorithmInstances;
    // guard agents
    private ArrayList<Agent> agents;

    public String getUuid() {
        return uuid;
    }

    public void setUuid(String uuid) {
        this.uuid = uuid;
    }

    public String getId() {
        return id;
    }

    public void setId(String id) {
        this.id = id;
    }

    public String getStatus() {
        return status;
    }

    public void setStatus(String status) {
        this.status = status;
    }

    public ArrayList<Agent> getAgents() {
        return agents;
    }

    public void setAgents(ArrayList<Agent> agents) {
        this.agents = agents;
    }

/*    public ArrayList<Algorithm> getAlgorithms() {
        return algorithms;
    }

    public void setAlgorithms(ArrayList<Algorithm> algorithms) {
        this.algorithms = algorithms;
    }*/

    public ArrayList<AlgorithmInstance> getAlgorithmInstances() {
        return algorithmInstances;
    }

    public void setAlgorithmInstances(ArrayList<AlgorithmInstance> algorithmInstances) {
        this.algorithmInstances = algorithmInstances;
    }

    @Override
    public String toString() {
        return "Pipeline{" +
                "uuid='" + uuid + '\'' +
                ", id='" + id + '\'' +
                ", status='" + status + '\'' +
                //", algorithms=" + algorithms +
                ", algorithmInstances=" + algorithmInstances +
                ", agents=" + agents +
                '}';
    }

    public String storePipelineToContextBroker () {
        KafkaProducerController.logger.info("Inside storePipelineToContextBroker method");
        return "Pipeline " + id + " is sent to Context Broker to be stored (not implemented)";
    }
}
