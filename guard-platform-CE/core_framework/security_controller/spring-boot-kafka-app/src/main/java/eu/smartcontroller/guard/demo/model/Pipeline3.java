package eu.smartcontroller.guard.demo.model;

import eu.smartcontroller.guard.demo.controller.KafkaProducerController;
import eu.smartcontroller.guard.demo.model.agents.Agent_config;
import eu.smartcontroller.guard.demo.model.agents.Agent_config2;
import eu.smartcontroller.guard.demo.model.agents.Agent_instance;
import eu.smartcontroller.guard.demo.model.algorithms.Algorithm_config;

import java.util.ArrayList;

public class Pipeline3 {

    // attributes added for smart controller
    private String uuid;
    private String scStatus;

    // original attributes
    private String agent_catalog_id;
    private ArrayList<Agent_config2> agent_configs;
    private String algorithm_catalog_id;
    //private ArrayList<Algorithm_config> algorithm_configs;
    private String created_at;
    private String id;
    private String name;
    private String status;
    private String updated_at;
    private String user;
    private String policy;

    public String getUuid() {
        return uuid;
    }

    public void setUuid(String uuid) {
        this.uuid = uuid;
    }

    public String getScStatus() {
        return scStatus;
    }

    public void setScStatus(String scStatus) {
        this.scStatus = scStatus;
    }

    public String getAgent_catalog_id() {
        return agent_catalog_id;
    }

    public void setAgent_catalog_id(String agent_catalog_id) {
        this.agent_catalog_id = agent_catalog_id;
    }

    public ArrayList<Agent_config2> getAgent_configs() {
        return agent_configs;
    }

    public void setAgent_configs(ArrayList<Agent_config2> agent_configs) {
        this.agent_configs = agent_configs;
    }

    public String getAlgorithm_catalog_id() {
        return algorithm_catalog_id;
    }

    public void setAlgorithm_catalog_id(String algorithm_catalog_id) {
        this.algorithm_catalog_id = algorithm_catalog_id;
    }

/*    public ArrayList<Algorithm_config> getAlgorithm_configs() {
        return algorithm_configs;
    }

    public void setAlgorithm_configs(ArrayList<Algorithm_config> algorithm_configs) {
        this.algorithm_configs = algorithm_configs;
    }*/

    public String getCreated_at() {
        return created_at;
    }

    public void setCreated_at(String created_at) {
        this.created_at = created_at;
    }

    public String getId() {
        return id;
    }

    public void setId(String id) {
        this.id = id;
    }

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public String getStatus() {
        return status;
    }

    public void setStatus(String status) {
        this.status = status;
    }

    public String getUpdated_at() {
        return updated_at;
    }

    public void setUpdated_at(String updated_at) {
        this.updated_at = updated_at;
    }

    public String getUser() {
        return user;
    }

    public void setUser(String user) {
        this.user = user;
    }

    public String getPolicy() {
        return policy;
    }

    public void setPolicy(String policy) {
        this.policy = policy;
    }

    @Override
    public String toString() {
        return "Pipeline3{" +
                "uuid='" + uuid + '\'' +
                ", scStatus='" + scStatus + '\'' +
                ", agent_catalog_id='" + agent_catalog_id + '\'' +
                ", agent_configs=" + agent_configs +
                ", algorithm_catalog_id='" + algorithm_catalog_id + '\'' +
                //", algorithm_configs=" + algorithm_configs +
                ", created_at='" + created_at + '\'' +
                ", id='" + id + '\'' +
                ", name='" + name + '\'' +
                ", status='" + status + '\'' +
                ", updated_at='" + updated_at + '\'' +
                ", user='" + user + '\'' +
                ", policy='" + policy + '\'' +
                '}';
    }

    public String storePipelineToContextBroker () {
        KafkaProducerController.logger.info("Inside storePipelineToContextBroker method");
        return "Pipeline " + id + " is sent to Context Broker to be stored (not implemented)";
    }
}
