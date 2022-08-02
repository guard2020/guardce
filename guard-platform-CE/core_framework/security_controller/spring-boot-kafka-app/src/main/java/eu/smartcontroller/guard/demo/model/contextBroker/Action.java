package eu.smartcontroller.guard.demo.model.contextBroker;

public class Action {
    private String id;
    private String mode;

    public String getId() {
        return id;
    }

    public void setId(String id) {
        this.id = id;
    }

    public String getMode() {
        return mode;
    }

    public void setMode(String mode) {
        this.mode = mode;
    }

    @Override
    public String toString() {
        return "Action{" +
                "id='" + id + '\'' +
                ", mode='" + mode + '\'' +
                '}';
    }
}
