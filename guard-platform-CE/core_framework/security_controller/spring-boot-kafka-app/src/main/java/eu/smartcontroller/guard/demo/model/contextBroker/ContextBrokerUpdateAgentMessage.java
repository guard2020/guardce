package eu.smartcontroller.guard.demo.model.contextBroker;

import java.util.ArrayList;

public class ContextBrokerUpdateAgentMessage {
    private String id;
    private ArrayList<Operations> operations;

    public String getId() {
        return id;
    }

    public void setId(String id) {
        this.id = id;
    }

    public ArrayList<Operations> getOperations() {
        return operations;
    }

    public void setOperations(ArrayList<Operations> operations) {
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

/*public class ContextBrokerUpdateAgentMessage {
    private String id;
    private Operations operations;

    public String getId() {
        return id;
    }

    public void setId(String id) {
        this.id = id;
    }

    public Operations getOperations() {
        return operations;
    }

    public void setOperations(Operations operations) {
        this.operations = operations;
    }

    @Override
    public String toString() {
        return "ContextBrokerUpdateAgentMessage{" +
                "id='" + id + '\'' +
                ", operations=" + operations +
                '}';
    }
}*/
