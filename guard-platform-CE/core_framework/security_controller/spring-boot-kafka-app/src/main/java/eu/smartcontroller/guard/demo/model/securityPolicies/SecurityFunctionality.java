package eu.smartcontroller.guard.demo.model.securityPolicies;

public class SecurityFunctionality {

    private String id;
    private String value;
    private String scope;

    public String getId() {
        return id;
    }

    public void setId(String id) {
        this.id = id;
    }

    public String getValue() {
        return value;
    }

    public void setValue(String value) {
        this.value = value;
    }

    public String getScope() {
        return scope;
    }

    public void setScope(String scope) {
        this.scope = scope;
    }

    @Override
    public String toString() {
        return "SecurityFunctionality{" +
                "id='" + id + '\'' +
                ", value='" + value + '\'' +
                ", scope='" + scope + '\'' +
                '}';
    }
}
