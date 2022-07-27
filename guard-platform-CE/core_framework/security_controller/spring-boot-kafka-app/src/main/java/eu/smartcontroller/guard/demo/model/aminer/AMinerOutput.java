package eu.smartcontroller.guard.demo.model.aminer;

import com.fasterxml.jackson.annotation.JsonProperty;
import eu.smartcontroller.guard.demo.controller.ContextBrokerHandler2;

public class AMinerOutput {
    @JsonProperty("AnalysisComponent")
    private AnalysisComponent AnalysisComponent;
    @JsonProperty("LogData")
    private LogData LogData;

    private String unblock;

    @Override
    public String toString() {
        return "AMinerOutput{" +
                "AnalysisComponent=" + AnalysisComponent +
                ", LogData=" + LogData +
                ", unblock='" + unblock + '\'' +
                '}';
    }

    public String getUnblock() {
        return unblock;
    }

    public void setUnblock(String unblock) {
        this.unblock = unblock;
    }

    public eu.smartcontroller.guard.demo.model.aminer.AnalysisComponent getAnalysisComponent() {
        return AnalysisComponent;
    }

    public void setAnalysisComponent(eu.smartcontroller.guard.demo.model.aminer.AnalysisComponent analysisComponent) {
        AnalysisComponent = analysisComponent;
    }

    public eu.smartcontroller.guard.demo.model.aminer.LogData getLogData() {
        return LogData;
    }

    public void setLogData(eu.smartcontroller.guard.demo.model.aminer.LogData logData) {
        LogData = logData;
    }

    public boolean contains(String str) {
        /*if (this.AnalysisComponent!=null) {
            if (this.AnalysisComponent.analysisComponentType.equals(str)) {
                return false;
            } else {
                return true;
            }
        }*/
        if (this.AnalysisComponent!=null && this.LogData!=null) {
            if (this.LogData.annotatedMatchElement != null) {
                if (this.LogData.annotatedMatchElement.getParserLogHttpHost() != null) {
                    return false;
                } else {
                    return true;
                }
            }
        }
        return true;
    }

    public String blockIpRequest(String agentId) {

        String ipAddress = this.getLogData().getAnnotatedMatchElement().getParserLogHttpHost();

        String messageBody = "{\n" +
                "    \"id\": \"" + agentId + "\", \n" +
                "    \"operations\": [\n" +
                "    {\n" +
                "        \"actions\": [\n" +
                "        {\n" +
                "            \"id\": \"block\",\n" +
                "            \"ip\": \"" + ipAddress + "\" \n" +
                "        }\n" +
                "        ]\n" +
                "    }\n" +
                "    ]\n" +
                "}";

        return ContextBrokerHandler2.updatefirewallRulesAminer(agentId, messageBody);
    }

    public String unBlockIpRequest(String agentId) {

        String ipAddress = this.getLogData().getAnnotatedMatchElement().getParserLogHttpHost();

        String messageBody = "{\n" +
                "    \"id\": \""+ agentId+ "\",\n" +
                "    \"operations\": [\n" +
                "    {\n" +
                "        \"actions\": [\n" +
                "        {\n" +
                "            \"id\": \"unblock\",\n" +
                "            \"ip\": \"" + ipAddress + "\"\n" +
                "        }\n" +
                "        ]\n" +
                "    }\n" +
                "    ]\n" +
                "}";

        return ContextBrokerHandler2.updatefirewallRulesAminer(agentId, messageBody);
    }
}
