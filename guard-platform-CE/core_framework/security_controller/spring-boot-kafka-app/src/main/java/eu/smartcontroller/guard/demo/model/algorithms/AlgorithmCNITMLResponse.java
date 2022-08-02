package eu.smartcontroller.guard.demo.model.algorithms;

import java.time.LocalDateTime;
import java.util.List;

public class AlgorithmCNITMLResponse {

    private String subjectUuid;
    private Boolean error;
    private String stdout;
    private String stderr;
    private Integer returncode;
    private String start;
    private String end;

    @Override
    public String toString() {
        return "AlgorithmCNITMLResponse{" +
                "subjectUuid='" + subjectUuid + '\'' +
                ", error=" + error +
                /*", stdout='" + stdout + '\'' +
                ", stderr='" + stderr + '\'' +
                ", returncode=" + returncode +
                ", start='" + start + '\'' +
                ", end='" + end + '\'' +*/
                '}';
    }

    public String getSubjectUuid() {
        return subjectUuid;
    }

    public void setSubjectUuid(String subjectUuid) {
        this.subjectUuid = subjectUuid;
    }

    public Boolean getError() {
        return error;
    }

    public void setError(Boolean error) {
        this.error = error;
    }

    public String getStdout() {
        return stdout;
    }

    public void setStdout(String stdout) {
        this.stdout = stdout;
    }

    public String getStderr() {
        return stderr;
    }

    public void setStderr(String stderr) {
        this.stderr = stderr;
    }

    public Integer getReturncode() {
        return returncode;
    }

    public void setReturncode(Integer returncode) {
        this.returncode = returncode;
    }

    public String getStart() {
        return start;
    }

    public void setStart(String start) {
        this.start = start;
    }

    public String getEnd() {
        return end;
    }

    public void setEnd(String end) {
        this.end = end;
    }
}
