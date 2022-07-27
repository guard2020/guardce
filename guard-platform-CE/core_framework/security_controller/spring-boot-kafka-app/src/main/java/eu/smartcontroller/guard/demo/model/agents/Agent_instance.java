package eu.smartcontroller.guard.demo.model.agents;

public class Agent_instance {

    private String agent_instance_id;

    public String getAgent_instance_id() {
        return agent_instance_id;
    }

    public void setAgent_instance_id(String agent_instance_id) {
        this.agent_instance_id = agent_instance_id;
    }

    @Override
    public String toString() {
        return "Agent_instance{" +
                "agent_instance_id='" + agent_instance_id + '\'' +
                '}';
    }
}
