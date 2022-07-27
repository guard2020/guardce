package eu.smartcontroller.guard.demo.model.aminer;

import com.fasterxml.jackson.annotation.JsonProperty;

import java.util.Date;

public class AnnotatedMatchElement {
    @JsonProperty("/parser/0")
    public String parser0;
    @JsonProperty("/parser/1")
    public String parser1;
    @JsonProperty("/parser/kubernetes/0")
    public String parserKubernetes0;
    @JsonProperty("/parser/kubernetes/1")
    public String parserKubernetes1;
    @JsonProperty("/parser/kubernetes/2")
    public String parserKubernetes2;
    @JsonProperty("/parser/kubernetes/3")
    public String parserKubernetes3;
    @JsonProperty("/parser/kubernetes/4")
    public String parserKubernetes4;
    @JsonProperty("/parser/kubernetes/5")
    public String parserKubernetes5;
    @JsonProperty("/parser/kubernetes/6")
    public String parserKubernetes6;
    @JsonProperty("/parser/kubernetes/7")
    public String parserKubernetes7;
    @JsonProperty("/parser/kubernetes/labels/0")
    public String parserKubernetesLabels0;
    @JsonProperty("/parser/kubernetes/labels/1")
    public String parserKubernetesLabels1;
    @JsonProperty("/parser/kubernetes/labels/2")
    public String parserKubernetesLabels2;
    @JsonProperty("/parser/kubernetes/labels/3")
    public String parserKubernetesLabels3;
    @JsonProperty("/parser/kubernetes/labels/4")
    public String parserKubernetesLabels4;
    @JsonProperty("/parser/kubernetes/labels/5")
    public String parserKubernetesLabels5;
    @JsonProperty("/parser/kubernetes/labels/6")
    public String parserKubernetesLabels6;
    @JsonProperty("/parser/kubernetes/labels/7")
    public String parserKubernetesLabels7;
    @JsonProperty("/parser/kubernetes/labels/8")
    public String parserKubernetesLabels8;
    @JsonProperty("/parser/kubernetes/labels/9")
    public String parserKubernetesLabels9;
    @JsonProperty("/parser/kubernetes/labels/10")
    public String parserKubernetesLabels10;
    @JsonProperty("/parser/kubernetes/labels/11")
    public String parserKubernetesLabels11;
    @JsonProperty("/parser/kubernetes/annotations/0")
    public String parserKubernetesAnnotations0;
    @JsonProperty("/parser/kubernetes/annotations/1")
    public Date parserKubernetesAnnotations1;
    @JsonProperty("/parser/kubernetes/annotations/2")
    public String parserKubernetesAnnotations2;
    @JsonProperty("/parser/kubernetes/annotations/3")
    public String parserKubernetesAnnotations3;
    @JsonProperty("/parser/kubernetes/annotations/4")
    public String parserKubernetesAnnotations4;
    @JsonProperty("/parser/kubernetes/annotations/5")
    public String parserKubernetesAnnotations5;
    @JsonProperty("/parser/floatts/floatts")
    public String parserFloattsFloatts;
    @JsonProperty("/parser/log/http")
    public String parserLogHttp;
    @JsonProperty("/parser/log/http/stuff/0")
    public String parserLogHttpStuff0;
    @JsonProperty("/parser/log/http/stuff/1")
    public String parserLogHttpStuff1;
    @JsonProperty("/parser/log/http/stuff/2")
    public String parserLogHttpStuff2;
    @JsonProperty("/parser/log/http/stuff/3")
    public String parserLogHttpStuff3;
    @JsonProperty("/parser/log/http/stuff/4")
    public String parserLogHttpStuff4;
    @JsonProperty("/parser/log/http/stuff/5")
    public String parserLogHttpStuff5;
    @JsonProperty("/parser/log/http/stuff/6")
    public String parserLogHttpStuff6;
    @JsonProperty("/parser/log/http/stuff/7")
    public String parserLogHttpStuff7;
    @JsonProperty("/parser/log/http/stuff/8")
    public String parserLogHttpStuff8;
    @JsonProperty("/parser/log/http/stuff/9")
    public String parserLogHttpStuff9;
    @JsonProperty("/parser/log/http/ts")
    public String parserLogHttpTs;
    @JsonProperty("/parser/log/http/method")
    public String parserLogHttpMethod;
    @JsonProperty("/parser/log/http/request/api")
    public String parserLogHttpRequestApi;
    @JsonProperty("/parser/log/http/space")
    public String parserLogHttpSpace;
    @JsonProperty("/parser/log/http/quotes")
    public String parserLogHttpQuotes;
    @JsonProperty("/parser/log/http/host")
    public String parserLogHttpHost;
    @JsonProperty("/parser/log/http/http_remainder")
    public String parserLogHttpHttpRemainder;
    @JsonProperty("/parser/log/http/request/api/apistr")
    public String parserLogHttpRequestApiApistr;
    @JsonProperty("/parser/log/http/request/api/request")
    public String parserLogHttpRequestApiRequest;
    @JsonProperty("/parser/log/http/request/api/limit")
    public String parserLogHttpRequestApiLimit;

