package eu.smartcontroller.guard.demo.model.algorithms;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;

public class AlgorithmInstance {
    private String id;
    private String service;
    private String kafkaTopic;
    private String scStatus;
    private String pipelineUuid;
    private String containerName;

    @Override
    public String toString() {
        return "AlgorithmInstance{" +
                "id='" + id + '\'' +
                ", service='" + service + '\'' +
                ", kafkaTopic='" + kafkaTopic + '\'' +
                ", scStatus='" + scStatus + '\'' +
                ", pipelineUuid='" + pipelineUuid + '\'' +
                ", containerName='" + containerName + '\'' +
                '}';
    }

    public String getContainerName() {
        return containerName;
    }

    public void setContainerName(String containerName) {
        this.containerName = containerName;
    }

    public String getId() {
        return id;
    }

    public void setId(String id) {
        this.id = id;
    }

    public String getService() {
        return service;
    }

    public void setService(String service) {
        this.service = service;
    }

    public String getKafkaTopic() {
        return kafkaTopic;
    }

    public void setKafkaTopic(String kafkaTopic) {
        this.kafkaTopic = kafkaTopic;
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

    public String executeScript(String service, String instanceName, String kafkaTopic, String operation, String containerName) {

        // use ProcessBuilder to execute external shell commands
        ProcessBuilder processBuilder = new ProcessBuilder();

        if (operation.equals("start")) {
            System.out.println("cd /home/smartcontroller/SmartController/algo/ ; ./" + service + " " + operation + " " + instanceName + " " + kafkaTopic);
            processBuilder.command("bash", "-c", "cd /home/smartcontroller/SmartController/algo/ ; ./" + service + " " + operation + " " + instanceName + " " + kafkaTopic);
        } else if (operation.equals("stop")) {
            System.out.println("cd /home/smartcontroller/SmartController/algo/ ; ./" + service + " " + operation + " " + containerName );
            processBuilder.command("bash", "-c", "cd /home/smartcontroller/SmartController/algo/ ; ./" + service + " " + operation + " " + containerName);
        }
        //processBuilder.command("cmd.exe", "/c", "cd");

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
                System.out.println("Success!");
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

        //todo
        // save the container name to the CB-MAnager

        return "response";
    }
}
