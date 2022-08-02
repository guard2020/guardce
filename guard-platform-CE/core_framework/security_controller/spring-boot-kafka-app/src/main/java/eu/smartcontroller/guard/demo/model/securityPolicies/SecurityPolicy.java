package eu.smartcontroller.guard.demo.model.securityPolicies;

import java.util.ArrayList;

public class SecurityPolicy {

    private ArrayList<Algo5Configuration> algo5Configurations;

    public ArrayList<Algo5Configuration> getAlgo5Configurations() {
        return algo5Configurations;
    }

    public void setAlgo5Configurations(ArrayList<Algo5Configuration> algo5Configurations) {
        this.algo5Configurations = algo5Configurations;
    }

    @Override
    public String toString() {
        return "SecurityPolicy{" +
                "algo5Configurations=" + algo5Configurations +
                '}';
    }
}
