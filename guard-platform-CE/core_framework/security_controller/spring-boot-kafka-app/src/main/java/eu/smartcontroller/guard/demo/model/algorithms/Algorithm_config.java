package eu.smartcontroller.guard.demo.model.algorithms;

import java.util.ArrayList;

public class Algorithm_config {

    private String algorithm_instance_id;
    private ArrayList<Parameter> parameters;
    private ArrayList<Resource> resources;

    public String getAlgorithm_instance_id() {
        return algorithm_instance_id;
    }

    public void setAlgorithm_instance_id(String algorithm_instance_id) {
        this.algorithm_instance_id = algorithm_instance_id;
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
        return "Algorithm_config{" +
                "algorithm_instance_id='" + algorithm_instance_id + '\'' +
                ", parameters=" + parameters +
                ", resources=" + resources +
                '}';
    }
}
