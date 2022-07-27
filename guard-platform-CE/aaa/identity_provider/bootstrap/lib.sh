function create_user() {
    username=$1
    password=$2
    user=$3
    request_data=$4

    if [ ! -f "${request_data}" ]; then
            echo "${request_data} File does not exists."
            return 255
    fi

    echo
    echo "Creating a user named ${user}..."

    curl -s \
         -k \
         --user "${username}":"${password}" \
         --data-binary @${request_data} \
         --header "Content-Type:application/json" \
         -o /dev/null \
         https://localhost:${WSO2_IS_TEMP_PORT}/wso2/scim/Users

    res=$?
    if test "${res}" != "0"; then
        echo "!! Problem occurred while creating user ${user}. !!"
        echo "${res}"
        return 255
    fi
    echo "** The user ${user} was successfully created. **"
    echo

    return 0;
}

function create_service_provider() {
    sp_name=$1
    username=$2
    password=$3
    config_file=$4

    auth=$(echo "${username}:${password}" | base64)

    if [ ! -d ".bootstrap" ]; then
        mkdir .bootstrap
    fi

    if [ ! -f "${config_file}" ]; then
        echo "${config_file} File does not exists."
        return 255
    fi

    if [ -f .bootstrap/create-sp-"${sp_name}".xml ]; then
        rm .bootstrap/create-sp-"${sp_name}".xml
    fi

    cp ${config_file} .bootstrap/create-sp-"${sp_name}".xml

    sed -i 's/${SP_NAME}/'${sp_name}'/g' .bootstrap/create-sp-"${sp_name}".xml

    echo "*** Creating Service Provider ${sp_name}..."

    CURL_RES=$(curl -s \
         -k \
         -w "%{http_code}" \
         -o .bootstrap/create-sp-"${sp_name}"-response.xml \
         -d @.bootstrap/create-sp-"${sp_name}".xml \
         -H "Authorization: Basic ${auth}" \
         -H "Content-Type: text/xml" \
         -H "SOAPAction: urn:createApplication" \
         "https://localhost:${WSO2_IS_TEMP_PORT}/services/IdentityApplicationManagementService.IdentityApplicationManagementServiceHttpsSoap11Endpoint/")

    if [[ ! -z "$CURL_RES" && "$CURL_RES" -eq "200" ]]; then
        echo "** Service Provider ${sp_name} successfully created. **"

        return 0;
    fi

    echo "!! Problem occurred while creating the service provider: ${sp_name} !!"
    echo "${CURL_RES}"
    return 255
}

function get_service_provider_id() {
    sp_name=$1
    username=$2
    password=$3
    template_file=$4

    auth=$(echo "${username}:${password}" | base64)

    if [ ! -d ".bootstrap" ]; then
        mkdir .bootstrap
    fi

    if [ ! -f "${template_file}" ]; then
        echo "${template_file} File does not exists."
        return 255
    fi

    if [ -f .bootstrap/get-application-"${sp_name}".xml ]; then
        rm .bootstrap/get-application-"${sp_name}".xml
    fi

    cp ${template_file} .bootstrap/get-application-"${sp_name}".xml

    sed -i 's/${SP_NAME}/'${sp_name}'/g' .bootstrap/get-application-"${sp_name}".xml

    CURL_RES=$(curl -s \
         -k \
         -w "%{http_code}" \
         -d @.bootstrap/get-application-"${sp_name}".xml \
         -o .bootstrap/get-application-"${sp_name}"-response.xml \
         -H "Authorization: Basic ${auth}" \
         -H "Content-Type: text/xml" \
         -H "SOAPAction: urn:getApplication" \
         "https://localhost:${WSO2_IS_TEMP_PORT}/services/IdentityApplicationManagementService.IdentityApplicationManagementServiceHttpsSoap11Endpoint/")

    if [[ ! -z "$CURL_RES" && "$CURL_RES" -eq "200" ]]; then
        echo $(grep -o 'applicationID>[0-9]\+' .bootstrap/get-application-${sp_name}-response.xml | cut -d '>' -f 2)
        return 0;
    fi

    echo "!! Cannot retrieve Application ID for Service Provider: ${sp_name} !!"
    echo "${CURL_RES}"
    return 255

}

