package eu.smartcontroller.guard.demo.model.agents;

import java.util.ArrayList;

public class Config {

    private ArrayList<String> path;
    private String schema;
    private String source;

    public ArrayList<String> getPath() {
        return path;
    }

    public void setPath(ArrayList<String> path) {
        this.path = path;
    }

    public String getSchema() {
        return schema;
    }

    public void setSchema(String schema) {
        this.schema = schema;
    }

    public String getSource() {
        return source;
    }

    public void setSource(String source) {
        this.source = source;
    }

    @Override
    public String toString() {
        return "Config{" +
                "path=" + path +
                ", schema='" + schema + '\'' +
                ", source='" + source + '\'' +
                '}';
    }
}
