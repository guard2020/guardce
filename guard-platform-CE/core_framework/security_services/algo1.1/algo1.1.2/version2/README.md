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



The nProbe/vDPI data features include IN_BYTES, IN_PKTS, TCP_FLAGS, IPV4_SRC_MASK, INPUT_SNMP, IPV4_DST_MASK, OUTPUT_SNMP, OUT_BYTES, OUT_PKTS, MIN_IP_PKT_LEN, MAX_IP_PKT_LEN,
IPV6_SRC_MASK, IPV6_DST_MASK, SAMPLING_INTERVAL, FLOW_ACTIVE_TIMEOUT, FLOW_INACTIVE_TIMEOUT, TOTAL_BYTES_EXP, TOTAL_PKTS_EXP, TOTAL_FLOWS_EXP,  MIN_TTL,  MAX_TTL,
PACKET_SECTION_OFFSET,  SAMPLED_PACKET_SIZE, FLOW_DURATION_MICROSECONDS, SAMPLING_SIZE, FRAME_LENGTH, PACKETS_OBSERVED, PACKETS_SELECTED, SRC_FRAGMENTS, DST_FRAGMENTS,
CLIENT_NW_LATENCY_MS, SERVER_NW_LATENCY_MS, CLIENT_TCP_FLAGS, SERVER_TCP_FLAGS, APPL_LATENCY_MS, SRC_TO_DST_MAX_THROUGHPUT, SRC_TO_DST_MIN_THROUGHPUT, SRC_TO_DST_AVG_THROUGHPUT,
DST_TO_SRC_MAX_THROUGHPUT, DST_TO_SRC_MIN_THROUGHPUT, DST_TO_SRC_AVG_THROUGHPUT, NUM_PKTS_UP_TO_128_BYTES, NUM_PKTS_128_TO_256_BYTES, NUM_PKTS_256_TO_512_BYTES, NUM_PKTS_512_TO_1024_BYTES,
NUM_PKTS_1024_TO_1514_BYTES, NUM_PKTS_OVER_1514_BYTES, LONGEST_FLOW_PKT, SHORTEST_FLOW_PKT, RETRANSMITTED_IN_BYTES, RETRANSMITTED_IN_PKTS, RETRANSMITTED_OUT_BYTES, RETRANSMITTED_OUT_PKTS,
OOORDER_IN_PKTS, OOORDER_OUT_PKTS, UNTUNNELED_PROTOCOL, NUM_PKTS_TTL_EQ_1, NUM_PKTS_TTL_2_5, NUM_PKTS_TTL_5_32, NUM_PKTS_TTL_32_64, NUM_PKTS_TTL_64_96, NUM_PKTS_TTL_96_128, NUM_PKTS_TTL_128_160,
NUM_PKTS_TTL_160_192, NUM_PKTS_TTL_192_224, NUM_PKTS_TTL_224_255, DURATION_IN, DURATION_OUT, TCP_WIN_MIN_IN, TCP_WIN_MAX_IN, TCP_WIN_MSS_IN, TCP_WIN_SCALE_IN,TCP_WIN_MIN_OUT, TCP_WIN_MAX_OUT,
TCP_WIN_MSS_OUT, TCP_WIN_SCALE_OUT, ENTROPY_CLIENT_BYTES, ENTROPY_SERVER_BYTES