function configure_service_provider_oauth() {
    sp_name=$1
    username=$2
    password=$3
    template_file=$4
    # escape special characters that could confuse sed during string substitution in template file
    callback_url=$(echo $5 | sed 's/\//\\\//g')
    oauth_key=$6
    oauth_secret=$7

    auth=$(echo "${username}:${password}" | base64)

    if [ ! -d ".bootstrap" ]; then
        mkdir .bootstrap
    fi

    if [ ! -f "${template_file}" ]; then
        echo "${template_file} File does not exists."
        return 255
    fi

    if [ -f .bootstrap/register-oauth-app-${sp_name}.xml ]; then
        rm .bootstrap/register-oauth-app-${sp_name}.xml
    fi

    cp ${template_file} .bootstrap/register-oauth-app-${sp_name}.xml

    sed -i \
        -e 's/${SP_NAME}/'${sp_name}'/g' \
        -e 's/${CALLBACK_URL}/'${callback_url}'/g' \
        -e 's/${OAUTH_KEY}/'${oauth_key}'/g' \
        -e 's/${OAUTH_SECRET}/'${oauth_secret}'/g' \
        .bootstrap/register-oauth-app-${sp_name}.xml

    CURL_RES=$(curl -s \
         -k \
         -w "%{http_code}" \
         -d @.bootstrap/register-oauth-app-"${sp_name}".xml \
         -o .bootstrap/register-oauth-app-"${sp_name}"-response.xml \
         -H "Authorization: Basic ${auth}" \
         -H "Content-Type: text/xml" \
         -H "SOAPAction: urn:registerOAuthApplicationData" \
         "https://localhost:${WSO2_IS_TEMP_PORT}/services/OAuthAdminService?wsdl")

    if [[ ! -z "$CURL_RES" && "$CURL_RES" -eq "200" ]]; then
        RES=$(grep -o 'ns:return[^<>]\+xsi:nil="true"' .bootstrap/register-oauth-app-${sp_name}-response.xml)

        if [ ! -z "$RES" ]; then
            return 0;
        fi
    fi

    echo "!! Cannot configure OAuth for Service Provider: ${sp_name} !!"
    echo "${CURL_RES}"
    return 255
}

function activate_service_provider_oauth() {
    sp_name=$1
    username=$2
    password=$3
    template_file=$4
    oauth_key=$5
    oauth_secret=$6
    sp_id=$7

    auth=$(echo "${username}:${password}" | base64)

    if [ ! -d ".bootstrap" ]; then
        mkdir .bootstrap
    fi

    if [ ! -f "${template_file}" ]; then
        echo "${template_file} File does not exists."
        return 255
    fi

    if [ -f .bootstrap/update-application-${sp_name}.xml ]; then
        rm .bootstrap/update-application-${sp_name}.xml
    fi

    cp ${template_file} .bootstrap/update-application-${sp_name}.xml

    sed -i \
        -e 's/${SP_ID}/'${sp_id}'/g' \
        -e 's/${SP_NAME}/'${sp_name}'/g' \
        -e 's/${OAUTH_KEY}/'${oauth_key}'/g' \
        -e 's/${OAUTH_SECRET}/'${oauth_secret}'/g' \
        .bootstrap/update-application-${sp_name}.xml

    CURL_RES=$(curl -s \
         -k \
         -w "%{http_code}" \
         -d @.bootstrap/update-application-"${sp_name}".xml \
         -o .bootstrap/update-application-"${sp_name}"-response.xml \
         -H "Authorization: Basic ${auth}" \
         -H "Content-Type: text/xml" \
         -H "SOAPAction: urn:updateApplication" \
         "https://localhost:${WSO2_IS_TEMP_PORT}/services/IdentityApplicationManagementService.IdentityApplicationManagementServiceHttpsSoap11Endpoint/")

    if [[ ! -z "$CURL_RES" && "$CURL_RES" -eq "200" ]]; then
        RES=$(grep -o 'ns:return[^<>]\+xsi:nil="true"' .bootstrap/register-oauth-app-${sp_name}-response.xml)

        if [ ! -z "$RES" ]; then
            return 0;
        fi
    fi

    echo "!! Cannot configure OAuth for Service Provider: ${sp_name} !!"
    echo "${CURL_RES}"
    return 255
}

