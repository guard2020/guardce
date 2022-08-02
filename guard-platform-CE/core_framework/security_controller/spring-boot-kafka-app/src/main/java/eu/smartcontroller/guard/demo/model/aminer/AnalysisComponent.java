package eu.smartcontroller.guard.demo.model.aminer;

import com.fasterxml.jackson.annotation.JsonProperty;

import java.util.ArrayList;

public class AnalysisComponent {
    @JsonProperty("AnalysisComponentIdentifier")
    public int analysisComponentIdentifier;
    @JsonProperty("AnalysisComponentType")
    public String analysisComponentType;
    @JsonProperty("AnalysisComponentName")
    public String analysisComponentName;
    @JsonProperty("Message")
    public String message;
    @JsonProperty("PersistenceFileName")
    public String persistenceFileName;
    @JsonProperty("TrainingMode")
    public boolean trainingMode;
    @JsonProperty("AffectedLogAtomPaths")
    public ArrayList<String> affectedLogAtomPaths;
    @JsonProperty("AffectedLogAtomValues")
    public ArrayList<String> affectedLogAtomValues;
    @JsonProperty("CriticalValue")
    public double criticalValue;
    @JsonProperty("ProbabilityThreshold")
    public double probabilityThreshold;

    public int getAnalysisComponentIdentifier() {
        return analysisComponentIdentifier;
    }

    public void setAnalysisComponentIdentifier(int analysisComponentIdentifier) {
        this.analysisComponentIdentifier = analysisComponentIdentifier;
    }

    public String getAnalysisComponentType() {
        return analysisComponentType;
    }

    public void setAnalysisComponentType(String analysisComponentType) {
        this.analysisComponentType = analysisComponentType;
    }

    public String getAnalysisComponentName() {
        return analysisComponentName;
    }

    public void setAnalysisComponentName(String analysisComponentName) {
        this.analysisComponentName = analysisComponentName;
    }

    public String getMessage() {
        return message;
    }

    public void setMessage(String message) {
        this.message = message;
    }

    public String getPersistenceFileName() {
        return persistenceFileName;
    }

    public void setPersistenceFileName(String persistenceFileName) {
        this.persistenceFileName = persistenceFileName;
    }

    public boolean isTrainingMode() {
        return trainingMode;
    }

    public void setTrainingMode(boolean trainingMode) {
        this.trainingMode = trainingMode;
    }

    public ArrayList<String> getAffectedLogAtomPaths() {
        return affectedLogAtomPaths;
    }

    public void setAffectedLogAtomPaths(ArrayList<String> affectedLogAtomPaths) {
        this.affectedLogAtomPaths = affectedLogAtomPaths;
    }

    public ArrayList<String> getAffectedLogAtomValues() {
        return affectedLogAtomValues;
    }

    public void setAffectedLogAtomValues(ArrayList<String> affectedLogAtomValues) {
        this.affectedLogAtomValues = affectedLogAtomValues;
    }

    public double getCriticalValue() {
        return criticalValue;
    }

    public void setCriticalValue(double criticalValue) {
        this.criticalValue = criticalValue;
    }

    public double getProbabilityThreshold() {
        return probabilityThreshold;
    }

    public void setProbabilityThreshold(double probabilityThreshold) {
        this.probabilityThreshold = probabilityThreshold;
    }

    @Override
    public String toString() {
        return "AnalysisComponent{" +
                "analysisComponentIdentifier=" + analysisComponentIdentifier +
                ", analysisComponentType='" + analysisComponentType + '\'' +
                ", analysisComponentName='" + analysisComponentName + '\'' +
                ", message='" + message + '\'' +
                ", persistenceFileName='" + persistenceFileName + '\'' +
                ", trainingMode=" + trainingMode +
                ", affectedLogAtomPaths=" + affectedLogAtomPaths +
                ", affectedLogAtomValues=" + affectedLogAtomValues +
                ", criticalValue=" + criticalValue +
                ", probabilityThreshold=" + probabilityThreshold +
                '}';
    }
}
