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

import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.InputStream;
import java.util.Properties;

public class TemporaryAuthorizationConfiguration {
    private static final String KAFKA_TEMPORARYAUTHZ_PROP_FILE_ENV_VAR = "KAFKA_TEMPORARYAUTHZ_PROP_FILE";
    private static final String KAFKA_TEMPORARYAUTHZ_EXPIRETIME_SECONDS = "temporaryauthz.expiretime.seconds";

    private final Logger log = LoggerFactory.getLogger(TemporaryAuthorizationConfiguration.class);
    private Properties properties;
    private long expireTimeSeconds;

    public TemporaryAuthorizationConfiguration() {
        log.debug("Intializing Temporary Authorization module Configuration");

        properties = this.getConfigurationFileProperties();

        expireTimeSeconds = getLongParameter(
            KAFKA_TEMPORARYAUTHZ_EXPIRETIME_SECONDS,
            3600 /* 1 hour */);

        log.info("TemporaryAuthorizationConfiguration values:\n"
                 + "\t{} = {}\n",
                 KAFKA_TEMPORARYAUTHZ_EXPIRETIME_SECONDS, expireTimeSeconds);
    }

    public long getExpireTimeSeconds() {
        return expireTimeSeconds;
    }

    private long getLongParameter(String paramName, long defaultValue) {
        String paramEnvName = buildEnvironmentParameterName(paramName);
        String propertyValue = properties.getProperty(paramName);
        Long fallbackValue = null;

        try {
            fallbackValue = Long.valueOf(propertyValue);
        } catch (NumberFormatException e) {
            fallbackValue = Long.valueOf(defaultValue);
        }

        return EnvironmentVariablesUtil.getLongEnvironmentVariable(paramEnvName, fallbackValue).longValue();
    }

    private String buildEnvironmentParameterName(String paramName) {
        String baseName = paramName.toUpperCase().replace('.', '_');
        return String.format("KAFKA_%s", baseName);
    }

    private Properties getConfigurationFileProperties() {
        Properties p = new Properties();

        try {
            log.debug("Loading properties for Temporary Authorization module.");

            String configFilePath = EnvironmentVariablesUtil.getStringEnvironmentVariable(
                KAFKA_TEMPORARYAUTHZ_PROP_FILE_ENV_VAR,
                null);

            if (!Utils.isNullOrEmpty(configFilePath)) {
                log.debug("Properties will be loaded from {}", configFilePath);

                InputStream i = new FileInputStream(configFilePath);
                p.load(i);
            }
        } catch (FileNotFoundException e) {
            log.warn("Temporary Authorization property file not found. Message: {}", e.getMessage());
        } catch (IOException e) {
            log.warn("Error reading Temporary Authorization property file. Message: {}", e.getMessage ());
        } finally {
            log.debug("Finished loading configuration properties for Temporary Authorization Module.");
        }

        return p;
    }
}
