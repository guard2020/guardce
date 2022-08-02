package eu.smartcontroller.guard.demo.model.contextBroker;

import com.google.gson.JsonArray;
import com.google.gson.JsonObject;

public class Parameter {
    private String id;
    private String value;
    private JsonArray hosts;
    private JsonArray rules;

    ////////////////////////////////
    public JsonArray getHosts() {
        return hosts;
    }

    public void setHosts(JsonArray hosts) {
        this.hosts = hosts;
    }
    ////////////////////////////////
    public JsonArray getRules() {
        return rules;
    }

    public void setRules(JsonArray rules) {
        this.rules = rules;
    }
    ////////////////////////////////

    public String getId() {
        return id;
    }

    public void setId(String id) {
        this.id = id;
    }

    public String getValue() {
        return value;
    }

    public void setValue(String value) {
        this.value = value;
    }

    @Override
    public String toString() {
        return "Parameter{" +
                "id='" + id + '\'' +
                ", value='" + value + '\'' +
                '}';
    }
}
