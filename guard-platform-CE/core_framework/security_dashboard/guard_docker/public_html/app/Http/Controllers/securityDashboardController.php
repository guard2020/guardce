<?php

namespace App\Http\Controllers;

use App\Http\API\ContextBrokerApiTrait;
use Elasticsearch\ClientBuilder;

class securityDashboardController extends Controller
{

    use ContextBrokerApiTrait;

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = $this->getData();
        return view('dashboard', $data);
    }

    public function getData()
    {
        $response['pipelines'] = count($this->getPipelines());

        if($this->countNotifications() === 1000){
            $response['notifications'] = '1000+';
        }else{
            $response['notifications'] = $this->countNotifications();
        }

        $response['services'] = $this->countExecuteEnvironments();

        return $response;
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
     * @return number
     */
    public function countNotifications() {
        $es = $this->esConnection();
        try {
            $results = $es->search([
                'index' => 'notification-index',
                'size' => 1000,
                'body' => [
                    "size"=> 1,
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
                    ],
                ]
            ]);

            $hits = isset($results['hits']['hits']) ? count($results['hits']['hits']) : '';

            return $hits;
        }
        catch(\Exception $ex) {
            \Log::critical($ex);
            return 0;
        }
    }


    /**
     * @param $resultData
     * @return number
     * @uses restricts only data relevant to threat notifications.
     */
    public function restrictData($resultData){
        $count = 0;
        if (!empty($resultData['hits']) && !empty($resultData['hits']['hits'])) {
            foreach ($resultData['hits']['hits'] as $key => $data) {
                if(!empty($data['_source']) && !empty($data['_source']['@timestamp']) && !empty($data['_source']['TIMESTAMP'])){
                    $count++;
                }
            }
        }

        return $count;
    }
}
