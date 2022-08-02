package eu.smartcontroller.guard.demo.model.securityPolicies.kafkamessage2;

import com.fasterxml.jackson.annotation.JsonProperty;

public class Message {
    @JsonProperty("OpenVAS")
    public OpenVAS openVAS;
    public W3af w3af;
    public String task_name;
}
