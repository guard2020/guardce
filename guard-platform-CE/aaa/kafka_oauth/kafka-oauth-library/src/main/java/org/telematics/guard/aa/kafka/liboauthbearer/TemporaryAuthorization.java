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

import org.apache.kafka.common.acl.AclBinding;
import org.apache.kafka.common.acl.AclBindingFilter;

import java.time.LocalDateTime;
import java.time.temporal.ChronoUnit;

public class TemporaryAuthorization {
    private AclBindingFilter bindingFilter;
    private LocalDateTime createdAt;
    private LocalDateTime expireAt;

    public TemporaryAuthorization(AclBindingFilter flt, long expireTimeSeconds) {
        bindingFilter = flt;
        createdAt = LocalDateTime.now();
        expireAt = createdAt.plus(expireTimeSeconds, ChronoUnit.SECONDS);
    }

    public AclBindingFilter aclBindingFilter() {
        return bindingFilter;
    }

    public boolean isExpired() {
        LocalDateTime now = LocalDateTime.now();

        if (now.isBefore(createdAt) || now.isAfter(expireAt))
            return true;

        return false;
    }

    public boolean matches(AclBinding a) {
        return bindingFilter.matches(a);
    }

    public String toString() {
        return String.format(
            "TemporaryAuthorization {filter: %s, createdAt: %s, expireAt: %s}",
            bindingFilter.toString(), createdAt.toString(), expireAt.toString());
    }
}