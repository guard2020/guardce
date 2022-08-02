package eu.smartcontroller.guard.demo.controller;

import com.google.gson.*;
import eu.smartcontroller.guard.demo.model.contextBroker.*;
import org.apache.http.HttpResponse;
import org.apache.http.client.HttpClient;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.client.methods.HttpPut;
import org.apache.http.entity.StringEntity;
import org.apache.http.impl.client.HttpClientBuilder;

import java.io.*;
import java.net.URL;
import java.util.ArrayList;

import static eu.smartcontroller.guard.demo.SpringBootKafkaAppApplication.contextBrokerManagerEndpoint;

public class ContextBrokerHandler2 {

    private static HttpClient client = HttpClientBuilder.create().build();

    public static ContextBrokerUpdateAgentResponse updateAgentInstance(ContextBrokerUpdateAgentMessage contextBrokerUpdateAgentMessage) {

        ContextBrokerUpdateAgentResponse contextBrokerUpdateAgentResponse = new ContextBrokerUpdateAgentResponse();
        String contextBrokerResponse ="";
        Boolean connectivity=false;
        String agentInstanceId = contextBrokerUpdateAgentMessage.getId();

        // read contextBrokerManagerEndpoint from the corresponding env-variable
        contextBrokerManagerEndpoint=System.getenv("contextBrokerManagerEndpoint");
        if (contextBrokerManagerEndpoint==null) {
            contextBrokerManagerEndpoint="10.0.0.7:5000";
        }
        KafkaProducerController.logger.info("contextBrokerManagerEndpoint: " + contextBrokerManagerEndpoint);

        try {
            // create the request
            URL url = new URL("http://" + contextBrokerManagerEndpoint + "/instance/agent/" + agentInstanceId);
            HttpPut request = new HttpPut(String.valueOf(url));
            request.setHeader("Content-Type", "application/json");
            request.setHeader("Accept", "application/json");

            request.setHeader("Authorization", "GUARD eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyLCJleHAiOjE3MDc0Mjg1OTN9.8DTKhcSqOLc7GVEfbe66u70nBwgh4p9JpBjuTWruCys");

            Gson gsonBuilder = new GsonBuilder().create();
            String jsonFromPojo = gsonBuilder.toJson(contextBrokerUpdateAgentMessage);

            System.out.println("jsonFromPojo: " + jsonFromPojo);

            // Splitting the parameters, actions and resources in two objects inside the list of operations for correct execution order.
            int position = jsonFromPojo.indexOf("\"actions");
            jsonFromPojo = addChar(jsonFromPojo, '{', position);
            jsonFromPojo = addChar(jsonFromPojo, '}', position-1);
            position = jsonFromPojo.indexOf("\"resources");
            jsonFromPojo = addChar(jsonFromPojo, '{', position);
            jsonFromPojo = addChar(jsonFromPojo, '}', position-1);

            System.out.println("jsonFromPojo: " + jsonFromPojo);

            StringEntity entity = new StringEntity(jsonFromPojo);
            request.setEntity(entity);

            System.out.println(request.toString());

            HttpResponse response = client.execute(request);

            // get the response
            try(BufferedReader rd = new BufferedReader(new InputStreamReader(response.getEntity().getContent()))) {
                String line;
                StringBuilder outcome= new StringBuilder();
                while ((line = rd.readLine()) != null) {
                    outcome.append(line);
                }
                contextBrokerResponse = outcome.toString();

                System.out.println("!!!!!!!!!!contextBrokerResponse: " + contextBrokerResponse);
                connectivity = true;
            }

        } catch (IOException e) {
            KafkaProducerController.logger.info(e.getMessage() + " during request to CB update agent instance API.");
            e.printStackTrace();
        }

        // read the contextBrokerResponse as JsonArray and construct the contextBrokerUpdateAgentResponse instance
        JsonParser jsonParser = new JsonParser();
        JsonArray jsonFromString;
        //JsonObject jsonFromString;
        if(connectivity) {
            if (!jsonParser.parse(contextBrokerResponse).isJsonNull()) {
                jsonFromString = jsonParser.parse(contextBrokerResponse).getAsJsonArray();
                //jsonFromString = jsonParser.parse(contextBrokerResponse).getAsJsonObject();

/*                //Iterating the contents of the array
                Iterator<JsonElement> iterator = jsonFromString.iterator();
                while(iterator.hasNext()) {
                    System.out.println(iterator.next());
                }*/

                contextBrokerUpdateAgentResponse.setCode(jsonFromString.get(0).getAsJsonObject().get("code").getAsInt());
                contextBrokerUpdateAgentResponse.setError(jsonFromString.get(0).getAsJsonObject().get("error").getAsBoolean());
                contextBrokerUpdateAgentResponse.setMessage(jsonFromString.get(0).getAsJsonObject().get("message").getAsString());
                contextBrokerUpdateAgentResponse.setStatus(jsonFromString.get(0).getAsJsonObject().get("status").getAsString());

                //contextBrokerUpdateAgentResponse.setCode(jsonFromString.getAsJsonObject().get("code").getAsInt());
                //contextBrokerUpdateAgentResponse.setError(jsonFromString.getAsJsonObject().get("error").getAsBoolean());
                //contextBrokerUpdateAgentResponse.setStatus(jsonFromString.getAsJsonObject().get("status").getAsString());
                /*if (jsonFromString.getAsJsonObject().get("code").getAsString().equals("200") ||
                        jsonFromString.getAsJsonObject().get("code").getAsString().equals("304")) {
                    contextBrokerUpdateAgentResponse.setMessage(jsonFromString.getAsJsonObject().get("message").getAsString());
                } else {
                    contextBrokerUpdateAgentResponse.setMessage(jsonFromString.getAsJsonObject().get("message").getAsJsonObject().toString());
                }*/

                if (jsonFromString.get(0).getAsJsonObject().get("code").getAsString().equals("200") ||
                        jsonFromString.get(0).getAsJsonObject().get("code").getAsString().equals("304")) {
                    contextBrokerUpdateAgentResponse.setMessage(jsonFromString.get(0).getAsJsonObject().get("message").getAsString());
                } else {
                    contextBrokerUpdateAgentResponse.setMessage(jsonFromString.get(0).getAsJsonObject().get("message").getAsJsonObject().toString());
                }
            }
        }

        //KafkaProducerController.logger.info("Request to CB update agent instance API. Response: " + contextBrokerResponse);
        return contextBrokerUpdateAgentResponse;

    }

