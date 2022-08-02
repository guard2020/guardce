package eu.smartcontroller.guard.demo.service;

import org.kie.api.runtime.KieContainer;
import org.kie.api.runtime.KieSession;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;

@Service
public class DroolsEngineService {

	private static KieContainer kieContainer;
	public static KieSession kieSession;

	@Autowired
	public DroolsEngineService(KieContainer kieContainer) {
		this.kieContainer = kieContainer;
		this.kieSession = kieContainer.newKieSession("rulesSession");
	}

}