    @Override
    public String toString() {
        return "AnnotatedMatchElement{" +
                "parser0='" + parser0 + '\'' +
                ", parser1='" + parser1 + '\'' +
                ", parserKubernetes0='" + parserKubernetes0 + '\'' +
                ", parserKubernetes1='" + parserKubernetes1 + '\'' +
                ", parserKubernetes2='" + parserKubernetes2 + '\'' +
                ", parserKubernetes3='" + parserKubernetes3 + '\'' +
                ", parserKubernetes4='" + parserKubernetes4 + '\'' +
                ", parserKubernetes5='" + parserKubernetes5 + '\'' +
                ", parserKubernetes6='" + parserKubernetes6 + '\'' +
                ", parserKubernetes7='" + parserKubernetes7 + '\'' +
                ", parserKubernetesLabels0='" + parserKubernetesLabels0 + '\'' +
                ", parserKubernetesLabels1='" + parserKubernetesLabels1 + '\'' +
                ", parserKubernetesLabels2='" + parserKubernetesLabels2 + '\'' +
                ", parserKubernetesLabels3='" + parserKubernetesLabels3 + '\'' +
                ", parserKubernetesLabels4='" + parserKubernetesLabels4 + '\'' +
                ", parserKubernetesLabels5='" + parserKubernetesLabels5 + '\'' +
                ", parserKubernetesLabels6='" + parserKubernetesLabels6 + '\'' +
                ", parserKubernetesLabels7='" + parserKubernetesLabels7 + '\'' +
                ", parserKubernetesLabels8='" + parserKubernetesLabels8 + '\'' +
                ", parserKubernetesLabels9='" + parserKubernetesLabels9 + '\'' +
                ", parserKubernetesLabels10='" + parserKubernetesLabels10 + '\'' +
                ", parserKubernetesLabels11='" + parserKubernetesLabels11 + '\'' +
                ", parserKubernetesAnnotations0='" + parserKubernetesAnnotations0 + '\'' +
                ", parserKubernetesAnnotations1=" + parserKubernetesAnnotations1 +
                ", parserKubernetesAnnotations2='" + parserKubernetesAnnotations2 + '\'' +
                ", parserKubernetesAnnotations3='" + parserKubernetesAnnotations3 + '\'' +
                ", parserKubernetesAnnotations4='" + parserKubernetesAnnotations4 + '\'' +
                ", parserKubernetesAnnotations5='" + parserKubernetesAnnotations5 + '\'' +
                ", parserFloattsFloatts='" + parserFloattsFloatts + '\'' +
                ", parserLogHttp='" + parserLogHttp + '\'' +
                ", parserLogHttpStuff0='" + parserLogHttpStuff0 + '\'' +
                ", parserLogHttpStuff1='" + parserLogHttpStuff1 + '\'' +
                ", parserLogHttpStuff2='" + parserLogHttpStuff2 + '\'' +
                ", parserLogHttpStuff3='" + parserLogHttpStuff3 + '\'' +
                ", parserLogHttpStuff4='" + parserLogHttpStuff4 + '\'' +
                ", parserLogHttpStuff5='" + parserLogHttpStuff5 + '\'' +
                ", parserLogHttpStuff6='" + parserLogHttpStuff6 + '\'' +
                ", parserLogHttpStuff7='" + parserLogHttpStuff7 + '\'' +
                ", parserLogHttpStuff8='" + parserLogHttpStuff8 + '\'' +
                ", parserLogHttpStuff9='" + parserLogHttpStuff9 + '\'' +
                ", parserLogHttpTs='" + parserLogHttpTs + '\'' +
                ", parserLogHttpMethod='" + parserLogHttpMethod + '\'' +
                ", parserLogHttpRequestApi='" + parserLogHttpRequestApi + '\'' +
                ", parserLogHttpSpace='" + parserLogHttpSpace + '\'' +
                ", parserLogHttpQuotes='" + parserLogHttpQuotes + '\'' +
                ", parserLogHttpHost='" + parserLogHttpHost + '\'' +
                ", parserLogHttpHttpRemainder='" + parserLogHttpHttpRemainder + '\'' +
                ", parserLogHttpRequestApiApistr='" + parserLogHttpRequestApiApistr + '\'' +
                ", parserLogHttpRequestApiRequest='" + parserLogHttpRequestApiRequest + '\'' +
                ", parserLogHttpRequestApiLimit='" + parserLogHttpRequestApiLimit + '\'' +
                '}';
    }

