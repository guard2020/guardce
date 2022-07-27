package eu.smartcontroller.guard.demo.model.contextBroker;

public class Data {
    private String cmd;

    public String getCmd() {
        return cmd;
    }

    public void setCmd(String cmd) {
        this.cmd = cmd;
    }

    @Override
    public String toString() {
        return "Data{" +
                "cmd='" + cmd + '\'' +
                '}';
    }
}
