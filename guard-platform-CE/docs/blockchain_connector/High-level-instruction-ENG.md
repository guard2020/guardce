
# Set up steps

1. Setup four CA's:
- Two TLS CA's (used to issue TLS certificates) one for peers and other for orderers..
- Two Root CA's (used to issue identity certificates) one for peers and other for orderers 

2. Download all created CA's certificates.

3. We need to enroll users for created CA's (download crypto materials) - it is required to authenticate requests for CA.

4. For each CA we need to register new users:
- For Orderers TLS CA and RCA we need to register each orderer (orderer1, orderer2, orderer3) and orderer admin (requried for interaction with orderers)
- For Peers TLS CA and RCA we need to register each peer (peer1, peer2, peer3), peers admin (requried for interaction with peers) and user for api (requried for blockchain connector)

5. For each register user we need to enroll it- download crypto materials

6. Based on crytpo materials generate genesis.block and channel tx requried to create channel for chaincode.

7. Based on generted crytpo materials set up all orderers

8. Based on generted crytpo materials set up all peers

9. Build and prepare chaincode packaging

10. Using peers CLI install chaincode packaging on all peers. 

11. Set up chaincode containers using installed packagingid 

12. Using peers CLI approve and initialize chaincode

13. All CA's can be stopped. CA's must be runing only when we need to be able to interact with it e.g. registration and enrollment.

14. Set up blockchain connector

If channel configuration requried update, we need to use orderers admin account. When new orderer or peer will be added, new user must be registered and enrolled (in the TLS CA and the RCA). After that we need to fetch current channel configuration, manually modified it, and create update channel configuration transaction (using orderers cli)

