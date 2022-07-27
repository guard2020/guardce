package eu.smartcontroller.guard.demo.model;

import eu.smartcontroller.guard.demo.controller.ContextBrokerHandler2;
import eu.smartcontroller.guard.demo.model.contextBroker.*;

import java.util.ArrayList;

public class Rulefile2 {

    private String uuid;
    private String content;
    private String fireRules;

    public String getUuid() {
        return uuid;
    }

    public void setUuid(String uuid) {
        this.uuid = uuid;
    }

    public String getContent() {
        return content;
    }

    public void setContent(String content) {
        this.content = content;
    }

    public String getFireRules() {
        return fireRules;
    }

    public void setFireRules(String fireRules) {
        this.fireRules = fireRules;
    }

    @Override
    public String toString() {
        return "Rulefile2{" +
                "uuid='" + uuid + '\'' +
                ", content='" + content + '\'' +
                ", fireRules='" + fireRules + '\'' +
                '}';
    }

    // additional methods
    public ContextBrokerUpdateAgentResponse sendRulefilecontentToContextBroker(String agentId) {

        // construct the contextBrokerUpdateAgentMessage object
        ContextBrokerUpdateAgentMessage contextBrokerUpdateAgentMessage = new ContextBrokerUpdateAgentMessage();
        contextBrokerUpdateAgentMessage.setId(agentId);

        ArrayList<Action> actions = new ArrayList<>();
        Action action = new Action();
        action.setId("reload");
        actions.add(action);

        ArrayList<Resource> resources = new ArrayList<>();
        Resource resource = new Resource();
        resource.setContent(this.content);
        resource.setId("rule-file");
        resources.add(resource);

        ArrayList<Operations> operations = new ArrayList<>();
        Operations operation = new Operations();
        operation.setActions(actions);
        operation.setResources(resources);
        operations.add(operation);

        contextBrokerUpdateAgentMessage.setOperations(operations);

        //System.out.println("contextBrokerUpdateAgentMessage" + contextBrokerUpdateAgentMessage);

        // call the updateAgentInstance with the constructed object
        ContextBrokerUpdateAgentResponse response = ContextBrokerHandler2.updateAgentInstance2(contextBrokerUpdateAgentMessage);
        response.setSubjectUuid(this.uuid);
        return response;
    }
}
