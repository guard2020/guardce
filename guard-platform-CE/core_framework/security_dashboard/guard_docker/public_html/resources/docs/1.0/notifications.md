# Threat Notification & Anomalies Analysis

---

- [Description](#threat)
- [Notifications Workflow](#workflow)
- [Filtering Notifications](#filtering)
- [Anomalies Analysis](#anomalies)

<a name="threat"></a>
## Description
The threat notification alerts the user when new threats have been identified by the different agent instances that are 
currently deployed and started in the various environments in the GUARD framework. The threats are listed in a table 
and threat information such as agent source, severity, description, data and datetime are displayed. 
By default the table is order by most recent notification. Below you can be see an example of the **Threat Notification** page

![image info](../../../images/docs/notifications.png)


<a name="workflow"></a>
## Notifications Workflow
The workflow of the notifications starts when new security pipelines are created and started. 

1. When pipelines are started, the Security Controller deploys the agent(s) and/or algorithm(s) in the specific environment. Some of these
algorithms and agents will identify threats and alert the system by sending messages.
2. These messages are sent to specific topics of the GUARD event streaming software, kafka. 
3. A configured logstash instanced that is listening to these specific kafka topics, stores the received messages in the elasticsearch
engine available in the GUARD framework.
4. The Dashboard is actively listening to new entries in the elasticsearch database, and is able to display the newly created threat notifications
in the **Threat Notification** page. A new alert message is also displayed in the page to the user every time new notifications appear.

<a name="filtering"></a>
##Filtering Notifications
The filter section is located in the left side of the table. The user is able to search for a specific content or message, or 
filter by source. The filter by source will display the currently existing sources in the table.

<a name="anomalies"></a>
## Anomalies Analysis
The Anomalies Analysis is also responsible for alerting and informing the user of any threat or anomaly. Supported by different
visualisations, the **Anomalies Analysis** page presents encountered anomalies by some agents and algorithms in the environment.
