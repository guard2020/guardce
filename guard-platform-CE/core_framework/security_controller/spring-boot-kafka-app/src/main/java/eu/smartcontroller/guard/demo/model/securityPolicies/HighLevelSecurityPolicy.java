package eu.smartcontroller.guard.demo.model.securityPolicies;

import java.util.ArrayList;

public class HighLevelSecurityPolicy {

    private String policyId;
    private ArrayList<SecurityFunctionality> securityFunctionalities;

    public String getPolicyId() {
        return policyId;
    }

    public void setPolicyId(String policyId) {
        this.policyId = policyId;
    }

    public ArrayList<SecurityFunctionality> getSecurityFunctionalities() {
        return securityFunctionalities;
    }

    public void setSecurityFunctionalities(ArrayList<SecurityFunctionality> securityFunctionalities) {
        this.securityFunctionalities = securityFunctionalities;
    }

    @Override
    public String toString() {
        return "HighLevelSecurityPolicy{" +
                "policyId='" + policyId + '\'' +
                ", securityFunctionalities=" + securityFunctionalities +
                '}';
    }
}
