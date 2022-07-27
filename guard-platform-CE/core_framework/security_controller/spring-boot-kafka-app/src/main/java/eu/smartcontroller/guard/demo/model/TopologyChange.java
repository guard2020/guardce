package eu.smartcontroller.guard.demo.model;

public class TopologyChange {
    private String node;
    private String modificationType;
    private String nodeData;

    public TopologyChange(String node, String modificationType, String nodeData) {
        this.node = node;
        this.modificationType = modificationType;
        this.nodeData = nodeData;
    }

    public String getNode() {
        return node;
    }

    public void setNode(String node) {
        this.node = node;
    }

    public String getModificationType() {
        return modificationType;
    }

    public void setModificationType(String modificationType) {
        this.modificationType = modificationType;
    }

    public String getNodeData() {
        return nodeData;
    }

    public void setNodeData(String nodeData) {
        this.nodeData = nodeData;
    }

    @Override
    public String toString() {
        return "TopologyChange{" +
                "node='" + node + '\'' +
                ", modificationType='" + modificationType + '\'' +
                ", nodeData='" + nodeData + '\'' +
                '}';
    }
}