function activate_service_provider_oauth_google() {
    sp_name=$1
    username=$2
    password=$3
    template_file=$4
    oauth_key=$5
    oauth_secret=$6
    sp_id=$7
    idp_name=$8

    auth=$(echo "${username}:${password}" | base64)

    if [ ! -d ".bootstrap" ]; then
        mkdir .bootstrap
    fi

    if [ ! -f "${template_file}" ]; then
        echo "${template_file} File does not exists."
        return 255
    fi

    if [ -f .bootstrap/update-application-${sp_name}.xml ]; then
        rm .bootstrap/update-application-${sp_name}.xml
    fi

    cp ${template_file} .bootstrap/update-application-${sp_name}.xml

    sed -i \
        -e 's/${SP_ID}/'${sp_id}'/g' \
        -e 's/${SP_NAME}/'${sp_name}'/g' \
        -e 's/${OAUTH_KEY}/'${oauth_key}'/g' \
        -e 's/${OAUTH_SECRET}/'${oauth_secret}'/g' \
        -e 's/${IDP_NAME}/'${idp_name}'/g' \
        .bootstrap/update-application-${sp_name}.xml

    CURL_RES=$(curl -s \
         -k \
         -w "%{http_code}" \
         -d @.bootstrap/update-application-"${sp_name}".xml \
         -o .bootstrap/update-application-"${sp_name}"-response.xml \
         -H "Authorization: Basic ${auth}" \
         -H "Content-Type: text/xml" \
         -H "SOAPAction: urn:updateApplication" \
         "https://localhost:${WSO2_IS_TEMP_PORT}/services/IdentityApplicationManagementService.IdentityApplicationManagementServiceHttpsSoap11Endpoint/")

    if [[ ! -z "$CURL_RES" && "$CURL_RES" -eq "200" ]]; then
        RES=$(grep -o 'ns:return[^<>]\+xsi:nil="true"' .bootstrap/register-oauth-app-${sp_name}-response.xml)

        if [ ! -z "$RES" ]; then
            return 0;
        fi
    fi

    echo "!! Cannot configure OAuth for Service Provider: ${sp_name} !!"
    echo "${CURL_RES}"
    return 255
}

function configure_service_provider_for_samlsso() {
    sp_name=$1
    issuer=$2
    acs=$3
    username=$4
    password=$5
    config_file=$6

    auth=$(echo "${username}:${password}"|base64)

    if [ ! -d ".bootstrap" ]; then
        mkdir .bootstrap
    fi

    if [ ! -f "${config_file}" ]; then
        echo "${config_file} File does not exists."
        return 255
    fi

    if [ -f .bootstrap/sso-config-"${sp_name}".xml ]; then
        rm .bootstrap/sso-config-"${sp_name}".xml
    fi

    cp ${config_file} .bootstrap/sso-config-"${sp_name}".xml

    sed -i 's#${ISSUER}#'${issuer}'#g' .bootstrap/sso-config-"${sp_name}".xml
    sed -i 's#${ACS}#'${acs}'#g' .bootstrap/sso-config-"${sp_name}".xml

    echo "Configuring SAML2 web SSO for ${sp_name}..."

    curl -s \
         -k \
         -d @.bootstrap/sso-config-"${sp_name}".xml \
         -H "Authorization: Basic ${auth}" \
         -H "Content-Type: text/xml" \
         -H "SOAPAction: urn:addRPServiceProvider" \
         https://localhost:${WSO2_IS_TEMP_PORT}/services/IdentitySAMLSSOConfigService.IdentitySAMLSSOConfigServiceHttpsSoap11Endpoint/

    res=$?
    if test "${res}" != "0"; then
        echo "!! Problem occurred while configuring SAML2 web SSO for ${sp_name}.... !!"
        echo "${res}"
        return 255
    fi
    echo "** Successfully configured SAML for ${sp_name}. **"
    return 0;
}

