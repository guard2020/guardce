package com.cnit.guard.aaa.testcase.one;

import org.eclipse.microprofile.auth.LoginConfig;

import javax.annotation.security.DeclareRoles;

import javax.ws.rs.ApplicationPath;
import javax.ws.rs.core.Application;

/**
 * Application Endpoint Service
 *
 * Author: Giovanni Grieco <giovanni.grieco@poliba.it>
 */
@ApplicationPath("/testcase-one")

@LoginConfig(authMethod = "MP-JWT", realmName = "org.wso2.is")
@DeclareRoles({"admin", "user"})

public class TestcaseoneRestApplication extends Application {
}
