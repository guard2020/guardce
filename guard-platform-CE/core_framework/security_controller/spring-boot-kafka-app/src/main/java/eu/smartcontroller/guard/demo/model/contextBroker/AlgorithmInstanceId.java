package eu.smartcontroller.guard.demo.model.contextBroker;

public class AlgorithmInstanceId {
    private String id;

    public String getId() {
        return id;
    }

    public void setId(String id) {
        this.id = id;
    }

    @Override
    public String toString() {
        return "AlgorithmInstanceId{" +
                "id='" + id + '\'' +
                '}';
    }
}
