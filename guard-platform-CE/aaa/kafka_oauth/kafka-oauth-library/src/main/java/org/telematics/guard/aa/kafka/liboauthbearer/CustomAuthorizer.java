/*
 * Copyright © 2021-2022 Telematics Lab
 * Copyright © 2020 BlackRock Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *   http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
package org.telematics.guard.aa.kafka.liboauthbearer;

import kafka.security.authorizer.AclAuthorizer;
import org.apache.kafka.server.authorizer.*;
import org.apache.kafka.common.acl.*;
import org.apache.kafka.common.resource.ResourcePattern;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

import java.util.Arrays;
import java.util.ArrayList;
import java.util.Collection;
import java.util.Collections;
import java.util.List;
import java.util.concurrent.CancellationException;
import java.util.concurrent.CompletableFuture;
import java.util.concurrent.CompletionStage;
import java.util.concurrent.ExecutionException;
import java.util.stream.Collectors;
import java.util.stream.IntStream;

/**
 * The type Oauth authorizer.
 */
public class CustomAuthorizer extends AclAuthorizer implements Authorizer {
	private static final Logger log = LoggerFactory.getLogger(CustomAuthorizer.class);

	private final OAuthServiceImpl oAuthService;
	private final TemporaryAuthorizationConfiguration temporaryAuthorizationConfiguration;

	private List<TemporaryAuthorization> temporaryAuthorizations;

	/**
	 * Instantiates a new Custom authorizer.
	 */
	public CustomAuthorizer() {
		super();
		oAuthService = new OAuthServiceImpl();
		temporaryAuthorizationConfiguration = new TemporaryAuthorizationConfiguration();

		// TODO!!
		// How can we sync this information, and especially restore it, in the entire Kafka cluster?
		// Maybe we should clone AclAuthorizer and extend the synced info with zookeeper with TemporaryAuthorizations?
		temporaryAuthorizations = Collections.synchronizedList(new ArrayList());
	}

	/**
	 * Check scopes from JWT to validate topic/resource operations.
	 *
	 * @param session   session info
	 * @param operation Kafka operation
	 * @param resource  resource being operated one
	 * @return true/false
	 */
	@Override
	public List<AuthorizationResult> authorize​(AuthorizableRequestContext requestContext, List<Action> actions) {
		OAuthBearerTokenJwt jwt;
		List<OAuthScope> oauthScopes;
		CustomPrincipal principal;
		ArrayList<AuthorizationResult> results = new ArrayList<>(actions.size());
		final boolean isPolicyEnforced = oAuthService.getOAuthConfiguration().getEnforceSecurityMechanism();

		log.debug("New AuthZ for Client Addr. {} | ClientID {} | CorrelationID {} | ListenerName {} | " +
				  "Principal {} | RequestType {} | RequestVersion {} | SecurityProtocol {} | " +
				  "Actions {}",
				  requestContext.clientAddress(),
				  requestContext.clientId(),
				  requestContext.correlationId(),
				  requestContext.listenerName(),
				  requestContext.principal(),
				  requestContext.requestType(),
				  requestContext.requestVersion(),
				  requestContext.securityProtocol(),
				  actions);

		if (!isPolicyEnforced) {
			log.debug("PERMISSIVE MODE =>> AuthZ GRANTED.");
			return allowAllActions(actions.size());
		}

		try {
			switch (requestContext.securityProtocol()) {
				case SASL_SSL:
					if (!(requestContext.principal() instanceof CustomPrincipal)) {
						log.error("Session Principal is not a CustomPrincipal.");
						return setAllActions(actions.size(), !isPolicyEnforced);
					}

					principal = (CustomPrincipal) requestContext.principal();
					if (principal.getOauthBearerTokenJwt() == null) {
						log.error("No token information contained in CustomPrincipal.");
						return setAllActions(actions.size(), !isPolicyEnforced);
					}

					jwt = principal.getOauthBearerTokenJwt();
					if (jwt.scope() == null || jwt.scope().isEmpty()) {
						log.error("Cannot AuthZ this operation because no OIDC scopes are defined in the JWT.");
						return setAllActions(actions.size(), !isPolicyEnforced);
					}

					oauthScopes = parseScopes(jwt.scope());
					for (Action action : actions) {
						AuthorizationResult r = checkAuthorization(oauthScopes,
																   action.resourcePattern(),
																   action.operation().toString())
												? AuthorizationResult.ALLOWED
												: AuthorizationResult.DENIED;
						results.add(r);
					}

					return results;

				case SSL:
					log.debug("Check TLS-based AuthZ");
					List<AuthorizationResult> res = super.authorize(requestContext, actions);
					log.debug("TLS-based AuthZ result: {}", res);
					return res;
				case PLAINTEXT:
					// Passthrough all for isolated PLAINTEXT port
					log.debug("Kafka client authorized over insecure and untrusted {} connection.",
							  requestContext.securityProtocol());
					return allowAllActions(actions.size());
				case SASL_PLAINTEXT:
				default:
					log.error("Kafka client NOT authorized over {} connection!",
							  requestContext.securityProtocol());
					return denyAllActions(actions.size());
			}
		} catch (Exception e) {
			log.error("AuthZ Error", e);
		}

		return setAllActions(actions.size(), !isPolicyEnforced);
	}

