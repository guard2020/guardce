package eu.smartcontroller.guard.demo.model.securityPolicies.kafkamessage2;

import com.fasterxml.jackson.annotation.JsonProperty;

public class Report {
    @JsonProperty("guard-api")
    public GuardApi guardApi;
    public Orthanc orthanc;
}
