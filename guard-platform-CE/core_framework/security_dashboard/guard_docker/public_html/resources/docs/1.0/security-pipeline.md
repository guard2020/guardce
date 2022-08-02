# Security Pipeline

---

- [Description](#description)
- [Create Pipeline](#create)
- [List of Pipelines](#list)
- [Update Pipeline](#update)
- [Delete Pipeline](#delete)
- [Reload Pipeline](#reload)
- [Start/Stop Pipeline](#start)
- [Filter and Ordering](#filtering)

<a name="description"></a>
## Description
Security pipeline is the core of the GUARD Dashboard. Here, the user will be able to create and manage the different pipelines
that control over the deployed agents and algorithms in the GUARD environments. This way, all agents and algorithms and its configurations
can be centrally controlled and managed by the user from the Dashboard. 

<a name="create"></a>
## Create Pipeline
The create pipeline feature enables the creation of a security pipeline which centrally manages one or more agent instances 
in different environments. The user is required to give the pipeline a name, select one agent and one or multiple instances 
of the selected agent. The available configuration parameters for each instance can be entered by the user and a configuration 
file can be uploaded. An example of the information needed to create a pipeline can be seen in the image below, which is the same 
information as updating a pipeline.

![image info](../../../images/docs/pipeline_edit_page_with_agent_panel.png)

<a name="list"></a>
## List  of Pipelines

The user can view all existing pipelines in a tabular format. The information available are pipeline id, name, agent, 
creation date, last updated date, creator user, current pipeline status, and actions that allow the user to edit, delete 
and reload the pipeline configuration. An example of such a table can be seen in the image below. 

![image info](../../../images/docs/pipeline_list_page.png)

<a name="update"></a>
## Update Pipeline

The user can update the existing pipeline information, change agents and agent instances, and update the configuration information.

<a name="delete"></a>
## Delete Pipeline

The user can delete an existing pipeline by using the delete button in the pipeline table under the action column.

<a name="reload"></a>
## Reload Pipeline

The reload pipeline feature provides the user the ability to reload the agent instances configuration for the selected pipeline.

<a name="start"></a>
## Start/Stop Pipeline

The start and stop security pipeline features allow the user to start and stop pipelines which supported by the security 
controller start or stop the agent instances deployed in the various environments. This feature is available as a button 
under the pipeline table. Depending on the status of the pipeline (created, started, stopped) the available action is 
shown to the user.

<a name="filtering"></a>
## Filtering and Ordering
The filtering feature provides the user with a search field and gives the ability to search a security pipeline. 
The different table columns id, name, agents, created date, modified date and creator are matched with the user search input.

The ordering feature allows the user to sort the table by a column and by ascending or descending order.
User can sort the table by the columns id, name, agents, created dated, modified date, creator and status.