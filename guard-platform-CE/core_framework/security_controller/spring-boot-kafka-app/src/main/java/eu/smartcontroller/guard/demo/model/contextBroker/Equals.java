package eu.smartcontroller.guard.demo.model.contextBroker;

public class Equals {
    private String target;
    private String expr;

    public String getTarget() {
        return target;
    }

    public void setTarget(String target) {
        this.target = target;
    }

    public String getExpr() {
        return expr;
    }

    public void setExpr(String expr) {
        this.expr = expr;
    }

    @Override
    public String toString() {
        return "Equals{" +
                "target='" + target + '\'' +
                ", expr='" + expr + '\'' +
                '}';
    }
}
