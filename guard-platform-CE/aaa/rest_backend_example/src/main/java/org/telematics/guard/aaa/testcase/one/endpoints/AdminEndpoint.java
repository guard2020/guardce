package com.cnit.guard.aaa.testcase.one.endpoints;

import org.eclipse.microprofile.jwt.JsonWebToken;

import javax.annotation.security.RolesAllowed;
import javax.enterprise.context.RequestScoped;
import javax.inject.Inject;
import javax.json.Json;
import javax.json.JsonObjectBuilder;
import javax.ws.rs.GET;
import javax.ws.rs.Path;
import javax.ws.rs.core.Response;

/**
 * Admin endpoint is made exclusively for, well, admins.
 *
 * Author: Giovanni Grieco <giovanni.grieco@poliba.it>
 */
@Path("/admin")
@RequestScoped
public class AdminEndpoint {

    @Inject
    private JsonWebToken callerPrincipal;

    @GET
    @RolesAllowed({"admin"})
    public Response getAdminEndpoint() {
        final String response = "Welcome, " + callerPrincipal.getSubject() + ". You are authorized to access this endpoint with admin role.";

        JsonObjectBuilder result = Json.createObjectBuilder()
                .add("response", response);

        return Response.ok(result.build()).build();
    }

}