    public static ContextBrokerUpdateAgentResponse updateAgentInstance2(ContextBrokerUpdateAgentMessage contextBrokerUpdateAgentMessage) {

        ContextBrokerUpdateAgentResponse contextBrokerUpdateAgentResponse = new ContextBrokerUpdateAgentResponse();
        String contextBrokerResponse ="";
        Boolean connectivity=false;
        String agentInstanceId = contextBrokerUpdateAgentMessage.getId();

        // read contextBrokerManagerEndpoint from the corresponding env-variable
        contextBrokerManagerEndpoint=System.getenv("contextBrokerManagerEndpoint");
        if (contextBrokerManagerEndpoint==null) {
            contextBrokerManagerEndpoint="10.0.0.7:5000";
        }
        //KafkaProducerController.logger.info("contextBrokerManagerEndpoint: " + contextBrokerManagerEndpoint);

        try {
            // create the request
            URL url = new URL("http://" + contextBrokerManagerEndpoint + "/instance/agent/" + agentInstanceId);
            HttpPut request = new HttpPut(String.valueOf(url));
            request.setHeader("Content-Type", "application/json");
            request.setHeader("Accept", "application/json");
            request.setHeader("Authorization", "GUARD eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyLCJleHAiOjE3MDc0Mjg1OTN9.8DTKhcSqOLc7GVEfbe66u70nBwgh4p9JpBjuTWruCys");

            Gson gsonBuilder = new GsonBuilder().create();
            String jsonFromPojo = gsonBuilder.toJson(contextBrokerUpdateAgentMessage);
            System.out.println("jsonFromPojo: " + jsonFromPojo);

            StringEntity entity = new StringEntity(jsonFromPojo);
            request.setEntity(entity);
            System.out.println(request.toString());

            HttpResponse response = client.execute(request);

            // get the response
            try(BufferedReader rd = new BufferedReader(new InputStreamReader(response.getEntity().getContent()))) {
                String line;
                StringBuilder outcome= new StringBuilder();
                while ((line = rd.readLine()) != null) {
                    outcome.append(line);
                }
                contextBrokerResponse = outcome.toString();

                System.out.println("!!!!!!!!!!contextBrokerResponse: " + contextBrokerResponse);
                connectivity = true;
            }

        } catch (IOException e) {
            KafkaProducerController.logger.info(e.getMessage() + " during request to CB update agent instance API.");
            e.printStackTrace();
        }

        // construct the return value
        JsonParser jsonParser = new JsonParser();
        JsonArray jsonFromString;
        if(connectivity) {
            if (!jsonParser.parse(contextBrokerResponse).isJsonNull()) {
                jsonFromString = jsonParser.parse(contextBrokerResponse).getAsJsonArray();

                contextBrokerUpdateAgentResponse.setCode(jsonFromString.get(0).getAsJsonObject().get("code").getAsInt());
                contextBrokerUpdateAgentResponse.setError(jsonFromString.get(0).getAsJsonObject().get("error").getAsBoolean());
                contextBrokerUpdateAgentResponse.setMessage(jsonFromString.get(0).getAsJsonObject().get("message").getAsString());
                contextBrokerUpdateAgentResponse.setStatus(jsonFromString.get(0).getAsJsonObject().get("status").getAsString());

                if (jsonFromString.get(0).getAsJsonObject().get("code").getAsString().equals("200") ||
                        jsonFromString.get(0).getAsJsonObject().get("code").getAsString().equals("304")) {
                    contextBrokerUpdateAgentResponse.setMessage(jsonFromString.get(0).getAsJsonObject().get("message").getAsString());
                } else {
                    contextBrokerUpdateAgentResponse.setMessage(jsonFromString.get(0).getAsJsonObject().get("message").getAsJsonObject().toString());
                }
            }
        }

        return contextBrokerUpdateAgentResponse;

    }

