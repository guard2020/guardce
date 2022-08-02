This application is made for ASTRID and GUARD H2020 projects.

Pre-requisites:
1) nProbe or vDPI local agent: This app ingests incoming traffic flow data using nProbe or vDPI. 
2) Kafka channel: This app listens to Kafka for the data flow sent by nProbe/vDPI. 
3) Docker: This app is deployed as a docker image.

NOTE: The application can run without the agent and the channel but it will not output anything.

To run this app:
1) Set the correct Kafka Channels: Edit deployment.py line 17 for incoming (data from local agent) and line 18 outgoing (output to Dashboard).
1) Simply create the docker application: docker build -t cnit_ml . 
3) Simply run the docker application: docker run cnit_ml



VERSION DESCRIPTION (improvement over the previous):
1) This application detects multiple DDoS attacks (multiclass) using Random Forests
2) This application uses multiple data from nProbe/vDPI (see below if needed)
3) This application sends only an aggregated output to the dashboard every minute


The methodology of this work is described in the following published papers (even though we use CICflowmeter as an agent in the papers):

O. R. Sanchez, M. Repetto, A. Carrega, R. Bolla, and J. F. Pajo, "Feature Selection Evaluation towards a Lightweight Deep Learning DDoS Detector", In Proc. of the 2021 IEEE International Conference on Communications (ICC), Montreal, Canada (virtual), pp.1-6 (2021)
O. R. Sanchez, M. Repetto, A. Carrega, and R. Bolla, "Evaluating ML-based DDoS Detection with Grid Search Hyperparameter Optimization", In Proc. of the 3rd International Workshop on Cyber-Security Threats, Trust and Privacy management in Software-defined and Virtualized Infrastructures (SecSOFT), Tokyo, Japan (virtual), pp.1-7 (2021)


The nProbe/vDPI data features include 'TCP_WIN_MIN_IN', 'TCP_WIN_MAX_IN', 'MIN_IP_PKT_LEN', 'NUM_PKTS_TTL_96_128', 'NUM_PKTS_UP_TO_128_BYTES', 'IN_PKTS', 'IN_BYTES', 'TCP_WIN_SCALE_IN', 'TCP_WIN_MSS_OUT', 'DURATION_IN','FLOW_DURATION_MICROSECONDS', 'TCP_WIN_SCALE_OUT', 'MIN_TTL', 'MAX_TTL', 'TCP_WIN_MIN_OUT', 'TCP_WIN_MAX_OUT', 'SHORTEST_FLOW_PKT', 'NUM_PKTS_TTL_224_255', 'TCP_WIN_MSS_IN', 'LONGEST_FLOW_PKT'],