<?php


namespace App\Http\Controllers;
use Elasticsearch\ClientBuilder;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;

class notificationsController extends Controller
{
    /**
     * @return Application|Factory|View
     */
    public function index()
    {
        return view('kafka-notifications.index');
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajaxNotificationsTable()
    {

        $params = [
            'index' => 'notification-index',
            'size' => 2000,
            'body' => [
                "size"=> 1,
                "query" => [
                    "bool" => [
                        "should" => [
                            [
                                "bool" => [
                                    "must" => [
                                        [
                                            "exists" => [
                                                "field" => "TIMESTAMP"
                                            ],
                                        ],
                                        [
                                            "exists" => [
                                                "field" => "SOURCE"
                                            ]
                                        ],
                                        [
                                            "exists" => [
                                                "field" => "SEVERITY"
                                            ]
                                        ],
                                        [
                                            "exists" => [
                                                "field" => "@timestamp"
                                            ]
                                        ]
                                    ]
                                ]
                            ],
                            [
                                "bool" => [
                                    "must" => [
                                        [
                                            "exists" => [
                                                "field" => "origin"
                                            ],
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                "sort"=> [
                    "@timestamp"=> [
                        "order"=> "desc"
                    ],
                ],
            ]
        ];

        $notifications = $this->prepareData($params);

        return datatables($notifications)->toJson();
    }

    /**
     * @param $params
     * @return array
     */
    public function prepareData($params){
        $notifications = [];
        $resultData = $this->loadDataFromEs($params);


        foreach ($resultData['hits'] as $key => $data) {

            if(isset($data['_source']['origin'])){
                $notifications[$key] = $this->structureRiskAssessmentMsgs($data);
            }else{
                $notifications[$key] = $data['_source'];
                $notifications[$key]['unixTimestamp'] = strtotime($data['_source']['@timestamp']);
                $notifications[$key]['timestamp'] = $data['_source']['@timestamp'];
            }
        }

        return $notifications;
    }


    public function structureRiskAssessmentMsgs($message)
    {
        /**
         * {
         *  "quantitativeTotalVulnerabilityScore":7.6800003,
         *  "qualitativeTotalVulnerabilityScore":"Low",
         *  "origin":"Risk Assessment",
         *  "description":"Qualitative and quantitative risk"
         * }
         **/

        /**
         *
        "@timestamp" => "2022-04-28T10:32:49.530Z"
        "DATA" => array:5 [▶]
        "SOURCE" => "ALGO112_v3"
        "DESCRIPTION" => "DDoS Attack(s)"
        "TIMESTAMP" => 1651141969.5235
        "SEVERITY" => "10"
        "@version" => "1"
        "unixTimestamp" => 1651141969
        "timestamp" => "2022-04-28T10:32:49.530Z"
         */

        if($message['_source']['origin'] === 'RiskAssessment'){
            $message['_source']['origin'] = 'Risk Assessment';
        }

        $result = [
            'DATA' => [
                'quantitativeTotalVulnerabilityScore' => $message['_source']['quantitativeTotalVulnerabilityScore'],
                'qualitativeTotalVulnerabilityScore' => $message['_source']['qualitativeTotalVulnerabilityScore']
            ],
            'DESCRIPTION' => $message['_source']['description'],
            'SOURCE' => $message['_source']['origin'],
            'TIMESTAMP' => strtotime($message['_source']['@timestamp']),
            'SEVERITY' => $message['_source']['qualitativeTotalVulnerabilityScore'],
            'unixTimestamp' => strtotime($message['_source']['@timestamp']),
            'timestamp' => $message['_source']['@timestamp'],
            '@timestamp' => $message['_source']['@timestamp']

        ];

        return $result;
    }
    

    /**
     * Find  latest notification from elasticsearch by timestamp
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajaxReloadNotificationsTable()
    {
        $notifications = [];
        if(!empty($_GET['timestamp'])) {
            $params = [
                'index' => 'notification-index',
                'size' => 5000,
                'body' => [
                    "size"=> 1,
                    'query' => [
                        "bool" => [
                            "filter" => [
                                [
                                    "range" => [
                                        "@timestamp" => [
                                            "gt" => $_GET['timestamp'],
                                        ],
                                    ],
                                ],
                                [
                                    "bool" => [
                                        "should" => [
                                            [
                                                "bool" => [
                                                    "must" => [
                                                        [
                                                            "exists" => [
                                                                "field" => "TIMESTAMP"
                                                            ],
                                                        ],
                                                        [
                                                            "exists" => [
                                                                "field" => "SOURCE"
                                                            ]
                                                        ],
                                                        [
                                                            "exists" => [
                                                                "field" => "SEVERITY"
                                                            ]
                                                        ],
                                                        [
                                                            "exists" => [
                                                                "field" => "@timestamp"
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ],
                                            [
                                                "bool" => [
                                                    "must" => [
                                                        [
                                                            "exists" => [
                                                                "field" => "origin"
                                                            ],
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    "sort"=> [
                        "@timestamp"=> [
                            "order"=> "desc"
                        ],
                    ],
                ],
            ];

            $notifications = $this->prepareData($params);
        }

        return datatables($notifications)->toJson();
    }

    public function ajaxCheckNewNotifications(){
        $newTimestamp = null;
        $status = false;
        if(!empty($_GET['timestamp'])) {
            $params['body'] = [
                "_source" => ['@timestamp'],
                "size"=> 1,
                "sort"=> [
                    "@timestamp"=> [
                    "order"=> "desc"
                  ],
                ],
                "query" => [
                    "bool" => [
                        "filter" => [
                            [
                                "exists" => [
                                    "field" => "TIMESTAMP"
                                ],
                            ],
                            [
                                "exists" => [
                                    "field" => "SOURCE"
                                ]
                            ],
                            [
                                "exists" => [
                                    "field" => "SEVERITY"
                                ]
                            ],
                            [
                                "exists" => [
                                    "field" => "@timestamp"
                                ]
                            ]
                        ]
                    ]
                ]
            ];

            $resultData = $this->loadDataFromEs($params);
            if(isset($resultData['hits'][0]['_source']['@timestamp']) &&
                $_GET['timestamp'] !== $resultData['hits'][0]['_source']['@timestamp']){
                $status = true;
                $newTimestamp = $resultData['hits'][0]['_source']['@timestamp'];
            }
        }

        return response()->json(['newTimestamp' => $newTimestamp, 'status' => $status]);
    }

    /**
     * This function establish connection to ES Client
     *
     * @return \Elasticsearch\Client
     */
    public function esConnection() {
        $esHost = config('elasticquent.config.hosts');
        return ClientBuilder::create()  // Instantiate a new ClientBuilder
        ->setHosts($esHost)             // Set the hosts
        ->build();
    }

    /**
     * This function loads the data from ElasticSearch based on the search params.
     *
     * @param $params
     * @return mixed
     */
    public function loadDataFromEs($params) {
        $es = $this->esConnection();

        try {
            $results = $es->search($params);
            $hits = $results['hits'];

            return $hits;
        }
        catch(\Exception $ex) {
            \Log::critical($ex);
            return response()->json(['errors' => ['elasticsearch' => ['The connection to Elastic Search is not working.']]], 400);
        }
    }

}