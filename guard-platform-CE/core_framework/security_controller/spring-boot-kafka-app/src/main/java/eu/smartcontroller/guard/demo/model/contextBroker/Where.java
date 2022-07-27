package eu.smartcontroller.guard.demo.model.contextBroker;

public class Where {
    private Equals equals;

    public Equals getEquals() {
        return equals;
    }

    public void setEquals(Equals equals) {
        this.equals = equals;
    }

    @Override
    public String toString() {
        return "Where{" +
                "equals=" + equals +
                '}';
    }
}