    private static String addChar(String str, char ch, int position) {
        return str.substring(0, position) + ch + str.substring(position);
    }

    public static String updatefirewallRulesCnit2(String agentId, String requestMessage) {

        String contextBrokerResponse ="";

        System.out.println(requestMessage);

        // read contextBrokerManagerEndpoint from the corresponding env-variable
        contextBrokerManagerEndpoint=System.getenv("contextBrokerManagerEndpoint");
        if (contextBrokerManagerEndpoint==null) {
            contextBrokerManagerEndpoint="10.0.0.7:5000";
        }
        KafkaProducerController.logger.info("contextBrokerManagerEndpoint: " + contextBrokerManagerEndpoint);

        try {
            // create the request
            URL url = new URL("http://" + contextBrokerManagerEndpoint + "/instance/agent/" + agentId);
            HttpPut request = new HttpPut(String.valueOf(url));
            request.setHeader("Content-Type", "application/json");
            request.setHeader("Authorization", "GUARD eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyLCJleHAiOjE3MDc0Mjg1OTN9.8DTKhcSqOLc7GVEfbe66u70nBwgh4p9JpBjuTWruCys");

            StringEntity entity = new StringEntity(requestMessage);
            request.setEntity(entity);
            System.out.println(request.toString());
            HttpResponse response = client.execute(request);

            System.out.println(response.getEntity().getContent());

            // get the response
            try(BufferedReader rd = new BufferedReader(new InputStreamReader(response.getEntity().getContent()))) {
                String line;
                StringBuilder outcome= new StringBuilder();
                while ((line = rd.readLine()) != null) {
                    outcome.append(line);
                }
                contextBrokerResponse = outcome.toString();

                System.out.println("!!!!!!!!!!contextBrokerResponse: " + contextBrokerResponse);
            }

        } catch (IOException e) {
            KafkaProducerController.logger.info(e.getMessage() + " during request to CB update agent instance API.");
            e.printStackTrace();
        }

        return contextBrokerResponse;
    }