    public String getParser0() {
        return parser0;
    }

    public void setParser0(String parser0) {
        this.parser0 = parser0;
    }

    public String getParser1() {
        return parser1;
    }

    public void setParser1(String parser1) {
        this.parser1 = parser1;
    }

    public String getParserKubernetes0() {
        return parserKubernetes0;
    }

    public void setParserKubernetes0(String parserKubernetes0) {
        this.parserKubernetes0 = parserKubernetes0;
    }

    public String getParserKubernetes1() {
        return parserKubernetes1;
    }

    public void setParserKubernetes1(String parserKubernetes1) {
        this.parserKubernetes1 = parserKubernetes1;
    }

    public String getParserKubernetes2() {
        return parserKubernetes2;
    }

    public void setParserKubernetes2(String parserKubernetes2) {
        this.parserKubernetes2 = parserKubernetes2;
    }

    public String getParserKubernetes3() {
        return parserKubernetes3;
    }

    public void setParserKubernetes3(String parserKubernetes3) {
        this.parserKubernetes3 = parserKubernetes3;
    }

    public String getParserKubernetes4() {
        return parserKubernetes4;
    }

    public void setParserKubernetes4(String parserKubernetes4) {
        this.parserKubernetes4 = parserKubernetes4;
    }

    public String getParserKubernetes5() {
        return parserKubernetes5;
    }

    public void setParserKubernetes5(String parserKubernetes5) {
        this.parserKubernetes5 = parserKubernetes5;
    }

    public String getParserKubernetes6() {
        return parserKubernetes6;
    }

    public void setParserKubernetes6(String parserKubernetes6) {
        this.parserKubernetes6 = parserKubernetes6;
    }

    public String getParserKubernetes7() {
        return parserKubernetes7;
    }

    public void setParserKubernetes7(String parserKubernetes7) {
        this.parserKubernetes7 = parserKubernetes7;
    }

    public String getParserKubernetesLabels0() {
        return parserKubernetesLabels0;
    }

    public void setParserKubernetesLabels0(String parserKubernetesLabels0) {
        this.parserKubernetesLabels0 = parserKubernetesLabels0;
    }

    public String getParserKubernetesLabels1() {
        return parserKubernetesLabels1;
    }

    public void setParserKubernetesLabels1(String parserKubernetesLabels1) {
        this.parserKubernetesLabels1 = parserKubernetesLabels1;
    }

    public String getParserKubernetesLabels2() {
        return parserKubernetesLabels2;
    }

    public void setParserKubernetesLabels2(String parserKubernetesLabels2) {
        this.parserKubernetesLabels2 = parserKubernetesLabels2;
    }

    public String getParserKubernetesLabels3() {
        return parserKubernetesLabels3;
    }

    public void setParserKubernetesLabels3(String parserKubernetesLabels3) {
        this.parserKubernetesLabels3 = parserKubernetesLabels3;
    }

    public String getParserKubernetesLabels4() {
        return parserKubernetesLabels4;
    }

    public void setParserKubernetesLabels4(String parserKubernetesLabels4) {
        this.parserKubernetesLabels4 = parserKubernetesLabels4;
    }

    public String getParserKubernetesLabels5() {
        return parserKubernetesLabels5;
    }

    public void setParserKubernetesLabels5(String parserKubernetesLabels5) {
        this.parserKubernetesLabels5 = parserKubernetesLabels5;
    }

