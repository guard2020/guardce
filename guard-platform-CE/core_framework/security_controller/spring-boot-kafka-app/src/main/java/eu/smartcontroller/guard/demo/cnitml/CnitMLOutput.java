package eu.smartcontroller.guard.demo.cnitml;

import com.fasterxml.jackson.annotation.JsonProperty;
import com.google.gson.JsonObject;
import eu.smartcontroller.guard.demo.controller.ContextBrokerHandler2;

public class CnitMLOutput {
    @JsonProperty("SOURCE")
    private String Source;
    @JsonProperty("SEVERITY")
    private String Severity;
    @JsonProperty("DESCRIPTION")
    private String Description;
    @JsonProperty("DATA")
    private Object Data;
    @JsonProperty("TIMESTAMP")
    private String Timestamp;

    private String unblock;

    @Override
    public String toString() {
        return "CnitMLOutput{" +
                "Source='" + Source + '\'' +
                ", Severity='" + Severity + '\'' +
                ", Description='" + Description + '\'' +
                ", Data=" + Data +
                ", Timestamp='" + Timestamp + '\'' +
                ", unblock='" + unblock + '\'' +
                '}';
    }

    public String getUnblock() {
        return unblock;
    }

    public void setUnblock(String unblock) {
        this.unblock = unblock;
    }

    public Object getData() {
        return Data;
    }

    public void setData(Object data) {
        Data = data;
    }

    public String getSource() {
        return Source;
    }

    public void setSource(String source) {
        Source = source;
    }

    public String getSeverity() {
        return Severity;
    }

    public void setSeverity(String severity) {
        Severity = severity;
    }

    public String getDescription() {
        return Description;
    }

    public void setDescription(String description) {
        Description = description;
    }

    public String getTimestamp() {
        return Timestamp;
    }

    public void setTimestamp(String timestamp) {
        Timestamp = timestamp;
    }

    public boolean contains(String str) {
        /*if (this.Data!=null) {
            if (this.Description.equals(str)) {
                return false;
            } else {
                return true;
            }
        }
        return true;
        */

        if (this.getSource().equals(str)) {
            return false;
        } else {
            return true;
        }

        /*if (this.Data!=null) {
                return false;
            } else {
                return true;
            }*/
    }

    public String blockIpRequest(String agentId) {

        String ipAddress = this.getData().toString().substring(1,this.getData().toString().indexOf("="));

        String messageBody = "{\n" +
                "  \"id\": \""+ agentId+ "\",\n" +
                "  \"operations\": [\n" +
                "    {\n" +
                "      \"parameters\": [\n" +
                "        {\n" +
                "          \"id\": \"firewall-rules\",\n" +
                "          \"value\": [\n" +
                "            \"sid:1|name:test1|saddr:" + ipAddress + "\"\n" +
                "          ]\n" +
                "        }\n" +
                "      ]\n" +
                "    },\n" +
                "    {\n" +
                "        \"actions\": [\n" +
                "            {\n" +
                "                \"id\": \"restart\"\n" +
                "            }\n" +
                "        ]\n" +
                "    }\n" +
                "  ]\n" +
                "}";

        return ContextBrokerHandler2.updatefirewallRulesCnit2(agentId, messageBody);
    }

    public String unBlockIpRequest(String agentId) {

        String ipAddress = this.getData().toString().substring(1,this.getData().toString().indexOf("="));

        String messageBody = "{\n" +
                "    \"id\": \""+ agentId+ "\",\n" +
                "    \"operations\": [\n" +
                "        {\n" +
                "            \"parameters\": [\n" +
                "                {\n" +
                "                    \"id\": \"firewall-rules\",\n" +
                "                    \"value\": [\n" +
                "                    ]\n" +
                "                }\n" +
                "            ]\n" +
                "        },\n" +
                "        {\n" +
                "            \"actions\": [\n" +
                "                {\n" +
                "                    \"id\": \"restart\",\n" +
                "                    \"output_format\": \"lines\"\n" +
                "                }\n" +
                "            ]\n" +
                "        }\n" +
                "    ]\n" +
                "}";

        return ContextBrokerHandler2.updatefirewallRulesCnit2(agentId, messageBody);
    }
}
