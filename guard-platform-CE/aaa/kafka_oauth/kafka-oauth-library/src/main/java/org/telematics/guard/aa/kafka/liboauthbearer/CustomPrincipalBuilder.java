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

import org.apache.kafka.common.KafkaException;
import org.apache.kafka.common.security.auth.AuthenticationContext;
import org.apache.kafka.common.security.auth.KafkaPrincipal;
import org.apache.kafka.common.security.auth.KafkaPrincipalBuilder;
import org.apache.kafka.common.security.auth.PlaintextAuthenticationContext;
import org.apache.kafka.common.security.auth.SaslAuthenticationContext;
import org.apache.kafka.common.security.auth.SslAuthenticationContext;

import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

import javax.net.ssl.SSLPeerUnverifiedException;

/**
 * The type Custom principal builder.
 */
public class CustomPrincipalBuilder implements KafkaPrincipalBuilder {
	private static final Logger log = LoggerFactory.getLogger(CustomPrincipalBuilder.class);

	@Override
	public KafkaPrincipal build(AuthenticationContext authenticationContext) throws KafkaException{
		try {
			CustomPrincipal customPrincipal;

			if (authenticationContext instanceof SaslAuthenticationContext) {
				SaslAuthenticationContext context = (SaslAuthenticationContext) authenticationContext;
				OAuthBearerTokenJwt token = (OAuthBearerTokenJwt) context.server()
						.getNegotiatedProperty("OAUTHBEARER.token");

				// log.info("Rx JWT: {}", token.toString());

				customPrincipal = new CustomPrincipal(KafkaPrincipal.USER_TYPE, token.principalName());
				customPrincipal.setOauthBearerTokenJwt(token);

				return customPrincipal;
			} else if (authenticationContext instanceof PlaintextAuthenticationContext) {
				PlaintextAuthenticationContext context = (PlaintextAuthenticationContext) authenticationContext;
				log.info("Unauthenticated access through PLAINTEXT channel: " +
						 "HOSTNAME " + context.clientAddress().getHostName()  + " | " +
						 "ADDRESS " + context.clientAddress().toString() 	  + " | " +
						 "LISTENER_NAME " + context.listenerName() 			  + " | " +
						 "PROTOCOL " + context.securityProtocol().toString());

				String principalName = String.format("%s,UNAUTHENTICATED,%s", 
													 context.clientAddress().toString(),
													 context.listenerName().toString());
				return new KafkaPrincipal(KafkaPrincipal.USER_TYPE, principalName);
			} else if (authenticationContext instanceof SslAuthenticationContext) {
				SslAuthenticationContext context = (SslAuthenticationContext) authenticationContext;

				try {
					log.info("Authenticated access through SSL channel: " 		 +
							 "HOSTNAME " + context.clientAddress().getHostName() + " | " +
							 "ADDRESS " + context.clientAddress().toString()     + " | " +
							 "LISTENER_NAME " + context.listenerName() 			 + " | " +
							 "PROTOCOL " + context.securityProtocol().toString() + " | " +
							 "PRINCIPAL_NAME " + context.session().getPeerPrincipal().getName());

					return new KafkaPrincipal(KafkaPrincipal.USER_TYPE,
											  context.session().getPeerPrincipal().getName());
				} catch (SSLPeerUnverifiedException e) {
					log.info("Unauthenticated access through SSL channel: " 	 +
							 "HOSTNAME " + context.clientAddress().getHostName() + " | " +
							 "ADDRESS " + context.clientAddress().toString() 	 + " | " +
							 "LISTENER_NAME " + context.listenerName() 			 + " | " 	+
							 "PROTOCOL " + context.securityProtocol().toString());

					String principalName = String.format("%s,UNAUTHENTICATED,%s",
														 context.clientAddress().toString(),
														 context.listenerName());
					return new KafkaPrincipal(KafkaPrincipal.USER_TYPE, principalName);
				}
			} else {
				throw new KafkaException("Cannot initialize Principal. " +
										 "SASL/SSL/PLAINTEXT AuthenticationContext is required. " +
										 "Got: " + authenticationContext.getClass().getSimpleName());
			}
		} catch (Exception e) {
			throw new KafkaException("Cannot initialize Principal.", e);
		}
	}
}
