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

/**
 * The type Environment variables util.
 */
public class EnvironmentVariablesUtil {

    /**
     * Gets boolean environment variable.
     *
     * @param envName      the env name
     * @param defaultValue the default value
     * @return the boolean environment variable
     */
    public static Boolean getBooleanEnvironmentVariable(String envName, Boolean defaultValue) {
        Boolean result;
        String env = System.getenv(envName);
        if (env == null) {
            result = defaultValue;
        } else {
            result = Boolean.valueOf(env);
        }
        return result;
    }

    /**
     * Gets string environment variable.
     *
     * @param envName      the env name
     * @param defaultValue the default value
     * @return the string environment variable
     */
    public static String getStringEnvironmentVariable(String envName, String defaultValue) {
        String result;
        String env = System.getenv(envName);
        if (env == null) {
            result = defaultValue;
        } else {
            result = env;
        }
        return result;
    }

    public static Long getLongEnvironmentVariable(String envName, Long defaultValue) {
        String val = System.getenv(envName);
        return (val == null) ? defaultValue : Long.valueOf(val);
    }
}
