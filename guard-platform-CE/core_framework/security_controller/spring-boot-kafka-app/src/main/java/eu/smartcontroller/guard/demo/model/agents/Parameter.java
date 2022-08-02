package eu.smartcontroller.guard.demo.model.agents;

public class Parameter {

    private Config config;
    private String description;
    private String id;
    private String input;
    private String type;

    public Config getConfig() {
        return config;
    }

    public void setConfig(Config config) {
        this.config = config;
    }

    public String getDescription() {
        return description;
    }

    public void setDescription(String description) {
        this.description = description;
    }

    public String getId() {
        return id;
    }

    public void setId(String id) {
        this.id = id;
    }

    public String getInput() {
        return input;
    }

    public void setInput(String input) {
        this.input = input;
    }

    public String getType() {
        return type;
    }

    public void setType(String type) {
        this.type = type;
    }

    @Override
    public String toString() {
        return "Parameter{" +
                "config=" + config +
                ", description='" + description + '\'' +
                ", id='" + id + '\'' +
                ", input='" + input + '\'' +
                ", type='" + type + '\'' +
                '}';
    }
}