	/**
	 * Create new Temporary Authorizations with the given set of ACL rules.
	 */
	@Override
	public List<? extends CompletionStage<AclCreateResult>> createAcls(AuthorizableRequestContext requestContext,
																	   List<AclBinding> aclBindings) {
		List<? extends CompletionStage<AclCreateResult>> aclResults = super.createAcls(requestContext, aclBindings);

		// TODO: How can we make the operation in .filter() async? Currently, we wait for CompletableFuture to finish,
		// which blocks the async pipeline.
		IntStream.range(0, Math.min(aclBindings.size(), aclResults.size()))
			.filter(i -> isAclCreationSuccessful(aclResults.get(i).toCompletableFuture()))
			.mapToObj(i -> aclBindings.get(i))
			.forEach(binding -> registerTemporaryAuthorization(binding));

		return aclResults;
	}

	/**
	 * Delete Temporary Authorizations with the given set of ACL rules.
	 */
	@Override
	public List<? extends CompletionStage<AclDeleteResult>> deleteAcls(AuthorizableRequestContext requestContext,
																	   List<AclBindingFilter> aclBindingFilters) {
		List<? extends CompletionStage<AclDeleteResult>> results = super.deleteAcls(requestContext, aclBindingFilters);

		for (CompletionStage<AclDeleteResult> ar : results) {
			ar.thenAcceptAsync(r -> unregisterTemporaryAuthorizations(r.aclBindingDeleteResults()));
		}

		return results;
	}

	/**
	 * List the available Temporary Authorizations. Expired TAs will be garbage collected.
	 */
	@Override
	public Iterable<AclBinding> acls(AclBindingFilter filter) {
		garbageCollectExpiredTemporaryAuthorizations();
		log.info("Temporary ACLs: {}", temporaryAuthorizations);
		return super.acls(filter);
	}

	/**
	 * Detect expired TAs and delete them from the underlying Kafka ACL Authorizer.
	 * CAVEAT: this operation is not synchronized across the entire kafka cluster.
	 */
	private void garbageCollectExpiredTemporaryAuthorizations() {
		List<TemporaryAuthorization> expiredTas = getExpiredTemporaryAuthorizations();
		List<AclBindingFilter> expiredRules = getRawAclBindingFilters(expiredTas);

		super.deleteAcls(null, expiredRules);
		temporaryAuthorizations.removeAll(expiredTas);
	}

	private List<AclBindingFilter> getRawAclBindingFilters(List<TemporaryAuthorization> tas) {
		return tas.stream()
			.map(ta -> ta.aclBindingFilter())
			.collect(Collectors.toList());
	}

	/**
	 * Retrieve the list of expired TAs from the main set of rules.
	 */
	private List<TemporaryAuthorization> getExpiredTemporaryAuthorizations() {
		return temporaryAuthorizations.stream()
			.filter(ta -> ta.isExpired())
			.collect(Collectors.toList());
	}

