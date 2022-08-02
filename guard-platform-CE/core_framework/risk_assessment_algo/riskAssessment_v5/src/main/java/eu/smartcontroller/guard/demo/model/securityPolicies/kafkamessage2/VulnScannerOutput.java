package eu.smartcontroller.guard.demo.model.securityPolicies.kafkamessage2;

import com.fasterxml.jackson.annotation.JsonProperty;

import java.util.Date;

public class VulnScannerOutput {
    @JsonProperty("Timestamp")
    public Date timestamp;
    @JsonProperty("Topic")
    public String topic;
    @JsonProperty("Partition")
    public int partition;
    @JsonProperty("Offset")
    public int offset;
    @JsonProperty("SchemaId")
    public Object schemaId;
    @JsonProperty("SchemaType")
    public Object schemaType;
    @JsonProperty("Key")
    public Object key;
    @JsonProperty("Headers")
    public Object headers;
    @JsonProperty("Message")
    public Message message;
    @JsonProperty("Error")
    public Object error;
}
