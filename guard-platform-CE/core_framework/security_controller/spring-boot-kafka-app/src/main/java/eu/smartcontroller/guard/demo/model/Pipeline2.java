package eu.smartcontroller.guard.demo.model;

import eu.smartcontroller.guard.demo.controller.KafkaProducerController;
import eu.smartcontroller.guard.demo.model.agents.Agent;
import eu.smartcontroller.guard.demo.model.agents.Agent_config;
import eu.smartcontroller.guard.demo.model.agents.Agent_instance;

import java.util.ArrayList;

public class Pipeline2 {

    // attributes added for smart controller
    private String uuid;
    private String scStatus;

    // original attributes
    private Agent_config agent_config;
    private ArrayList<Agent_instance> agent_instances;
    private String created_at;
    private String id;
    private String name;
    private String status;
    private String updated_at;
    private String user;

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

    public Agent_config getAgent_config() {
        return agent_config;
    }

    public void setAgent_config(Agent_config agent_config) {
        this.agent_config = agent_config;
    }

    public ArrayList<Agent_instance> getAgent_instances() {
        return agent_instances;
    }

    public void setAgent_instances(ArrayList<Agent_instance> agent_instances) {
        this.agent_instances = agent_instances;
    }

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

    @Override
    public String toString() {
        return "Pipeline2{" +
                "uuid='" + uuid + '\'' +
                ", scStatus='" + scStatus + '\'' +
                ", agent_config=" + agent_config +
                ", agent_instances=" + agent_instances +
                ", created_at='" + created_at + '\'' +
                ", id='" + id + '\'' +
                ", name='" + name + '\'' +
                ", status='" + status + '\'' +
                ", updated_at='" + updated_at + '\'' +
                ", user='" + user + '\'' +
                '}';
    }

    public String storePipelineToContextBroker () {
        KafkaProducerController.logger.info("Inside storePipelineToContextBroker method");
        return "Pipeline " + id + " is sent to Context Broker to be stored (not implemented)";
    }
}
