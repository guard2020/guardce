package eu.smartcontroller.guard.demo.model.contextBroker;

public class ContextBrokerGetAlgorithmInstanceMessage {
    private String select;
    private Where where;

    public String getSelect() {
        return select;
    }

    public void setSelect(String select) {
        this.select = select;
    }

    public Where getWhere() {
        return where;
    }

    public void setWhere(Where where) {
        this.where = where;
    }

    @Override
    public String toString() {
        return "ContextBrokergetAlgorithmInstanceMessage{" +
                "select='" + select + '\'' +
                ", where=" + where +
                '}';
    }
}
