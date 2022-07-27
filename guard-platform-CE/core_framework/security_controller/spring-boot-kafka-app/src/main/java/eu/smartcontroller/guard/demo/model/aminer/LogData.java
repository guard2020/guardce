package eu.smartcontroller.guard.demo.model.aminer;

import com.fasterxml.jackson.annotation.JsonProperty;

import java.util.ArrayList;

public class LogData {
    @JsonProperty("RawLogData")
    public ArrayList<String> rawLogData;
    @JsonProperty("Timestamps")
    public ArrayList<Integer> timestamps;
    @JsonProperty("DetectionTimestamp")
    public double detectionTimestamp;
    @JsonProperty("LogLinesCount")
    public int logLinesCount;
    @JsonProperty("AnnotatedMatchElement")
    public AnnotatedMatchElement annotatedMatchElement;

    public ArrayList<String> getRawLogData() {
        return rawLogData;
    }

    public void setRawLogData(ArrayList<String> rawLogData) {
        this.rawLogData = rawLogData;
    }

    public ArrayList<Integer> getTimestamps() {
        return timestamps;
    }

    public void setTimestamps(ArrayList<Integer> timestamps) {
        this.timestamps = timestamps;
    }

    public double getDetectionTimestamp() {
        return detectionTimestamp;
    }

    public void setDetectionTimestamp(double detectionTimestamp) {
        this.detectionTimestamp = detectionTimestamp;
    }

    public int getLogLinesCount() {
        return logLinesCount;
    }

    public void setLogLinesCount(int logLinesCount) {
        this.logLinesCount = logLinesCount;
    }

    public AnnotatedMatchElement getAnnotatedMatchElement() {
        return annotatedMatchElement;
    }

    public void setAnnotatedMatchElement(AnnotatedMatchElement annotatedMatchElement) {
        this.annotatedMatchElement = annotatedMatchElement;
    }

    @Override
    public String toString() {
        return "LogData{" +
                "rawLogData=" + rawLogData +
                ", timestamps=" + timestamps +
                ", detectionTimestamp=" + detectionTimestamp +
                ", logLinesCount=" + logLinesCount +
                ", annotatedMatchElement=" + annotatedMatchElement +
                '}';
    }
}
