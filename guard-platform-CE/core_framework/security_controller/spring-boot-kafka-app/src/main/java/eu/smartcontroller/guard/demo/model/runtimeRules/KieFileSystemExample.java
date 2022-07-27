package eu.smartcontroller.guard.demo.model.runtimeRules;
/*
 * Copyright (c) 2016 ... and others.  All rights reserved.
 *
 * This program and the accompanying materials are made available under the
 * terms of the Eclipse Public License v1.0 which accompanies this distribution,
 * and is available at http://www.eclipse.org/legal/epl-v10.html
 */

import eu.smartcontroller.guard.demo.controller.PgasigHandler;
import eu.smartcontroller.guard.demo.model.Rulefile;
import eu.smartcontroller.guard.demo.model.aminer.AMinerOutput;
import eu.smartcontroller.guard.demo.service.DroolsEngineService;
import org.kie.api.KieBase;
import org.kie.api.KieServices;
import org.kie.api.builder.KieBuilder;
import org.kie.api.builder.KieFileSystem;
import org.kie.api.builder.ReleaseId;
import org.kie.api.builder.model.KieModuleModel;
import org.kie.api.io.Resource;
import org.kie.api.io.ResourceType;
import org.kie.api.runtime.KieSession;

import java.io.*;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;

import static eu.smartcontroller.guard.demo.service.DroolsEngineService.kieSession;

public class KieFileSystemExample {

    private static final ReleaseId APP_REL_ID = KieServices.Factory.get().newReleaseId("org.drools.example", "fetch-external-resource", "1.0.0-SNAPSHOT");
    //private static final ReleaseId APP_REL_ID = KieServices.Factory.get().newReleaseId("eu.semiotics.pattern", "fetch-external-resource", "1.0.0-SNAPSHOT");
    public static String EXTERNAL_DRL_RESOURCE = "./externalrules/";
    //public static String EXTERNAL_DRL_RESOURCE = "./src/main/resources/";
    public KieServices ks;
    public static KieSession ksession1;
	/*public static ArrayList<Placeholder> placeholderlist = new ArrayList<Placeholder>();
	public static ArrayList<MYRule> rulelist = new ArrayList<MYRule>();*/

    private KieBase createKieBase() {

        final String PACKAGE_NAME = "externalrules";
        ks = KieServices.Factory.get();
        KieFileSystem kfs = ks.newKieFileSystem();
        kfs.generateAndWritePomXML(APP_REL_ID);

        //read all files from folder distribution/files/externalrules/

        //create folder externalrules if doesn't exist
        File folder = new File(EXTERNAL_DRL_RESOURCE);
        if (!folder.exists()){
            folder.mkdir();

        }

        Resource rs = null,rs2 = null;

        for (final File fileEntry : folder.listFiles()) {
            if (fileEntry.isDirectory()) {
                //
            } else {
                try {
                    rs = ks.getResources().newUrlResource("file:///"+fileEntry.getCanonicalPath()).setResourceType(ResourceType.DRL);
                    kfs.write(rs);
                } catch (IOException e) {
                    e.printStackTrace();
                }
            }
        }

        kfs.writeKModuleXML(createKieProjectWithPackages(ks, PACKAGE_NAME).toXML());
        ks.newKieBuilder( kfs ).buildAll();
        KieBuilder kbuilder = ks.newKieBuilder(kfs);
        kbuilder.buildAll();
        if (kbuilder.getResults().getMessages().size()!=0){
            System.out.println("Errors " + kbuilder.getResults().toString());
        }
        return ks.newKieContainer(APP_REL_ID).getKieBase();
    }

    private KieModuleModel createKieProjectWithPackages(KieServices ks, String pkg) {
        KieModuleModel kmodule = ks.newKieModuleModel();
        kmodule.newKieBaseModel("KBase").addPackage(pkg).setDefault(true).newKieSessionModel("defaultSession").setDefault(true);

        return kmodule;
    }

    public  void insertAllFacts(){
        Date now2 = new Date();
        SimpleDateFormat sdf = new SimpleDateFormat("HH:mm:ss.SSS");

        System.out.println(sdf.format(now2) + " Firing Rules!");
        KieServices ks1;
        KieBase kbase1;
        //KieSession ksession1;

        ks1 = KieServices.Factory.get();
        ks1.newKieClasspathContainer(getClass().getClassLoader());
        Thread.currentThread().setContextClassLoader(getClass().getClassLoader());

        kbase1 = createKieBase();
        ksession1 = kbase1.newKieSession();

        //insert all Rulefiles in the working memory
        for (int i = 0; i < DroolsEngineService.rulefiles.size(); i++) {
            ksession1.insert(DroolsEngineService.rulefiles.get(i));
            //System.out.println("Rulefile " + i + " from the array: " + DroolsEngineService.rulefiles.get(i));
        }

        //insert all AnalysisComponents in the working memory
        for (int i = 0; i < DroolsEngineService.aMinerOutputs.size(); i++) {
            ksession1.insert(DroolsEngineService.aMinerOutputs.get(i));
        }

        //insert all AnalysisComponents in the working memory
        for (int i = 0; i < DroolsEngineService.cnitMLOutputs.size(); i++) {
            ksession1.insert(DroolsEngineService.cnitMLOutputs.get(i));

        }

      
        ksession1.fireAllRules();
    
        ksession1.dispose();

    }
}
