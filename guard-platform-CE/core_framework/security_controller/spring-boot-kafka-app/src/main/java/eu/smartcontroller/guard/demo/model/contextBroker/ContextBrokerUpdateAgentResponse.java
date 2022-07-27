package eu.smartcontroller.guard.demo.model.contextBroker;

public class ContextBrokerUpdateAgentResponse {

    private String subjectUuid;
    private int code;
    private boolean error;
    private String message;
    private String status;
    private Lcp_response lcp_response;

    public Lcp_response getLcp_response() {
        return lcp_response;
    }

    public void setLcp_response(Lcp_response lcp_response) {
        this.lcp_response = lcp_response;
    }

    public String getSubjectUuid() {
        return subjectUuid;
    }

    public void setSubjectUuid(String subjectUuid) {
        this.subjectUuid = subjectUuid;
    }

    public int getCode() {
        return code;
    }

    public void setCode(int code) {
        this.code = code;
    }

    public boolean isError() {
        return error;
    }

    public void setError(boolean error) {
        this.error = error;
    }

    public String getMessage() {
        return message;
    }

    public void setMessage(String message) {
        this.message = message;
    }

    public String getStatus() {
        return status;
    }

    public void setStatus(String status) {
        this.status = status;
    }

    @Override
    public String toString() {
        return "ContextBrokerUpdateAgentResponse{" +
                "subjectUuid='" + subjectUuid + '\'' +
                ", code=" + code +
                ", error=" + error +
                ", message='" + message + '\'' +
                ", status='" + status + '\'' +
                ", lcp_response=" + lcp_response +
                '}';
    }
}
