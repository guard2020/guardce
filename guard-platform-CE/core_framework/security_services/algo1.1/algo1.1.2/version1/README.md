This application is made for ASTRID and GUARD H2020 projects.

Pre-requisites:
1) nProbe or vDPI local agent: This app ingests incoming traffic flow data using nProbe or vDPI. 
2) Kafka channel: This app listens to Kafka for the data flow sent by nProbe/vDPI. 
3) Docker: This app is deployed as a docker image.

NOTE: The application can run without the agent and the channel but it will not output anything.

To run this app:
1) Set the correct Kafka Channels: Edit deployment.py line 15 for incoming (data from local agent) and line 16 outgoing (output to Dashboard).
1) Simply create the docker application: docker build -t cnit_ml . 
3) Simply run the docker application: docker run cnit_ml



VERSION DESCRIPTION:
1) This application detects DDoS LOIC attacks using Random Forests
2) This application only needs FLOW_DURATION_MICROSECONDS, IN_PKTS, OUT_PKTS, IN_BYTES, OUT_BYTES, SHORTEST_FLOW_PKT,LONGEST_FLOW_PKT from the local agents


The methodology of this work is described in the following published papers (even though we use CICflowmeter as an agent in the papers):

O. R. Sanchez, M. Repetto, A. Carrega, R. Bolla, and J. F. Pajo, "Feature Selection Evaluation towards a Lightweight Deep Learning DDoS Detector", In Proc. of the 2021 IEEE International Conference on Communications (ICC), Montreal, Canada (virtual), pp.1-6 (2021)
O. R. Sanchez, M. Repetto, A. Carrega, and R. Bolla, "Evaluating ML-based DDoS Detection with Grid Search Hyperparameter Optimization", In Proc. of the 3rd International Workshop on Cyber-Security Threats, Trust and Privacy management in Software-defined and Virtualized Infrastructures (SecSOFT), Tokyo, Japan (virtual), pp.1-7 (2021)