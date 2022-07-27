package eu.smartcontroller.guard.demo.model.contextBroker;

import com.google.gson.JsonArray;
import com.google.gson.JsonObject;

import java.util.ArrayList;

public class Operations {
    private ArrayList<Parameter> parameters;
    private ArrayList<Action> actions;
    private ArrayList<Resource> resources;

    public ArrayList<Parameter> getParameters() {
        return parameters;
    }

    public void setParameters(ArrayList<Parameter> parameters) {
        this.parameters = parameters;
    }

    public ArrayList<Action> getActions() {
        return actions;
    }

    public void setActions(ArrayList<Action> actions) {
        this.actions = actions;
    }

    public ArrayList<Resource> getResources() {
        return resources;
    }

    public void setResources(ArrayList<Resource> resources) {
        this.resources = resources;
    }

    @Override
    public String toString() {
        return "Operations{" +
                "parameters=" + parameters +
                ", actions=" + actions +
                ", resources=" + resources +
                '}';
    }
}
