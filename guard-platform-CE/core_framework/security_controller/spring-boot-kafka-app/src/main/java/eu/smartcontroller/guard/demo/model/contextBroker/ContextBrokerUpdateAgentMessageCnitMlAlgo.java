package eu.smartcontroller.guard.demo.model.contextBroker;

import java.util.ArrayList;

public class ContextBrokerUpdateAgentMessageCnitMlAlgo {
    private String id;
    private ArrayList<OperationsCnitMlAlgo> operations;

    public String getId() {
        return id;
    }

    public void setId(String id) {
        this.id = id;
    }

    public ArrayList<OperationsCnitMlAlgo> getOperations() {
        return operations;
    }

    public void setOperations(ArrayList<OperationsCnitMlAlgo> operations) {
        this.operations = operations;
    }

    @Override
    public String toString() {
        return "ContextBrokerUpdateAgentMessage{" +
                "id='" + id + '\'' +
                ", operations=" + operations +
                '}';
    }
}
