
### Table of Contents
- [GUARD Integration](#orgd7746c8)
  + [Execution Environment](#org8feac27)
    - [Create](#orgc9d244b)
    - [List](#orge435360)
    - [Delete](#orgd7971eb)
  + [Agent Catalog](#org4c45d39)
    - [Create](#org66802d2)
    - [List](#org831beac)
    - [Update](#org5722d6b)
    - [Delete](#org3b42bd0)
  + [Agent Instance](#org13b75d4)
    - [Create](#org42da8cd)
    - [History](#orgaca9076)
    - [Start, Stop, Restart, Reload](#orgda0a1b3)
    - [Configuration](#orgd38b527)



<a id="orgd7746c8"></a>

# GUARD Integration

This document contains information about agent&rsquo;s integration with GUARD platform.
Integration is performed through [Context Broker Manager](https://guard-cb-manager.readthedocs.io/en/latest) API and [Local Control Plane](https://guard-lcp.readthedocs.io/en/latest)
(behind the scenes) running on the same environment as the agent.

`LCP` requires a files system access to the agent&rsquo;s application for changing
the configuration files and running `pgafilterctl` script that starts, stops,
restarts and reloads the agent daemon.

To integration agent into GUARD platform the necessary steps are:

1.  Create Execution Environment - information on where the agent is running.
2.  Create Agent Catalog - schema with information on agent&rsquo;s configuration and possible actions.
3.  Create Agent Instance - information on agent from Agent Catalog running on Execution Environment.


<a id="org8feac27"></a>

## Execution Environment

Execution Environment is an environment with `LCP` and `pgafilter` agent running.
`LCP` must be running as it provides the interface to control the agent through `CB`.


<a id="orgc9d244b"></a>

### Create

Execution Environments are created with:

1.  `production` environment - Google Cloud

        POST :CB_URL/exec-env
        :COMMON_HEADERS
        
        {
            "description": "PGA Filter GUARD agent [production environment]",
            "enabled": true,
            "hostname": "35.193.65.139",
            "id": "pgafilter-production",
            "lcp": {
                "port": 4000,
                "https": false
            },
            "partner": "nask",
            "stage": "production",
            "type_id": "vm"
        }

2.  `testing` environment - proxying through Google Cloud

        POST :CB_URL/exec-env
        :COMMON_HEADERS
        
        {
            "description": "PGA Filter GUARD agent [testing environment]",
            "enabled": true,
            "hostname": "35.193.65.139",
            "id": "pgafilter-testing",
            "lcp": {
                "port": 4444,
                "https": false
            },
            "partner": "nask",
            "stage": "testing",
            "type_id": "vm"
        }

3.  `backup` environment - ItalTel (Azure)

        POST :CB_URL/exec-env
        :COMMON_HEADERS
        
        {
            "description": "PGA Filter GUARD agent [backup environment]",
            "enabled": true,
            "hostname": "20.52.36.121",
            "id": "pgafilter-backup",
            "lcp": {
                "port": 4000,
                "https": false
            },
            "partner": "nask",
            "stage": "backup",
            "type_id": "vm"
        }

4.  `demo` environment - 8Bells

        POST :CB_URL/exec-env
        :COMMON_HEADERS
        
        {
            "description": "8Bells router demo environment [vDPI + PGAfilter]",
            "enabled": true,
            "hostname": "212.31.108.19",
            "id": "8bells-guard-uc2-router",
            "lcp": {
                "port": 4001,
                "https": false
            },
            "partner": "8bells",
            "stage": "production",
            "type_id": "vm"
        }


<a id="orge435360"></a>

### List

The list of Execution Environments is returned by the query:

    GET :CB_URL/exec-env
    :COMMON_HEADERS
    
    {
        "where": {
            "equals": {
                "target": "partner",
                "expr": "nask"
            }
        }
    }

    GET :CB_URL/exec-env
    :COMMON_HEADERS
    
    {
        "where": {
            "equals": {
                "target": "partner",
                "expr": "8bells"
            }
        }
    }


<a id="orgd7971eb"></a>

### Delete

Execution Environments can be removed with:

    DELETE :CB_URL/exec-env/pgafilter-testing
    :COMMON_HEADERS

or to delete all `nask` environments:

    DELETE :CB_URL/exec-env
    :COMMON_HEADERS
    
    {
        "where": {
            "equals": {
                "target": "partner",
                "expr": "nask"
            }
        }
    }


<a id="org4c45d39"></a>

## Agent Catalog

The schema with GUARD agent configuration and interaction possibilities.

-   creation,
-   listing and overview
-   updating,
-   deleting.


<a id="org66802d2"></a>

### Create

The `pgafilter` agent schema in the catalog.

-   actions by `pgafilterctl` script
    -   start
    -   stop
    -   restart
    -   reload
-   resources configuration files
    -   config.yml main configuration file (`id`: config-file)
    -   rules.yml rules file with signatures (`id`: rule-file)
-   parameters by configuration file
    -   config-file
        -   rules - enabled rules by: `key`, `sid` or specials like `all`
        -   kafka-interval - interval of Kafka messages in seconds
        -   kafka-topic - kafka topic for sending the messages
    -   rule-file
        -   release - release date of the rule file (version)

whole agent catalog declaration:

    POST :CB_URL/catalog/agent/:AGENT
    :COMMON_HEADERS
    
    {
      "actions": [
        {
          "config": {
            "cmd": "pgafilterctl start"
          },
          "status": "started",
          "id": "start"
        },
        {
          "config": {
            "cmd": "pgafilterctl stop"
          },
          "status": "stopped",
          "id": "stop"
        },
        {
          "config": {
            "cmd": "pgafilterctl restart"
          },
          "status": "started",
          "id": "restart"
        },
        {
          "config": {
            "cmd": "pgafilterctl reload"
          },
          "status": "started",
          "id": "reload"
        }
      ],
      "resources": [
        {
          "example": "interface: pgafilter\nkafka_server:\n- guard2-svc.westeurope.cloudapp.azure.com:29092\nkafka_topic: detection-results\nlog:\n-  format: no_place\n   level: INFO\n   sink: /opt/pgafilter/pgafilter.log\nrules:\n  - \"tcp-seq_is_tcp-ack\"\n  - \"tcp-sport_is_tcp-seq:0:1\"\n  - \"tcp-sport_is_tcp-seq:2:3\"\n  - \"tcp-dport_is_tcp-seq:0:1\"\n  - \"tcp-dport_is_tcp-seq:2:3\"\n  - \"ip-dst:2_xor_ip-id:0_is_tcp-seq:2\"\n",
          "config": {
            "path": "/opt/pgafilter/config.yml"
          },
          "description": "pgafilter configuration file",
          "id": "config-file"
        },
        {
          "example": "release: 2021-03-01T12:46:01\nrules:\n  tcp-seq_is_tcp-ack:\n    sid: 1001\n    rev: 1\n    args:\n      - tcphdr\n    content: \"tcphdr->syn && !tcphdr->ack && tcphdr->seq == tcphdr->ack_seq\"\n    comment: \"TCP-SYN and TCP SEQ is equal to TCP ACK\"\n  tcp-ack_nonzero:\n    sid: 1002\n    rev: 1\n    args: [tcphdr]\n    content: \"tcphdr->syn && !tcphdr->ack && tcphdr->ack_seq != 0\"\n    comment: \"TCP-SYN and TCP ACK is non-zero\"\n  tcp-sport_is_tcp-seq:0:1:\n    sid: 1003\n    rev: 1\n    args: [tcphdr, iphdr]\n    content: \"tcphdr->source == pga_2_byte32(tcphdr->seq 0 1)\"\n",
          "config": {
            "path": "/opt/pgafilter/rules.yml"
          },
          "description": "pgafilter PGA signatures/rules file",
          "id": "rule-file"
        }
      ],
      "parameters": [
        {
          "config": {
            "path": [
              "rules"
            ],
            "source": "/opt/pgafilter/config.yml",
            "schema": "yaml"
          },
          "description": "Enabled rules",
          "example": "['rule1', 'rule2', 'rule3']",
          "list": "true",
          "type": "string",
          "id": "rules"
        },
        {
          "config": {
            "path": [
              "poll_interval"
            ],
            "source": "/opt/pgafilter/config.yml",
            "schema": "yaml"
          },
          "description": "Polling interval  in seconds (also interval for of Kafka messages)",
          "example": 1,
          "type": "integer",
          "id": "poll-interval"
        },
        {
          "config": {
            "path": [
              "kafka_topic"
            ],
            "source": "/opt/pgafilter/config.yml",
            "schema": "yaml"
          },
          "description": "Kafka topic for sending the messages",
          "example": "data-pgafilter",
          "type": "string",
          "id": "kafka-topic"
        },
        {
          "config": {
            "path": [
              "release"
            ],
            "source": "/opt/pgafilter/rules.yml",
            "schema": "yaml"
          },
          "description": "Release date of the rule file (version)",
          "example": "2021-03-01T12:46:01",
          "type": "string",
          "id": "release"
        }
      ],
      "description": "eBPF+XDP rule-based detection of packets generated with PGA",
      "id": ":AGENT"
    }


<a id="org831beac"></a>

### List

To get the schema for specific agent:

    GET :CB_URL/catalog/agent/:AGENT
    :COMMON_HEADERS

It is possible to select and filter the results using the following request body
(redundancy of *id* and *partner* to show an example):

    GET :CB_URL/catalog/agent
    :COMMON_HEADERS
    
    {
      "select": [
        "parameters"
      ],
      "where": {
        "equals": {
          "target": "id",
          "expr": ":AGENT"
        },
        "equals": {
          "target": "partner",
          "expr": "nask"
        }
      }
    }


<a id="org5722d6b"></a>

### Update

Modification of specific actions/parameters/resources is possible, but
sending the whole configuration with applied changes is strongly encouraged.

    PUT :CB_URL/catalog/agent/:AGENT
    :COMMON_HEADERS
    
    {
      "actions": [
        {
          "config": {
            "cmd": "pgafilterctl start"
          },
          "status": "started",
          "id": "start"
        },
        {
          "config": {
            "cmd": "pgafilterctl stop"
          },
          "status": "stopped",
          "id": "stop"
        },
        {
          "config": {
            "cmd": "pgafilterctl restart"
          },
          "status": "started",
          "id": "restart"
        },
        {
          "config": {
            "cmd": "pgafilterctl reload"
          },
          "status": "started",
          "id": "reload"
        }
      ],
      "resources": [
        {
          "example": "interface: pgafilter\nkafka_server:\n- guard2-svc.westeurope.cloudapp.azure.com:29092\nkafka_topic: detection-results\nlog:\n-  format: no_place\n   level: INFO\n   sink: /opt/pgafilter/pgafilter.log\nrules:\n  - \"tcp-seq_is_tcp-ack\"\n  - \"tcp-sport_is_tcp-seq:0:1\"\n  - \"tcp-sport_is_tcp-seq:2:3\"\n  - \"tcp-dport_is_tcp-seq:0:1\"\n  - \"tcp-dport_is_tcp-seq:2:3\"\n  - \"ip-dst:2_xor_ip-id:0_is_tcp-seq:2\"\n",
          "config": {
            "path": "/opt/pgafilter/config.yml"
          },
          "description": "pgafilter configuration file",
          "id": "config-file"
        },
        {
          "example": "release: 2021-03-01T12:46:01\nrules:\n  tcp-seq_is_tcp-ack:\n    sid: 1001\n    rev: 1\n    args:\n      - tcphdr\n    content: \"tcphdr->syn && !tcphdr->ack && tcphdr->seq == tcphdr->ack_seq\"\n    comment: \"TCP-SYN and TCP SEQ is equal to TCP ACK\"\n  tcp-ack_nonzero:\n    sid: 1002\n    rev: 1\n    args: [tcphdr]\n    content: \"tcphdr->syn && !tcphdr->ack && tcphdr->ack_seq != 0\"\n    comment: \"TCP-SYN and TCP ACK is non-zero\"\n  tcp-sport_is_tcp-seq:0:1:\n    sid: 1003\n    rev: 1\n    args: [tcphdr, iphdr]\n    content: \"tcphdr->source == pga_2_byte32(tcphdr->seq 0 1)\"\n",
          "config": {
            "path": "/opt/pgafilter/rules.yml"
          },
          "description": "pgafilter PGA signatures/rules file",
          "id": "rule-file"
        }
      ],
      "parameters": [
        {
          "config": {
            "path": [
              "rules"
            ],
            "source": "/opt/pgafilter/config.yml",
            "schema": "yaml"
          },
          "description": "Enabled rules",
          "example": "['rule1', 'rule2', 'rule3']",
          "list": "true",
          "type": "string",
          "id": "rules"
        },
        {
          "config": {
            "path": [
              "poll_interval"
            ],
            "source": "/opt/pgafilter/config.yml",
            "schema": "yaml"
          },
          "description": "Polling interval  in seconds (also interval for of Kafka messages)",
          "example": 1,
          "type": "integer",
          "id": "poll-interval"
        },
        {
          "config": {
            "path": [
              "kafka_topic"
            ],
            "source": "/opt/pgafilter/config.yml",
            "schema": "yaml"
          },
          "description": "Kafka topic for sending the messages",
          "example": "data-pgafilter",
          "type": "string",
          "id": "kafka-topic"
        },
        {
          "config": {
            "path": [
              "release"
            ],
            "source": "/opt/pgafilter/rules.yml",
            "schema": "yaml"
          },
          "description": "Release date of the rule file (version)",
          "example": "2021-03-01T12:46:01",
          "type": "string",
          "id": "release"
        }
      ],
      "description": "eBPF+XDP rule-based detection of packets generated with PGA",
      "id": ":AGENT"
    }


<a id="org3b42bd0"></a>

### Delete

Remove a single agent schema from catalog with specific `id`

    DELETE :CB_URL/catalog/agent/:AGENT
    :COMMON_HEADERS

&#x2026;or all agent matching the expression (redundancy of *id* and *partner* to
show an example)

    DELETE :CB_URL/catalog/agent
    :COMMON_HEADERS
    
    {
      "where": {
        "equals": {
          "target": "id",
          "expr": ":AGENT"
        },
        "equals": {
          "target": "partner",
          "expr": "nask"
        }
      }
    }


<a id="org13b75d4"></a>

## Agent Instance

Managing the `pgafilter` agent instance:

-   creation,
-   interaction history,
-   starting/stopping/restarting/reloading,
-   changing configuration,
-   uploading configuration.


<a id="org42da8cd"></a>

### Create

Agent instance for given Execution Environment

    POST :CB_URL/instance/agent/:AGENT@:EXEC_ENV
    :COMMON_HEADERS
    
    {
      "id": ":AGENT@:EXEC_ENV",
      "agent_catalog_id": "pgafilter",
      "exec_env_id": ":EXEC_ENV",
      "status": "stopped"
    }


<a id="orgaca9076"></a>

### History

Changes applied to the agent&rsquo;s configuration or state thorough `CB`

    GET :CB_URL/instance/agent/:AGENT@:EXEC_ENV
    :COMMON_HEADERS


<a id="orgda0a1b3"></a>

### Start, Stop, Restart, Reload

Change the state of the agent

Start

    PUT :CB_URL/instance/agent/:AGENT@:EXEC_ENV
    :COMMON_HEADERS
    
    {
      "id": ":AGENT@:EXEC_ENV",
      "operations": [
        {
          "actions": [
            {
              "id": "start"
            }
          ]
        }
      ]
    }

Stop

    PUT :CB_URL/instance/agent/:AGENT@:EXEC_ENV
    :COMMON_HEADERS
    
    {
      "id": ":AGENT@:EXEC_ENV",
      "operations": [
        {
          "actions": [
            {
              "id": "stop"
            }
          ]
        }
      ]
    }

Restart

    PUT :CB_URL/instance/agent/:AGENT@:EXEC_ENV
    :COMMON_HEADERS
    
    {
      "id": ":AGENT@:EXEC_ENV",
      "operations": [
        {
          "actions": [
            {
              "id": "restart"
            }
          ]
        }
      ]
    }

Reload (reload new configuration)

    PUT :CB_URL/instance/agent/:AGENT@:EXEC_ENV
    :COMMON_HEADERS
    
    {
      "id": ":AGENT@:EXEC_ENV",
      "operations": [
        {
          "actions": [
            {
              "id": "reload"
            }
          ]
        }
      ]
    }


<a id="orgd38b527"></a>

### Configuration

Can be modified by specifically changing given parameters or uploading and
overwriting the configuration file.

1.  Modifying parameters

        PUT http://guard3.westeurope.cloudapp.azure.com:5000/instance/agent/:AGENT@:EXEC_ENV
        :COMMON_HEADERS
        
        {
          "id": ":AGENT@:EXEC_ENV",
          "operations": [
            {
              "parameters": [
                {
                  "id": "poll-interval",
                  "value": 2
                }
              ]
            }
          ]
        }
    
    Change `rules` and `kafka-interval` parameters, and reload the service to run
    with new configuration.
    
        PUT http://guard3.westeurope.cloudapp.azure.com:5000/instance/agent/:AGENT@:EXEC_ENV
        :COMMON_HEADERS
        
        {
          "id": ":AGENT@:EXEC_ENV",
          "operations": [
            {
              "actions": [
                {
                  "id": "reload"
                }
              ],
              "parameters": [
                {
                  "id": "rules",
                  "value": [
                    "1007",
                    "1009",
                    "1011",
                    "1012"
                  ]
                },
                {
                  "id": "kafka-interval",
                  "value": 2
                }
              ]
            }
          ]
        }

2.  Uploading configuration

    Upload whole new `rules.yml` configuration file
    
        PUT http://guard3.westeurope.cloudapp.azure.com:5000/instance/agent/pgafilter@backend-prod
        :COMMON_HEADERS
        
        {
          "id": "pgafilter@backend-prod",
          "operations": {
            "resources": [
              {
                "content": "---\nrelease: 2022-03-01 12:46:01\nrules:\n  tcp-seq_is_tcp-ack:\n    sid: 1001\n    rev: 1\n    args:\n      - tcphdr\n    content: \"tcphdr->syn && !tcphdr->ack && tcphdr->seq == tcphdr->ack_seq\"\n    comment: \"TCP-SYN and TCP SEQ is equal to TCP ACK\"\n  tcp-ack_nonzero:\n    sid: 1002\n    rev: 1\n    args: [tcphdr]\n    content: \"tcphdr->syn && !tcphdr->ack && tcphdr->ack_seq != 0\"\n    comment: \"TCP-SYN and TCP ACK is non-zero\"\n  tcp-sport_is_tcp-seq:0:1:\n    sid: 1003\n    rev: 1\n    args: [tcphdr, iphdr]\n    content: \"tcphdr->source == pga_2_byte32(tcphdr->seq 0 1)\"\n",
                "id": "rule-file"
              }
            ]
          }
        }

