package eu.smartcontroller.guard.demo.controller;

import com.google.gson.JsonObject;
import com.google.gson.JsonParser;
import eu.smartcontroller.guard.demo.model.Rulefile;
import eu.smartcontroller.guard.demo.model.Rulefile2;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.UnsupportedEncodingException;
import java.net.*;
import java.util.HashMap;
import java.util.Map;
import java.util.UUID;

import static eu.smartcontroller.guard.demo.SpringBootKafkaAppApplication.pgasigEndpoint;
import static eu.smartcontroller.guard.demo.service.DroolsEngineService.addRulefileToInternalMemory;

public class PgasigHandler {

    public static Rulefile2 exportRuleFile() {

        Rulefile2 rulefile = new Rulefile2();
        String uniqueID = UUID.randomUUID().toString();
        rulefile.setUuid(uniqueID);

        // send a request for new Rulefile and get the answer
        HttpURLConnection con;
        try {
            URL url = new URL("http://" + pgasigEndpoint + "/rulefile/export?delta_update=11");
            con = (HttpURLConnection)url.openConnection();
            con.setRequestMethod("GET");
            con.setRequestProperty("X-API-Key", "GUARD2022DEMO");

            // get the response
            try(BufferedReader br = new BufferedReader(new InputStreamReader(con.getInputStream(), "utf-8"))) {
                StringBuilder response = new StringBuilder();

                String responseLine = null;
                while ((responseLine = br.readLine()) != null) {
                    response.append(responseLine + "\n");
                }
                System.out.println("ExportRuleFile request response: " + response);
                rulefile.setContent(response.toString());

            }

        } catch (IOException e) {
            KafkaProducerController.logger.info(e.getMessage() + " during request to Pgasig API.");
            e.printStackTrace();
        }

        //KafkaProducerController.logger.info("Request to Pgasig API. Response: " + responeString);
        //addRulefileToInternalMemory(rulefile);
        return rulefile;
    }

    private static String getParamsString(Map<String, String> params) throws UnsupportedEncodingException {
        StringBuilder result = new StringBuilder();

        for (Map.Entry<String, String> entry : params.entrySet()) {
            result.append(URLEncoder.encode(entry.getKey(), "UTF-8"));
            result.append("=");
            result.append(URLEncoder.encode(entry.getValue(), "UTF-8"));
            result.append("&");
        }

        String resultString = result.toString();
        return resultString.length() > 0
                ? resultString.substring(0, resultString.length() - 1)
                : resultString;
    }
}
