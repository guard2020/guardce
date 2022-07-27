package eu.smartcontroller.guard.demo.model.algorithms;

import eu.smartcontroller.guard.demo.controller.AlgorithmCNITMLHandler;
import eu.smartcontroller.guard.demo.controller.ContextBrokerHandler2;
import eu.smartcontroller.guard.demo.model.contextBroker.ContextBrokerUpdateAgentMessage;
import eu.smartcontroller.guard.demo.model.contextBroker.ContextBrokerUpdateAgentResponse;
import eu.smartcontroller.guard.demo.model.contextBroker.Operations;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.util.ArrayList;

public class Algorithm {
    private String id;
    private String status;
    private ArrayList<Operations> operations;
    private String description;

    private String scStatus;
    private String pipelineUuid;

    @Override
    public String toString() {
        return "Algorithm{" +
                "id='" + id + '\'' +
                ", status='" + status + '\'' +
                ", operations=" + operations +
                ", description='" + description + '\'' +
                ", scStatus='" + scStatus + '\'' +
                ", pipelineUuid='" + pipelineUuid + '\'' +
                '}';
    }

    public String getId() {
        return id;
    }

    public void setId(String id) {
        this.id = id;
    }

    public String getStatus() {
        return status;
    }

    public void setStatus(String status) {
        this.status = status;
    }

    public String getDescription() {
        return description;
    }

    public void setDescription(String description) {
        this.description = description;
    }

    public ArrayList<Operations> getOperations() {
        return operations;
    }

    public void setOperations(ArrayList<Operations> operations) {
        this.operations = operations;
    }

    public String getScStatus() {
        return scStatus;
    }

    public void setScStatus(String scStatus) {
        this.scStatus = scStatus;
    }

    public String getPipelineUuid() {
        return pipelineUuid;
    }

    public void setPipelineUuid(String pipelineUuid) {
        this.pipelineUuid = pipelineUuid;
    }

/*    public AlgorithmCNITMLResponse executeCommand() {

        AlgorithmCNITMLResponse response = AlgorithmCNITMLHandler.executeCommand(this.status);
        response.setSubjectUuid(this.id);
        return response;
    }*/

    public String executeCommand() {

        String algoId = this.getId();
        String action = this.getOperations().get(0).getActions().get(0).getId();

        // use ProcessBuilder to execute external shell commands
        ProcessBuilder processBuilder = new ProcessBuilder();
        //processBuilder.command("bash", "-c", "ls /home/");
        //processBuilder.command("cmd.exe", "/c", "dir C:\\Users\\manos");
        //processBuilder.command("cmd.exe", "/c", "cd");

        if (action.equals("start")){
            if (algoId.contains("cnit")) {
                processBuilder.command("bash", "-c", "docker-compose -f docker-compose_algo.yml up -d blockchain-connector");
                processBuilder.command("bash", "-c", "docker-compose -f docker-compose_algo.yml start blockchain-connector");
            } else if (algoId.contains("cnit")) {
                processBuilder.command("bash", "-c", "docker-compose -f docker-compose_algo.yml up -d algo1.2");
                processBuilder.command("bash", "-c", "docker-compose -f docker-compose_algo.yml start algo1.2");
            } else if (algoId.contains("cnit")) {
                processBuilder.command("bash", "-c", "docker-compose -f docker-compose_algo.yml up -d algo1.1.2");
                processBuilder.command("bash", "-c", "docker-compose -f docker-compose_algo.yml start algo1.1.2");
            } else if (algoId.contains("cnit")) {
                processBuilder.command("bash", "-c", "docker-compose -f docker-compose_algo.yml up -d algo5");
                processBuilder.command("bash", "-c", "docker-compose -f docker-compose_algo.yml start algo5");
            }
        } else {
            if (algoId.contains("cnit")) {
                processBuilder.command("bash", "-c", "docker-compose -f docker-compose_algo.yml stop blockchain-connector");
                processBuilder.command("bash", "-c", "docker-compose -f docker-compose_algo.yml down blockchain-connector");
            } else if (algoId.contains("cnit")) {
                processBuilder.command("bash", "-c", "docker-compose -f docker-compose_algo.yml stop algo1.2");
                processBuilder.command("bash", "-c", "docker-compose -f docker-compose_algo.yml down algo1.2");
            } else if (algoId.contains("cnit")) {
                processBuilder.command("bash", "-c", "docker-compose -f docker-compose_algo.yml stop algo1.1.2");
                processBuilder.command("bash", "-c", "docker-compose -f docker-compose_algo.yml down algo1.1.2");
            } else if (algoId.contains("cnit")) {
                processBuilder.command("bash", "-c", "docker-compose -f docker-compose_algo.yml stop algo5");
                processBuilder.command("bash", "-c", "docker-compose -f docker-compose_algo.yml down algo5");
            }
        }


        try {
            Process process = processBuilder.start();

            StringBuilder output = new StringBuilder();

            BufferedReader reader = new BufferedReader(
                    new InputStreamReader(process.getInputStream()));

            String line;
            while ((line = reader.readLine()) != null) {
                output.append(line + "\n");
            }

            int exitVal = process.waitFor();
            if (exitVal == 0) {
                //System.out.println("Success!");
                //System.out.println(output);
                //System.exit(0);
                return output.toString();
            } else {
                //abnormal...
                return "Command has not been executed";
            }
        } catch (IOException e) {
            e.printStackTrace();
        } catch (InterruptedException e) {
            e.printStackTrace();
        }

        return "executeCommand method";
    }

    public String updateParameters() {
        ArrayList<AlgorithmCNITMLResponse> responses = new ArrayList<>();
        AlgorithmCNITMLResponse response;
        response = new AlgorithmCNITMLResponse();
        for (int i = 0; i < this.getOperations().size(); i++) {
            for (int j = 0; j < this.getOperations().get(i).getParameters().size(); j++) {
                response = AlgorithmCNITMLHandler.updateParameters(this.getOperations().get(i).getParameters().get(j).getId(), this.getOperations().get(i).getParameters().get(j).getValue());
                response.setSubjectUuid(this.id);
                responses.add(response);
            }
        }
        return responses.toString();
    }

/*    public AlgorithmCNITMLResponse updateResources() {
        AlgorithmCNITMLResponse response = AlgorithmCNITMLHandler.updateResources(this.status);
        response.setSubjectUuid(this.id);
        return response;
    }*/
}