function update_service_provider_with_samlsso() {
    sp_name=$1
    username=$2
    password=$3
    config_file=$4
    saasApp=$5

    auth=$(echo "${username}:${password}"|base64)

    if [ ! -d ".bootstrap" ]; then
        mkdir .bootstrap
    fi

    if [ -f .bootstrap/response_unformatted.xml ]; then
        rm .bootstrap/response_unformatted.xml
    fi

    if [ ! -f "${config_file}" ]; then
        echo "${config_file} File does not exists."
        return 255
    fi

    if [ -f .bootstrap/get-sp-"${sp_name}".xml ]; then
        rm .bootstrap/get-sp-"${sp_name}".xml
    fi

    cp ${config_file} .bootstrap/get-sp-"${sp_name}".xml

    sed -i 's/${SP_NAME}/'${sp_name}'/g' .bootstrap/get-sp-"${sp_name}".xml

    touch .bootstrap/response_unformatted.xml
    curl -s \
         -k \
         -d @.bootstrap/get-sp-"${sp_name}".xml \
         -H "Authorization: Basic ${auth}" \
         -H "Content-Type: text/xml" \
         -H "SOAPAction: urn:getApplication" \
         https://localhost:${WSO2_IS_TEMP_PORT}/services/IdentityApplicationManagementService.IdentityApplicationManagementServiceHttpsSoap11Endpoint/ > .bootstrap/response_unformatted.xml

    res=$?
    if test "${res}" != "0"; then
        echo "!! Problem occurred while getting application details for ${sp_name}.... !!"
        echo "${res}"
        return 255
    fi

    xmllint --format .bootstrap/response_unformatted.xml
    app_id=$(xmllint --xpath "//*[local-name()='applicationID']/text()" .bootstrap/response_unformatted.xml)
    rm .bootstrap/response_unformatted.xml

    if [ -f ".bootstrap/update-sp-${sp_name}.xml" ]; then
        rm .bootstrap/update-sp-"${sp_name}".xml
    fi

    touch .bootstrap/update-sp-"${sp_name}".xml
    echo "<soapenv:Envelope xmlns:soapenv="\"http://schemas.xmlsoap.org/soap/envelope/"\" xmlns:xsd="\"http://org.apache.axis2/xsd"\" xmlns:xsd1="\"http://model.common.application.identity.carbon.wso2.org/xsd"\">
        <soapenv:Header/>
        <soapenv:Body>
            <xsd:updateApplication>
                <!--Optional:-->
                <xsd:serviceProvider>
                    <!--Optional:-->
                    <xsd1:applicationID>${app_id}</xsd1:applicationID>
                    <!--Optional:-->
                    <xsd1:applicationName>${sp_name}</xsd1:applicationName>
                    <!--Optional:-->
                    <xsd1:claimConfig>
                        <!--Optional:-->
                        <xsd1:alwaysSendMappedLocalSubjectId>false</xsd1:alwaysSendMappedLocalSubjectId>
                        <!--Optional:-->
                        <xsd1:localClaimDialect>true</xsd1:localClaimDialect>
                    </xsd1:claimConfig>
                    <!--Optional:-->
                    <xsd1:description>sample service provider</xsd1:description>
                    <!--Optional:-->
                    <xsd1:inboundAuthenticationConfig>
                        <!--Zero or more repetitions:-->
                        <xsd1:inboundAuthenticationRequestConfigs>
                            <!--Optional:-->
                            <xsd1:inboundAuthKey>saml2-web-app-dispatch.com</xsd1:inboundAuthKey>
                            <!--Optional:-->
                            <xsd1:inboundAuthType>samlsso</xsd1:inboundAuthType>
                            <!--Zero or more repetitions:-->
                            <xsd1:properties>
                                <!--Optional:-->
                                <xsd1:name>attrConsumServiceIndex</xsd1:name>
                                <!--Optional:-->
                                <xsd1:value>1223160755</xsd1:value>
                            </xsd1:properties>
                        </xsd1:inboundAuthenticationRequestConfigs>
                    </xsd1:inboundAuthenticationConfig>
                    <!--Optional:-->
                    <xsd1:inboundProvisioningConfig>
                        <!--Optional:-->
                        <xsd1:provisioningEnabled>false</xsd1:provisioningEnabled>
                        <!--Optional:-->
                        <xsd1:provisioningUserStore>PRIMARY</xsd1:provisioningUserStore>
                    </xsd1:inboundProvisioningConfig>
                    <!--Optional:-->
                    <xsd1:localAndOutBoundAuthenticationConfig>
                        <!--Optional:-->
                        <xsd1:alwaysSendBackAuthenticatedListOfIdPs>false</xsd1:alwaysSendBackAuthenticatedListOfIdPs>
                        <!--Optional:-->
                        <xsd1:authenticationStepForAttributes></xsd1:authenticationStepForAttributes>
                        <!--Optional:-->
                        <xsd1:authenticationStepForSubject></xsd1:authenticationStepForSubject>
                        <xsd1:authenticationType>default</xsd1:authenticationType>
                        <!--Optional:-->
                        <xsd1:subjectClaimUri>http://wso2.org/claims/fullname</xsd1:subjectClaimUri>
                    </xsd1:localAndOutBoundAuthenticationConfig>
                    <!--Optional:-->
                    <xsd1:outboundProvisioningConfig>
                        <!--Zero or more repetitions:-->
                        <xsd1:provisionByRoleList></xsd1:provisionByRoleList>
                    </xsd1:outboundProvisioningConfig>
                    <!--Optional:-->
                    <xsd1:permissionAndRoleConfig></xsd1:permissionAndRoleConfig>
                    <!--Optional:-->
                    <xsd1:saasApp>${saasApp}</xsd1:saasApp>
                </xsd:serviceProvider>
            </xsd:updateApplication>
        </soapenv:Body>
    </soapenv:Envelope>" >> .bootstrap/update-sp-"${sp_name}".xml

    echo
    echo "Updating application ${sp_name}..."

    curl -s \
         -k \
         -d @.bootstrap/update-sp-"${sp_name}".xml \
         -H "Authorization: Basic ${auth}" \
         -H "Content-Type: text/xml" \
         -H "SOAPAction: urn:updateApplication" \
         -o /dev/null \
         https://localhost:${WSO2_IS_TEMP_PORT}/services/IdentityApplicationManagementService.IdentityApplicationManagementServiceHttpsSoap11Endpoint/

    res=$?
    if test "${res}" != "0"; then
        echo "!! Problem occurred while updating application ${sp_name}.... !!"
        echo "${res}"
        return 255
    fi
    echo
    echo "** Successfully updated the application ${sp_name}. **"

    return 0;
}

