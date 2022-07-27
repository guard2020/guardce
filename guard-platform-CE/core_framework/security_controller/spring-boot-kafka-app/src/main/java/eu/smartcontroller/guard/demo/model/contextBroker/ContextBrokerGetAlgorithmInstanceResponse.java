package eu.smartcontroller.guard.demo.model.contextBroker;

import java.util.ArrayList;

public class ContextBrokerGetAlgorithmInstanceResponse {
    private ArrayList<AlgorithmInstanceId> algorithmInstanceIds;

    public ArrayList<AlgorithmInstanceId> getAlgorithmInstanceIds() {
        return algorithmInstanceIds;
    }

    public void setAlgorithmInstanceIds(ArrayList<AlgorithmInstanceId> algorithmInstanceIds) {
        this.algorithmInstanceIds = algorithmInstanceIds;
    }

    @Override
    public String toString() {
        return "ContextBrokergetAlgorithmInstanceResponse{" +
                "algorithmInstanceIds=" + algorithmInstanceIds +
                '}';
    }
}
