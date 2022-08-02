package eu.smartcontroller.guard.demo.model.contextBroker;

import java.util.ArrayList;

public class OperationsCnitMlAlgo {
    private ArrayList<ParameterCnitMlAlgo> parameters;
    private ArrayList<Action> actions;
    private ArrayList<Resource> resources;

    public ArrayList<ParameterCnitMlAlgo> getParameters() {
        return parameters;
    }

    public void setParameters(ArrayList<ParameterCnitMlAlgo> parameters) {
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
