package eu.smartcontroller.guard.demo.model.agents;

import eu.smartcontroller.guard.demo.controller.ContextBrokerHandler2;
import eu.smartcontroller.guard.demo.model.algorithms.Algorithm;
import eu.smartcontroller.guard.demo.model.contextBroker.*;
import eu.smartcontroller.guard.demo.model.contextBroker.Parameter;
import eu.smartcontroller.guard.demo.model.contextBroker.Resource;

import java.util.ArrayList;

public class Agent {
    private String id;
    private String agent_catalog_id;
    private String exec_env_id;
    private String status;
    private ArrayList<Operations> operations;
    private String description;
    private String scStatus;
    private String pipelineUuid;

    public String getScStatus() {
        return scStatus;
    }

    public void setScStatus(String scStatus) {
        this.scStatus = scStatus;
    }

    public String getPipelineUuid() {
        return pipelineUuid;
    }

    public void setPipelineUuid(String pipelineUuid) {
        this.pipelineUuid = pipelineUuid;
    }

    @Override
    public String toString() {
        return "Agent{" +
                "id='" + id + '\'' +
                ", agent_catalog_id='" + agent_catalog_id + '\'' +
                ", exec_env_id='" + exec_env_id + '\'' +
                ", status='" + status + '\'' +
                ", operations=" + operations +
                ", description='" + description + '\'' +
                ", scStatus='" + scStatus + '\'' +
                ", pipelineUuid='" + pipelineUuid + '\'' +
                '}';
    }

    public String getId() {
        return id;
    }

    public void setId(String id) {
        this.id = id;
    }

    public String getAgent_catalog_id() {
        return agent_catalog_id;
    }

    public void setAgent_catalog_id(String agent_catalog_id) {
        this.agent_catalog_id = agent_catalog_id;
    }

    public String getExec_env_id() {
        return exec_env_id;
    }

    public void setExec_env_id(String exec_env_id) {
        this.exec_env_id = exec_env_id;
    }

    public String getStatus() {
        return status;
    }

    public void setStatus(String status) {
        this.status = status;
    }

    public String getDescription() {
        return description;
    }

    public void setDescription(String description) {
        this.description = description;
    }

    public ArrayList<Operations> getOperations() {
        return operations;
    }

    public void setOperations(ArrayList<Operations> operations) {
        this.operations = operations;
    }

    public ContextBrokerUpdateAgentResponse sendUpdateRequestToContextBroker() {

        // construct the contextBrokerUpdateAgentMessage object
        ContextBrokerUpdateAgentMessage contextBrokerUpdateAgentMessage = new ContextBrokerUpdateAgentMessage();
        contextBrokerUpdateAgentMessage.setId(this.id);
        contextBrokerUpdateAgentMessage.setOperations(this.operations);

        // call the updateAgentInstance with the constructed object
        ContextBrokerUpdateAgentResponse response = ContextBrokerHandler2.updateAgentInstance(contextBrokerUpdateAgentMessage);
        response.setSubjectUuid(this.agent_catalog_id);
        return response;
    }
}

/*public class Agent {
    private String id;
    private String agent_catalog_id;
    private String exec_env_id;
    private String status;
    private Operations operations;
    private String description;
    private String scStatus;
    private String pipelineUuid;

    public String getScStatus() {
        return scStatus;
    }

    public void setScStatus(String scStatus) {
        this.scStatus = scStatus;
    }

    public String getPipelineUuid() {
        return pipelineUuid;
    }

    public void setPipelineUuid(String pipelineUuid) {
        this.pipelineUuid = pipelineUuid;
    }

    @Override
    public String toString() {
        return "Agent{" +
                "id='" + id + '\'' +
                ", agent_catalog_id='" + agent_catalog_id + '\'' +
                ", exec_env_id='" + exec_env_id + '\'' +
                ", status='" + status + '\'' +
                ", operations=" + operations +
                ", description='" + description + '\'' +
                ", scStatus='" + scStatus + '\'' +
                ", pipelineUuid='" + pipelineUuid + '\'' +
                '}';
    }

    public String getId() {
        return id;
    }

    public void setId(String id) {
        this.id = id;
    }

    public String getAgent_catalog_id() {
        return agent_catalog_id;
    }

    public void setAgent_catalog_id(String agent_catalog_id) {
        this.agent_catalog_id = agent_catalog_id;
    }

    public String getExec_env_id() {
        return exec_env_id;
    }

    public void setExec_env_id(String exec_env_id) {
        this.exec_env_id = exec_env_id;
    }

    public String getStatus() {
        return status;
    }

    public void setStatus(String status) {
        this.status = status;
    }

    public String getDescription() {
        return description;
    }

    public void setDescription(String description) {
        this.description = description;
    }

    public Operations getOperations() {
        return operations;
    }

    public void setOperations(Operations operations) {
        this.operations = operations;
    }

    public ContextBrokerUpdateAgentResponse sendUpdateRequestToContextBroker() {

        // construct the contextBrokerUpdateAgentMessage object
        ContextBrokerUpdateAgentMessage contextBrokerUpdateAgentMessage = new ContextBrokerUpdateAgentMessage();
        contextBrokerUpdateAgentMessage.setId(this.id);
        contextBrokerUpdateAgentMessage.setOperations(this.operations);

        // call the updateAgentInstance with the constructed object
        ContextBrokerUpdateAgentResponse response = ContextBrokerHandler2.updateAgentInstance(contextBrokerUpdateAgentMessage);
        response.setSubjectUuid(this.agent_catalog_id);
        return response;
    }
}*/