    public String getParserKubernetesLabels6() {
        return parserKubernetesLabels6;
    }

    public void setParserKubernetesLabels6(String parserKubernetesLabels6) {
        this.parserKubernetesLabels6 = parserKubernetesLabels6;
    }

    public String getParserKubernetesLabels7() {
        return parserKubernetesLabels7;
    }

    public void setParserKubernetesLabels7(String parserKubernetesLabels7) {
        this.parserKubernetesLabels7 = parserKubernetesLabels7;
    }

    public String getParserKubernetesLabels8() {
        return parserKubernetesLabels8;
    }

    public void setParserKubernetesLabels8(String parserKubernetesLabels8) {
        this.parserKubernetesLabels8 = parserKubernetesLabels8;
    }

    public String getParserKubernetesLabels9() {
        return parserKubernetesLabels9;
    }

    public void setParserKubernetesLabels9(String parserKubernetesLabels9) {
        this.parserKubernetesLabels9 = parserKubernetesLabels9;
    }

    public String getParserKubernetesLabels10() {
        return parserKubernetesLabels10;
    }

    public void setParserKubernetesLabels10(String parserKubernetesLabels10) {
        this.parserKubernetesLabels10 = parserKubernetesLabels10;
    }

    public String getParserKubernetesLabels11() {
        return parserKubernetesLabels11;
    }

    public void setParserKubernetesLabels11(String parserKubernetesLabels11) {
        this.parserKubernetesLabels11 = parserKubernetesLabels11;
    }

    public String getParserKubernetesAnnotations0() {
        return parserKubernetesAnnotations0;
    }

    public void setParserKubernetesAnnotations0(String parserKubernetesAnnotations0) {
        this.parserKubernetesAnnotations0 = parserKubernetesAnnotations0;
    }

    public Date getParserKubernetesAnnotations1() {
        return parserKubernetesAnnotations1;
    }

    public void setParserKubernetesAnnotations1(Date parserKubernetesAnnotations1) {
        this.parserKubernetesAnnotations1 = parserKubernetesAnnotations1;
    }

    public String getParserKubernetesAnnotations2() {
        return parserKubernetesAnnotations2;
    }

    public void setParserKubernetesAnnotations2(String parserKubernetesAnnotations2) {
        this.parserKubernetesAnnotations2 = parserKubernetesAnnotations2;
    }

    public String getParserKubernetesAnnotations3() {
        return parserKubernetesAnnotations3;
    }

    public void setParserKubernetesAnnotations3(String parserKubernetesAnnotations3) {
        this.parserKubernetesAnnotations3 = parserKubernetesAnnotations3;
    }

    public String getParserKubernetesAnnotations4() {
        return parserKubernetesAnnotations4;
    }

    public void setParserKubernetesAnnotations4(String parserKubernetesAnnotations4) {
        this.parserKubernetesAnnotations4 = parserKubernetesAnnotations4;
    }

    public String getParserKubernetesAnnotations5() {
        return parserKubernetesAnnotations5;
    }

    public void setParserKubernetesAnnotations5(String parserKubernetesAnnotations5) {
        this.parserKubernetesAnnotations5 = parserKubernetesAnnotations5;
    }

    public String getParserFloattsFloatts() {
        return parserFloattsFloatts;
    }

    public void setParserFloattsFloatts(String parserFloattsFloatts) {
        this.parserFloattsFloatts = parserFloattsFloatts;
    }

    public String getParserLogHttp() {
        return parserLogHttp;
    }

    public void setParserLogHttp(String parserLogHttp) {
        this.parserLogHttp = parserLogHttp;
    }

    public String getParserLogHttpStuff0() {
        return parserLogHttpStuff0;
    }

    public void setParserLogHttpStuff0(String parserLogHttpStuff0) {
        this.parserLogHttpStuff0 = parserLogHttpStuff0;
    }

    public String getParserLogHttpStuff1() {
        return parserLogHttpStuff1;
    }

    public void setParserLogHttpStuff1(String parserLogHttpStuff1) {
        this.parserLogHttpStuff1 = parserLogHttpStuff1;
    }

    public String getParserLogHttpStuff2() {
        return parserLogHttpStuff2;
    }

    public void setParserLogHttpStuff2(String parserLogHttpStuff2) {
        this.parserLogHttpStuff2 = parserLogHttpStuff2;
    }

