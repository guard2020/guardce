export $(cat ../.env | xargs)
bash cb-manager/start_config.sh $GUARD_SERVER_ADDRESS $CB_MAN_PORT $LCP_PORT $ELASTIC_PORT_1 $KIBANA_PORT 
