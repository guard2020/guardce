package com.cnit.guard.aaa.testcase.one.endpoints;

import org.eclipse.microprofile.jwt.JsonWebToken;

import javax.enterprise.context.RequestScoped;
import javax.inject.Inject;
import javax.ws.rs.GET;
import javax.ws.rs.Path;

/**
 * Debug endpoint is useful to debug JWT information.
 *
 * Author: Giovanni Grieco <giovanni.grieco@poliba.it>
 */
@Path("/debug")
@RequestScoped
public class DebugEndpoint {

    @Inject
    private JsonWebToken callerPrincipal;

    @GET
    public String getJwt() {
        return callerPrincipal.toString();
    }

}
