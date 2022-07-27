package eu.smartcontroller.guard.demo.model;

import java.util.ArrayList;

public class PipelineArray {

    private ArrayList<Pipeline3> pipelines = new ArrayList<>();

    public ArrayList<Pipeline3> getPipelines() {
        return pipelines;
    }

    public void setPipelines(ArrayList<Pipeline3> pipelines) {
        this.pipelines = pipelines;
    }

    @Override
    public String toString() {
        return "PipelineArray{" +
                "pipelines=" + pipelines +
                '}';
    }

    public boolean addNewPipeline(Pipeline3 pipeline) {
        return pipelines.add(pipeline);
    }

    public boolean removePipeline(Pipeline3 pipeline) {
        return pipelines.remove(pipeline);
    }

    public String removePipeline(String pipelineId) {
        for (Pipeline3 value : pipelines) {
            if (value.getId().equals(pipelineId)) {
                pipelines.remove(value);
                return "Pipeline removed from internal array.";
            }
        }
        return "Pipeline does not exists in internal array.";
    }

    public boolean pipelineExistsInArray(Pipeline2 pipeline) {
        return pipelines.contains(pipeline);
    }

    public int pipelineExistsInArray(String pipelineId) {
        for (int i = 0; i < pipelines.size(); i++) {
            if (pipelines.get(i).getId().equals(pipelineId)){
                return i;
            }
        }
        return -1;
    }

    public String getPipelineStatus(String pipelineId) {
        for (Pipeline3 value : pipelines) {
            if (value.getId().equals(pipelineId)) {
                return value.getStatus();
            }
        }
        return "The pipeline does not exist in the internal array.";
    }

    public String updatePipelineStatus(String pipelineId, String pipelineStatus) {
        for (Pipeline3 value : pipelines) {
            if (value.getId().equals(pipelineId)) {
                value.setStatus(pipelineStatus);
                return "The pipeline" + pipelineId + "'s status is updated in the internal array.";
            }
        }
        return "The pipeline" + pipelineId + " does not exist in the internal array.";
    }
}
