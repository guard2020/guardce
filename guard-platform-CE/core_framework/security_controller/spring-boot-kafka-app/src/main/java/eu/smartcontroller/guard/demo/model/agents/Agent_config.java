package eu.smartcontroller.guard.demo.model.agents;

import java.util.ArrayList;

public class Agent_config {

    private String agent_catalog_id;
    private ArrayList<Parameter> parameters;

    public String getAgent_catalog_id() {
        return agent_catalog_id;
    }

    public void setAgent_catalog_id(String agent_catalog_id) {
        this.agent_catalog_id = agent_catalog_id;
    }

    public ArrayList<Parameter> getParameters() {
        return parameters;
    }

    public void setParameters(ArrayList<Parameter> parameters) {
        this.parameters = parameters;
    }

    @Override
    public String toString() {
        return "Agent_config{" +
                "agent_catalog_id='" + agent_catalog_id + '\'' +
                ", parameters=" + parameters +
                '}';
    }
}
