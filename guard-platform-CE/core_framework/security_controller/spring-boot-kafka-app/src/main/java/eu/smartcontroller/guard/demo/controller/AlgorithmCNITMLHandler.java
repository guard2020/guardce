package eu.smartcontroller.guard.demo.controller;

import com.google.gson.Gson;
import com.google.gson.JsonArray;
import com.google.gson.JsonObject;
import com.google.gson.JsonParser;
import eu.smartcontroller.guard.demo.model.algorithms.AlgorithmCNITMLResponse;
import org.apache.http.HttpResponse;
import org.apache.http.client.HttpClient;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.impl.client.HttpClientBuilder;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.net.URL;

import static eu.smartcontroller.guard.demo.SpringBootKafkaAppApplication.cnitMlAlgorithmEndpoint;

public class AlgorithmCNITMLHandler {

    private static HttpClient client = HttpClientBuilder.create().build();

    public static AlgorithmCNITMLResponse executeCommand(String commandId) {

        AlgorithmCNITMLResponse algorithmCNITMLResponse = new AlgorithmCNITMLResponse();
        String algorithmResponse ="";
        Boolean connectivity=false;

        try {
            // create the request
            URL url = new URL("http://" + cnitMlAlgorithmEndpoint + "/commands/" + commandId);
            HttpPost request = new HttpPost(String.valueOf(url));
            request.setHeader("Content-Type", "application/json");

            //System.out.println("algorithm request: " + request);

            HttpResponse response = client.execute(request);

            // get the response
            try(BufferedReader rd = new BufferedReader(new InputStreamReader(response.getEntity().getContent()))) {
                String line;
                StringBuilder outcome= new StringBuilder();
                while ((line = rd.readLine()) != null) {
                    outcome.append(line);
                }
                algorithmResponse = outcome.toString();

                //System.out.println("!!!!!!!!!!algorithmResponse (executeCommand): " + algorithmResponse);
                connectivity = true;
            }

        } catch (IOException e) {
            KafkaProducerController.logger.info(e.getMessage() + " during request to CNIT-ML algorithm instance API.");
            e.printStackTrace();
        }

        // read the algorithm Response as JsonArray and construct the algorithmCNITMLResponse instance
        JsonParser jsonParser = new JsonParser();
        //JsonArray jsonFromString;
        JsonObject jsonFromString;
        if(connectivity) {
            if (!jsonParser.parse(algorithmResponse).isJsonNull()) {
                //jsonFromString = jsonParser.parse(algorithmResponse).getAsJsonArray();
                jsonFromString = jsonParser.parse(algorithmResponse).getAsJsonObject();

                /*
                //Ο Μάνος μου το είπε αυτό αλλά για να το χρησιμοποιήσω πρέπει να φτιαχτούν σωστά οι τύποι στο AlgorithmCNITMLResponse
                Gson gson = new Gson();
                AlgorithmCNITMLResponse algorithmCNITMLResponse2 = gson.fromJson(algorithmResponse, AlgorithmCNITMLResponse.class);
                */

                /*
                //Iterating the contents of the array
                Iterator<JsonElement> iterator = jsonFromString.iterator();
                while(iterator.hasNext()) {
                    System.out.println(iterator.next());
                }
                */

                /*algorithmCNITMLResponse.setError(jsonFromString.get(0).getAsJsonObject().get("error").getAsBoolean());
                algorithmCNITMLResponse.setStdout(jsonFromString.get(0).getAsJsonObject().get("stdout").getAsJsonArray().get(0).getAsString());
                algorithmCNITMLResponse.setStderr(jsonFromString.get(0).getAsJsonObject().get("stderr").getAsJsonArray().get(0).getAsString());
                algorithmCNITMLResponse.setReturncode(jsonFromString.get(0).getAsJsonObject().get("returncode").getAsInt());
                algorithmCNITMLResponse.setStart(jsonFromString.get(0).getAsJsonObject().get("start").getAsString());
                algorithmCNITMLResponse.setEnd(jsonFromString.get(0).getAsJsonObject().get("end").getAsString());*/

                algorithmCNITMLResponse.setError(jsonFromString.getAsJsonObject().get("error").getAsBoolean());
                /*algorithmCNITMLResponse.setStdout(jsonFromString.getAsJsonObject().get("stdout").getAsJsonArray().get(0).getAsString());
                algorithmCNITMLResponse.setStderr(jsonFromString.getAsJsonObject().get("stderr").getAsJsonArray().get(0).getAsString());
                algorithmCNITMLResponse.setReturncode(jsonFromString.getAsJsonObject().get("returncode").getAsInt());
                algorithmCNITMLResponse.setStart(jsonFromString.getAsJsonObject().get("start").getAsString());
                algorithmCNITMLResponse.setEnd(jsonFromString.getAsJsonObject().get("end").getAsString());*/

            }
        }

        KafkaProducerController.logger.info("Request to CNIT-ML algorithm instance API (executeCommand). Response: " + algorithmCNITMLResponse);
        return algorithmCNITMLResponse;
    }

    public static AlgorithmCNITMLResponse updateParameters(String id, String value) {

        AlgorithmCNITMLResponse algorithmCNITMLResponse = new AlgorithmCNITMLResponse();
        String algorithmResponse ="";
        Boolean connectivity=false;

        try {
            // create the request
            URL url = new URL("http://" + cnitMlAlgorithmEndpoint + "/parameters/" + id + "/" + value);
            HttpPost request = new HttpPost(String.valueOf(url));
            request.setHeader("Content-Type", "application/json");

            //System.out.println("algorithm request2: " + request);

            HttpResponse response = client.execute(request);

            // get the response
            try(BufferedReader rd = new BufferedReader(new InputStreamReader(response.getEntity().getContent()))) {
                String line;
                StringBuilder outcome= new StringBuilder();
                while ((line = rd.readLine()) != null) {
                    outcome.append(line);
                }
                algorithmResponse = outcome.toString();

                //System.out.println("!!!!!!!!!!algorithmResponse (updateParameters): " + algorithmResponse);
                connectivity = true;
            }

        } catch (IOException e) {
            KafkaProducerController.logger.info(e.getMessage() + " during request to CNIT-ML algorithm instance API.");
            e.printStackTrace();
        }

        // read the Algorithm Response as JsonArray and construct the algorithmCNITMLResponse instance
        JsonParser jsonParser = new JsonParser();
        JsonObject jsonFromString;
        if(connectivity) {
            if (!jsonParser.parse(algorithmResponse).isJsonNull()) {
                jsonFromString = jsonParser.parse(algorithmResponse).getAsJsonObject();

                algorithmCNITMLResponse.setError(jsonFromString.getAsJsonObject().get("error").getAsBoolean());
                    /*algorithmCNITMLResponse.setStdout(jsonFromString.getAsJsonObject().get("stdout").getAsJsonArray().get(0).getAsString());
                    algorithmCNITMLResponse.setStderr(jsonFromString.getAsJsonObject().get("stderr").getAsJsonArray().get(0).getAsString());
                    algorithmCNITMLResponse.setReturncode(jsonFromString.getAsJsonObject().get("returncode").getAsInt());
                    algorithmCNITMLResponse.setStart(jsonFromString.getAsJsonObject().get("start").getAsString());
                    algorithmCNITMLResponse.setEnd(jsonFromString.getAsJsonObject().get("end").getAsString());*/

            }
        }

        KafkaProducerController.logger.info("Request to CNIT-ML algorithm instance API (updateParameters). Response: " + algorithmCNITMLResponse);
        return algorithmCNITMLResponse;
    }
}