	/**
	 * CAVEAT: this check awaits for the completion of the AclCreateResult! Thus, it blocks the entire stream.
	 * TODO: it is needed an async reimplementation.
	 */
	private boolean isAclCreationSuccessful(CompletableFuture<AclCreateResult> futureAclResult) {
		try {
			return futureAclResult.get() == AclCreateResult.SUCCESS;
		} catch (CancellationException|ExecutionException|InterruptedException e) {
			return false;
		}
	}

	private void registerTemporaryAuthorization(AclBinding ab) {
		long taExpireTimeSeconds = temporaryAuthorizationConfiguration.getExpireTimeSeconds();
		TemporaryAuthorization ta = new TemporaryAuthorization(ab.toFilter(), taExpireTimeSeconds);
		temporaryAuthorizations.add(ta);
	}

	private void unregisterTemporaryAuthorizations(Collection<AclDeleteResult.AclBindingDeleteResult> results) {
		results.stream()
			.filter(r -> !r.exception().isPresent())
			.map(r -> r.aclBinding())
			.forEach(binding -> temporaryAuthorizations.removeIf(ta -> ta.matches(binding)));
	}

	private List<AuthorizationResult> allowAllActions(int numberOfActions) {
		return setAllActions(numberOfActions, AuthorizationResult.ALLOWED);
	}

	private List<AuthorizationResult> denyAllActions(int numberOfActions) {
		return setAllActions(numberOfActions, AuthorizationResult.DENIED);
	}

	private List<AuthorizationResult> setAllActions(int numberOfActions, boolean targetResult) {
		return targetResult
			? setAllActions(numberOfActions, AuthorizationResult.ALLOWED)
			: setAllActions(numberOfActions, AuthorizationResult.DENIED);
	}

	private List<AuthorizationResult> setAllActions(int numberOfActions, AuthorizationResult targetResult) {
		ArrayList<AuthorizationResult> ret = new ArrayList<>(numberOfActions);

		for (int i = 0; i < numberOfActions; i++) {
			ret.add(targetResult);
		}

		return ret;
	}

	/**
	 * Check authorization against scopes.
	 *
	 * @param scopeInfo list of scopes
	 * @param resource  resource info
	 * @param operation operation performed
	 * @return true /false
	 */
	protected boolean checkAuthorization(List<OAuthScope> scopeInfo, ResourcePattern resource, String operation) {
		for (OAuthScope scope : scopeInfo) {
			String lowerCaseOperation = operation.toLowerCase();
			String lowerCaseResourceName = resource.name().toLowerCase();
			String lowerCaseCaseResourceType = resource.resourceType().toString().toLowerCase();

			boolean operationVal = scope.getOperation().toLowerCase().equals(lowerCaseOperation);
			boolean nameVal = scope.getResourceName().toLowerCase().equals(lowerCaseResourceName);
			boolean typeVal = scope.getResourceType().toLowerCase().equals(lowerCaseCaseResourceType);

			if (operationVal && nameVal && typeVal) {
				log.debug("AuthZ OIDC Check OK");
				return true;
			}
		}

		log.error("AuthZ OIDC Check failed by client with scopes {} to do {} for the given resource {}.",
				 scopeInfo, operation, resource);
		return false;
	}

	/**
	 * Parse topic and Operation out of scope.
	 *
	 * @param scopes set of scopes
	 * @return return list of pairs, each pair is a topic/operation
	 * 				  Scope format kafka:<resourceType>:<resourceName>:<operation>
	 */
	protected List<OAuthScope> parseScopes(java.util.Set<String> scopes) {
		List<OAuthScope> result = new ArrayList<>();

		for (String scope : scopes) {
			String[] scopeArray = scope.split("\\s+");
			for (String str : scopeArray){
				convertScope(result, str);
			}
		}

		return result;
	}

	/**
	 * convertScope.
	 * @param result list of scopesInfo
	 * @param scope string of scope
	 */
	private void convertScope(List<OAuthScope> result, String scope) {
		String[] str = scope.split(":");

		if (str.length == 4) {
			String type = str[1];
			String name = str[2];
			String operation = str[3];
			OAuthScope oAuthScope = new OAuthScope();
			oAuthScope.setOperation(operation);
			oAuthScope.setResourceName(name);
			oAuthScope.setResourceType(type);
			result.add(oAuthScope);
		} else {
			log.error("Format not recognized for the given OIDC scope: {}.", scope);
		}
	}
}