function create_google_oauth2_identity_provider() {
    idp_name=$1
    username=$2
    password=$3
    idp_host=$4
    google_client_id=$5
    google_client_secret=$6
    google_oauth_scopes=$7
    config_file=/bootstrap/template/create-idp-google.xml

    auth=$(echo "${username}:${password}" | base64)

    if [ ! -d ".bootstrap" ]; then
        mkdir .bootstrap
    fi

    if [ ! -f "${config_file}" ]; then
        echo "${config_file} File does not exists."
        return 255
    fi

    if [ -f .bootstrap/create-idp-google-"${idp_name}".xml ]; then
        rm .bootstrap/create-idp-google-"${idp_name}".xml
    fi

    cp ${config_file} .bootstrap/create-idp-google-"${idp_name}".xml

    sed -i -e 's/${IDP_NAME}/'${idp_name}'/g' \
        -e 's/${IDP_HOST}/'${idp_host}'/g' \
        -e 's/${GOOGLE_CLIENT_ID}/'${google_client_id}'/g' \
        -e 's/${GOOGLE_CLIENT_SECRET}/'${google_client_secret}'/g' \
        -e 's/${GOOGLE_OAUTH_SCOPES}/'${google_oauth_scopes}'/g' \
        .bootstrap/create-idp-google-"${idp_name}".xml

    echo "*** Creating Identity Provider ${idp_name}..."

    CURL_RES=$(curl -s \
         -k \
         -w "%{http_code}" \
         -o .bootstrap/create-idp-google-"${idp_name}"-response.xml \
         -d @.bootstrap/create-idp-google-"${idp_name}".xml \
         -H "Authorization: Basic ${auth}" \
         -H "Content-Type: text/xml" \
         -H "SOAPAction: urn:addIdp" \
         "https://localhost:${WSO2_IS_TEMP_PORT}/services/IdentityProviderMgtService?wsdl")

    if [[ ! -z "$CURL_RES" && "$CURL_RES" -eq "200" ]]; then
        echo "** Google-based Identity Provider ${idp_name} successfully created. **"

        return 0;
    fi

    echo "!! Problem occurred while creating the Google-based identity provider: ${idp_name} !!"
    echo "${CURL_RES}"
    return 255
}

function export_wso2_certificate() {
    # clean temp directory in case something is there
    rm .bootstrap/wso2.pem 2> /dev/null || true

    mkdir .bootstrap 2> /dev/null || true

    openssl s_client \
        -showcerts \
        -connect localhost:${WSO2_IS_TEMP_PORT} \
        < /dev/null \
        2> /dev/null \
        | openssl x509 -outform PEM > .bootstrap/wso2.pem
}

