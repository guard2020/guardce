FROM wso2/wso2is:5.7.0

COPY ./bootstrap /bootstrap
COPY ./idp.jks /home/wso2carbon/wso2is-5.7.0/repository/resources/security/wso2carbon.jks

ENTRYPOINT /bootstrap/bootstrap.sh