    public static String updatefirewallRulesAminer(String agentId, String requestMessage){
        String contextBrokerResponse ="";

        //System.out.println(requestMessage);

        // read contextBrokerManagerEndpoint from the corresponding env-variable
        contextBrokerManagerEndpoint=System.getenv("contextBrokerManagerEndpoint");
        if (contextBrokerManagerEndpoint==null) {
            contextBrokerManagerEndpoint="10.0.0.7:5000";
        }
        KafkaProducerController.logger.info("contextBrokerManagerEndpoint: " + contextBrokerManagerEndpoint);

        try {
            // create the request
            URL url = new URL("http://" + contextBrokerManagerEndpoint + "/instance/agent/" + agentId);
            HttpPut request = new HttpPut(String.valueOf(url));
            request.setHeader("Content-Type", "application/json");
            request.setHeader("Authorization", "GUARD eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyLCJleHAiOjE3MDc0Mjg1OTN9.8DTKhcSqOLc7GVEfbe66u70nBwgh4p9JpBjuTWruCys");

            StringEntity entity = new StringEntity(requestMessage);
            System.out.println(requestMessage);
            request.setEntity(entity);
            System.out.println(request.toString());
            HttpResponse response = client.execute(request);

            System.out.println(response.getEntity().getContent());

            // get the response
            try(BufferedReader rd = new BufferedReader(new InputStreamReader(response.getEntity().getContent()))) {
                String line;
                StringBuilder outcome= new StringBuilder();
                while ((line = rd.readLine()) != null) {
                    outcome.append(line);
                }
                contextBrokerResponse = outcome.toString();

                System.out.println("!!!!!!!!!!contextBrokerResponse: " + contextBrokerResponse);
            }

        } catch (IOException e) {
            KafkaProducerController.logger.info(e.getMessage() + " during request to CB update agent instance API.");
            e.printStackTrace();
        }

        return contextBrokerResponse;
    }

    public static JsonArray getAlgorithmInstanceScriptName(String algorithmCatalogId) {

        String contextBrokerResponse ="";
        Boolean connectivity=false;

        try {
            // create the request
            URL url = new URL("http://" + contextBrokerManagerEndpoint + "/catalog/algorithm/" + algorithmCatalogId);
            HttpGet request = new HttpGet(String.valueOf(url));

            request.setHeader("Content-Type", "application/json");
            request.setHeader("Authorization", "GUARD eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyLCJleHAiOjE3MDc0Mjg1OTN9.8DTKhcSqOLc7GVEfbe66u70nBwgh4p9JpBjuTWruCys");

            System.out.println(request);
            HttpResponse response = client.execute(request);

            // get the response
            try(BufferedReader rd = new BufferedReader(new InputStreamReader(response.getEntity().getContent()))) {
                String line;
                StringBuilder outcome= new StringBuilder();
                while ((line = rd.readLine()) != null) {
                    outcome.append(line);
                }
                contextBrokerResponse = outcome.toString();

                System.out.println("!!!!!!!!!!contextBrokerResponse: " + contextBrokerResponse);
                connectivity = true;
            }

        } catch (IOException e) {
            KafkaProducerController.logger.info(e.getMessage() + " during request to CB get algorithm instance script name API.");
            e.printStackTrace();
        }

        // read the contextBrokerResponse as JsonArray and construct the ContextBrokergetAlgorithmInstanceResponse instance
        JsonParser jsonParser = new JsonParser();

        if(connectivity) {
            if (!jsonParser.parse(contextBrokerResponse).isJsonNull()) {
                return jsonParser.parse(contextBrokerResponse).getAsJsonArray();
            }
        }

        return null;
    }

}