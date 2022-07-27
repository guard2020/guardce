package eu.smartcontroller.guard.demo.model.agents;

import java.util.ArrayList;

public class Agent_config2 {

    private String agent_instance_id;
    private ArrayList<Parameter> parameters;
    private ArrayList<Resource> resources;

    public String getAgent_instance_id() {
        return agent_instance_id;
    }

    public void setAgent_instance_id(String agent_instance_id) {
        this.agent_instance_id = agent_instance_id;
    }

    public ArrayList<Parameter> getParameters() {
        return parameters;
    }

    public void setParameters(ArrayList<Parameter> parameters) {
        this.parameters = parameters;
    }

    public ArrayList<Resource> getResources() {
        return resources;
    }

    public void setResources(ArrayList<Resource> resources) {
        this.resources = resources;
    }

    @Override
    public String toString() {
        return "Agent_config2{" +
                "agent_instance_id='" + agent_instance_id + '\'' +
                ", parameters=" + parameters +
                ", resources=" + resources +
                '}';
    }
}
