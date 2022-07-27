package eu.smartcontroller.guard.demo.model.contextBroker;

import java.util.ArrayList;

public class ParameterCnitMlAlgo {
    private String id;
    private ArrayList<String> value;

    public String getId() {
        return id;
    }

    public void setId(String id) {
        this.id = id;
    }

    public ArrayList<String> getValue() {
        return value;
    }

    public void setValue(ArrayList<String> value) {
        this.value = value;
    }

    @Override
    public String toString() {
        return "ParameterCnitMlAlgo{" +
                "id='" + id + '\'' +
                ", value=" + value +
                '}';
    }
}
