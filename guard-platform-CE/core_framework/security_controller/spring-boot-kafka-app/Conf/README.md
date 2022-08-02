# Smart Controller
This project uses Spring Boot and Maven. It is a Drools Rule Engine. 
A sample Drools rule is located in folder 'src/main/resources/rules/rules.drl'. More rules can be added there.

### How to run
There is a Dockerfile, according to which, a jar file is created and then run. 

### Test scripts
In the folder testScripts two scripts can be found that test the two REST APIs that the project exposes.
getVulnerabilityMeasurementPolicy.sh sends a VulnerabilityMeasurementPolicy to http://127.0.0.1:9000/gfg/getVulnerabilityMeasurementPolicy, a Drools rule is fired and the sent policy is printed in the console.

publishTopologyChanges.sh sends a topology change to http://127.0.0.1:9000/gfg/publishTopologyChange, and this change is sent to the TopologyChanges topic to a Kafka instance.
(Kafka must run a priori)

### Test with postman 
1) postman can be used in order to send a policy.

post http://127.0.0.1:9000/gfg/getVulnerabilityMeasurementPolicy
Content-Type:application/json

Body
````
{
    "component":"testComponent",
    "staticScanPath":"staticPath",
    "dynamicScanURL":"dynamicURL",
    "scanningFrequency":"frequency"
}
````

The response, for now, is the policy itself.

2) postman can be used in order to send a topology change.

post http://127.0.0.1:9000/gfg/publishTopologyChange
Content-Type:application/json

Body
````
{
     "node": "ITE29102020-2",
     "modificationType": "ITE29102020-2",
     "nodeData": "ITE29102020-2"
}
````

The change can be seen in the Topic.