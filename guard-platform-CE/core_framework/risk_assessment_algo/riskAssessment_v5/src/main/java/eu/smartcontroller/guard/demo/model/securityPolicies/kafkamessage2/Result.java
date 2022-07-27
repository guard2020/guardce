package eu.smartcontroller.guard.demo.model.securityPolicies.kafkamessage2;

import com.fasterxml.jackson.annotation.JsonProperty;

import java.util.ArrayList;

public class Result {
    public String name;
    public String threat;
    public String severity;
    public String description;
    public String nvt_family;
    public String cvss_base;
    public String cve;
    public int qod;
    public String qod_type;
    public Attributes attributes;
    public Object cwe_ids;
    public Object cwe_urls;
    public String desc;
    public Object fix_effort;
    public Object fix_guidance;
    public ArrayList<String> highlight;
    public String href;
    public int id;
    public Object long_description;
    public Object owasp_top_10_references;
    public String plugin_name;
    public Object references;
    public ArrayList<Integer> response_ids;
    public Object tags;
    public ArrayList<String> traffic_hrefs;
    public String uniq_id;
    public String url;
    public ArrayList<String> urls;
    @JsonProperty("var")
    public Object myvar;
    public Object vulndb_id;
    public Object wasc_ids;
    public Object wasc_urls;
}