function generate_wso2_certificate() {
    # clean temp directory in case something is in there
    mkdir .bootstrap 2>/dev/null || true
    rm .bootstrap/wso2carbon.jks 2>/dev/null || true
    rm .bootstrap/wso2carbon.p12 2>/dev/null || true
    rm .bootstrap/wso2.pem 2>/dev/null || true

    keytool -genkey \
            -alias wso2carbon \
            -keyalg RSA \
            -keysize 2048 \
            -keystore .bootstrap/wso2carbon.jks \
            -dname "CN=idp,OU=CNIT,O=GUARD,L=Bari,S=BA,C=IT" \
            -storepass wso2carbon \
            -keypass wso2carbon \
            &>/dev/null

    keytool -importkeystore \
            -srckeystore .bootstrap/wso2carbon.jks \
            -destkeystore .bootstrap/wso2carbon.p12 \
            -srcstoretype jks \
            -srcstorepass wso2carbon \
            -deststoretype pkcs12 \
            -deststorepass wso2carbon \
            &>/dev/null

    openssl pkcs12 \
            -in .bootstrap/wso2carbon.p12 \
            -passin pass:wso2carbon \
            -passout pass:wso2carbon \
    | openssl x509 -outform PEM > .bootstrap/wso2.pem
}

function register_oauth_service_provider() {
    SP_NAME=$1
    WSO2_USERNAME=$2
    WSO2_PASSWORD=$3
    SP_OAUTH2_KEY=$4
    SP_OAUTH2_SECRET=$5

    create_service_provider $SP_NAME $WSO2_USERNAME $WSO2_PASSWORD /bootstrap/template/create-sp.xml
    SP_ID=$(get_service_provider_id $SP_NAME $WSO2_USERNAME $WSO2_PASSWORD /bootstrap/template/get-application.xml)
    configure_service_provider_oauth $SP_NAME $WSO2_USERNAME $WSO2_PASSWORD /bootstrap/template/register-oauth-app.xml \
        'https://localhost/callback' \
        $SP_OAUTH2_KEY \
        $SP_OAUTH2_SECRET
    activate_service_provider_oauth $SP_NAME $WSO2_USERNAME $WSO2_PASSWORD /bootstrap/template/update-application.xml \
        $SP_OAUTH2_KEY \
        $SP_OAUTH2_SECRET \
        $SP_ID
}

function register_oauth_service_provider_with_callback() {
    SP_NAME=$1
    WSO2_USERNAME=$2
    WSO2_PASSWORD=$3
    SP_OAUTH2_KEY=$4
    SP_OAUTH2_SECRET=$5
    CALLBACK_URL=$6

    create_service_provider $SP_NAME $WSO2_USERNAME $WSO2_PASSWORD /bootstrap/template/create-sp.xml
    SP_ID=$(get_service_provider_id $SP_NAME $WSO2_USERNAME $WSO2_PASSWORD /bootstrap/template/get-application.xml)
    configure_service_provider_oauth $SP_NAME $WSO2_USERNAME $WSO2_PASSWORD /bootstrap/template/register-oauth-app.xml \
        $CALLBACK_URL \
        $SP_OAUTH2_KEY \
        $SP_OAUTH2_SECRET
    activate_service_provider_oauth $SP_NAME $WSO2_USERNAME $WSO2_PASSWORD /bootstrap/template/update-application.xml \
        $SP_OAUTH2_KEY \
        $SP_OAUTH2_SECRET \
        $SP_ID
}

function register_google_oauth_service_provider_with_callback() {
    SP_NAME=$1
    WSO2_USERNAME=$2
    WSO2_PASSWORD=$3
    SP_OAUTH2_KEY=$4
    SP_OAUTH2_SECRET=$5
    CALLBACK_URL=$6
    IDP_NAME=$7

    create_service_provider $SP_NAME $WSO2_USERNAME $WSO2_PASSWORD /bootstrap/template/create-sp.xml
    SP_ID=$(get_service_provider_id $SP_NAME $WSO2_USERNAME $WSO2_PASSWORD /bootstrap/template/get-application.xml)
    configure_service_provider_oauth $SP_NAME $WSO2_USERNAME $WSO2_PASSWORD /bootstrap/template/register-oauth-app.xml \
        $CALLBACK_URL \
        $SP_OAUTH2_KEY \
        $SP_OAUTH2_SECRET
    activate_service_provider_oauth_google $SP_NAME $WSO2_USERNAME $WSO2_PASSWORD /bootstrap/template/update-application-google.xml \
        $SP_OAUTH2_KEY \
        $SP_OAUTH2_SECRET \
        $SP_ID \
        $IDP_NAME
}