    public String getParserLogHttpStuff3() {
        return parserLogHttpStuff3;
    }

    public void setParserLogHttpStuff3(String parserLogHttpStuff3) {
        this.parserLogHttpStuff3 = parserLogHttpStuff3;
    }

    public String getParserLogHttpStuff4() {
        return parserLogHttpStuff4;
    }

    public void setParserLogHttpStuff4(String parserLogHttpStuff4) {
        this.parserLogHttpStuff4 = parserLogHttpStuff4;
    }

    public String getParserLogHttpStuff5() {
        return parserLogHttpStuff5;
    }

    public void setParserLogHttpStuff5(String parserLogHttpStuff5) {
        this.parserLogHttpStuff5 = parserLogHttpStuff5;
    }

    public String getParserLogHttpStuff6() {
        return parserLogHttpStuff6;
    }

    public void setParserLogHttpStuff6(String parserLogHttpStuff6) {
        this.parserLogHttpStuff6 = parserLogHttpStuff6;
    }

    public String getParserLogHttpStuff7() {
        return parserLogHttpStuff7;
    }

    public void setParserLogHttpStuff7(String parserLogHttpStuff7) {
        this.parserLogHttpStuff7 = parserLogHttpStuff7;
    }

    public String getParserLogHttpStuff8() {
        return parserLogHttpStuff8;
    }

    public void setParserLogHttpStuff8(String parserLogHttpStuff8) {
        this.parserLogHttpStuff8 = parserLogHttpStuff8;
    }

    public String getParserLogHttpStuff9() {
        return parserLogHttpStuff9;
    }

    public void setParserLogHttpStuff9(String parserLogHttpStuff9) {
        this.parserLogHttpStuff9 = parserLogHttpStuff9;
    }

    public String getParserLogHttpTs() {
        return parserLogHttpTs;
    }

    public void setParserLogHttpTs(String parserLogHttpTs) {
        this.parserLogHttpTs = parserLogHttpTs;
    }

    public String getParserLogHttpMethod() {
        return parserLogHttpMethod;
    }

    public void setParserLogHttpMethod(String parserLogHttpMethod) {
        this.parserLogHttpMethod = parserLogHttpMethod;
    }

    public String getParserLogHttpRequestApi() {
        return parserLogHttpRequestApi;
    }

    public void setParserLogHttpRequestApi(String parserLogHttpRequestApi) {
        this.parserLogHttpRequestApi = parserLogHttpRequestApi;
    }

    public String getParserLogHttpSpace() {
        return parserLogHttpSpace;
    }

    public void setParserLogHttpSpace(String parserLogHttpSpace) {
        this.parserLogHttpSpace = parserLogHttpSpace;
    }

    public String getParserLogHttpQuotes() {
        return parserLogHttpQuotes;
    }

    public void setParserLogHttpQuotes(String parserLogHttpQuotes) {
        this.parserLogHttpQuotes = parserLogHttpQuotes;
    }

    public String getParserLogHttpHost() {
        return parserLogHttpHost;
    }

    public void setParserLogHttpHost(String parserLogHttpHost) {
        this.parserLogHttpHost = parserLogHttpHost;
    }

    public String getParserLogHttpHttpRemainder() {
        return parserLogHttpHttpRemainder;
    }

    public void setParserLogHttpHttpRemainder(String parserLogHttpHttpRemainder) {
        this.parserLogHttpHttpRemainder = parserLogHttpHttpRemainder;
    }

    public String getParserLogHttpRequestApiApistr() {
        return parserLogHttpRequestApiApistr;
    }

    public void setParserLogHttpRequestApiApistr(String parserLogHttpRequestApiApistr) {
        this.parserLogHttpRequestApiApistr = parserLogHttpRequestApiApistr;
    }

    public String getParserLogHttpRequestApiRequest() {
        return parserLogHttpRequestApiRequest;
    }

    public void setParserLogHttpRequestApiRequest(String parserLogHttpRequestApiRequest) {
        this.parserLogHttpRequestApiRequest = parserLogHttpRequestApiRequest;
    }

    public String getParserLogHttpRequestApiLimit() {
        return parserLogHttpRequestApiLimit;
    }

    public void setParserLogHttpRequestApiLimit(String parserLogHttpRequestApiLimit) {
        this.parserLogHttpRequestApiLimit = parserLogHttpRequestApiLimit;
    }
}
