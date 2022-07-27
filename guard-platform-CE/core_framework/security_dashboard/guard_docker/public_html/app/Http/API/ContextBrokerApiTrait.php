<?php


namespace App\Http\API;

use Illuminate\Support\Facades\Http;

trait ContextBrokerApiTrait
{
    /**
     * Static bearer token for the API connection to the CB
     * @var array
     */
    public static $authorizationToken = [
        'Authorization' => 'GUARD eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.e30.Jqlph3IlOo7ugQdR9PgvmxGEFzb3mMqTXkr7Te_-yJ0',
    ];

    /**
     * This function returns the execute environments and types from the CB.
     *
     * Temporary: The displayed environments in the topology view are restricted due to constant data structure changes.
     *
     * @return array
     */
    public function getExecuteEnvironmentsTopology()
    {
        $response = Http::withHeaders(self::$authorizationToken)->get(config('constants.cb_api').'/exec-env');
        $responseType = Http::withHeaders(self::$authorizationToken)->get(config('constants.cb_api').'/type/exec-env');
        $data = $response->json();
        $dataType = $responseType->json();

        $topologyEnvs = ['user-vm', 'simulation-model-tool', 'backend-test', 'backend-backup', 'digit-cyber',
            'alpha-service', 'vision-tech', 'lora', 'network-manager', 'web-server', 'frontend', 'backend-prod', 'mobile-phone', 'chirpstack-kafka'];

        $result = [];
        $index = 0;

        foreach($data as $item){

            if(in_array($item['id'], $topologyEnvs)){

                //required
                $result[$index]['id'] = $item['id'];
                $result[$index]['enabled'] = $item['enabled'];
                $result[$index]['hostname'] = $item['hostname'];
                $result[$index]['type_id'] = $item['type_id'];


                //optional
                if(isset($item['description'])){
                    $result[$index]['description'] = $item['description'];
                }
                if(isset($item['lcp'])){
                    $result[$index]['lcp'] = $item['lcp'];
                }
                if(isset($item['partner'])){
                    $result[$index]['partner'] = $item['partner'];
                }
                if(isset($item['partner'])){
                    $result[$index]['partner'] = $item['partner'];
                }
                if(isset($item['stage'])){
                    $result[$index]['stage'] = $item['stage'];
                }

                foreach ($dataType as $type){
                    if($type['id'] === $item['type_id']){
                        $result[$index]['type_description'] = $type;
                    }
                }
                $index++;
            }
        }
        return $result;

    }


    /**
     * @return array
     */
    public function getExecuteEnvironments()
    {
        $response = Http::withHeaders(self::$authorizationToken)->get(config('constants.cb_api').'/exec-env');
        $responseType = Http::withHeaders(self::$authorizationToken)->get(config('constants.cb_api').'/type/exec-env');
        $data = $response->json();
        $dataType = $responseType->json();

        $result = [];
        $index = 0;

        if($response->status() !== 200 || $responseType->status() !== 200){
            return [];
        }

        if(is_array($data)){
            foreach($data as $item){

                //required
                $result[$index]['id'] = $item['id'];
                $result[$index]['enabled'] = $item['enabled'];
                $result[$index]['hostname'] = $item['hostname'];
                $result[$index]['type_id'] = $item['type_id'];

                //optional
                if(isset($item['description'])){
                    $result[$index]['description'] = $item['description'];
                }else{
                    $result[$index]['description'] = $item['hostname'];
                }
                if(isset($item['lcp'])){
                    $result[$index]['lcp'] = $item['lcp'];
                }
                if(isset($item['partner'])){
                    $result[$index]['partner'] = $item['partner'];
                }
                if(isset($item['partner'])){
                    $result[$index]['partner'] = $item['partner'];
                }
                if(isset($item['stage'])){
                    $result[$index]['stage'] = $item['stage'];
                }

                foreach ($dataType as $type){
                    if($type['id'] === $item['type_id']){
                        $result[$index]['type_description'] = $type;
                    }
                }
                $index++;
            }
        }else{
            $result = [];
        }

        return $result;

    }

    /**
     * @return int
     */
    public function countExecuteEnvironments()
    {
        $response = Http::withHeaders(self::$authorizationToken)->get(config('constants.cb_api').'/exec-env');


        if($response->status() !== 200){
            return 0;
        }

        return count(json_decode($response->body(), true));
    }

    public function getExecuteEnvironment($env)
    {
        $body = [
            "where" => [
                "equals" => [
                    "target" => "id",
                    "expr" => $env
                ]
            ]
        ];

        $response = Http::withHeaders(self::$authorizationToken)->send('GET', config('constants.cb_api').'/exec-env', [
            'body' => json_encode($body)
        ]);

        return $response;
    }

    public function getExecuteEnvironmentByHostname($hostname)
    {
        $body = [
            "select" => ['id'],
            "where" => [
                "equals" => [
                    "target" => "hostname",
                    "expr" => $hostname
                ]
            ]
        ];

        $response = Http::withHeaders(self::$authorizationToken)->send('GET', config('constants.cb_api').'/exec-env', [
            'body' => json_encode($body)
        ]);

        return $response;
    }

    /**
     * This function returns network links from the CB
     *
     * @return array
     */
    public function getNetworkLink()
    {
        $response = Http::withHeaders(self::$authorizationToken)->get(config('constants.cb_api').'/network-link');
        $responseType = Http::withHeaders(self::$authorizationToken)->get(config('constants.cb_api').'/type/network-link');
        $data = $response->json();
        $dataType = $responseType->json();


        if($response->status() !== 200 || $responseType->status() !== 200){
            return [];
        }

        $result = [];
        $index = 0;

        if(is_array($data)){
            foreach($data as $item){
                if(isset($item['exec_env_id'])){
                    $result[$index]['exec_env_id'] = $item['exec_env_id'];
                }
                $result[$index]['id'] = $item['id'];
                $result[$index]['type_id'] = $item['type_id'];
                foreach ($dataType as $type){
                    if($type['id'] === $item['type_id']){
                        $result[$index]['type_description'] = $type;
                    }
                }
                $index++;
            }
        }else{
            $result = [];
        }

        return $result;

    }

    /**
     * This function returns the connections between Execute Environments and Networks from the CB.
     *
     * @return array
     */
    public function getConnection()
    {
        $response = Http::withHeaders(self::$authorizationToken)->get(config('constants.cb_api').'/connection');
        $data = $response->json();


        if($response->status() !== 200){
            return [];
        }

        $result = [];
        $index = 0;

        foreach($data as $item){
            //only create connection if exec exists...
            if($this->getExecuteEnvironment($item['exec_env_id'])->status() === 200){
                //required
                $result[$index]['exec_env_id'] = $item['exec_env_id'];
                $result[$index]['id'] = $item['id'];
                $result[$index]['network_link_id'] = $item['network_link_id'];

                $index++;
            }
        }

        return $result;
    }

    /**
     * This function returns the agents from the CB.
     *
     * @return array
     */
    public function getAgents()
    {
        $response = Http::withHeaders(self::$authorizationToken)->get(config('constants.cb_api').'/catalog/agent');

        if($response->status() !== 200){
            return [];
        }

        return $response->json();
    }

    /**
     * This function returns the agent instances from the CB.
     *
     * @return array
     */
    public function getAgentInstances()
    {
        $response = Http::withHeaders(self::$authorizationToken)->get(config('constants.cb_api').'/instance/agent');

        if($response->status() !== 200){
            return [];
        }

        return $response->json();
    }

    public function getAgentInstanceByEnv($env)
    {
        $body = [
            "select" => ['id', 'agent_catalog_id'],
            "where" => [
                "equals" => [
                    "target" => "exec_env_id",
                    "expr" => $env
                ]
            ]
        ];

        $response = Http::withHeaders(self::$authorizationToken)->send('GET', config('constants.cb_api').'/instance/agent', [
            'body' => json_encode($body)
        ]);

        return $response;
    }

    public function getEnvByInstanceId($instanceId)
    {
        $body = [
            "select" => ['exec_env_id'],
            "where" => [
                "equals" => [
                    "target" => "id",
                    "expr" => $instanceId
                ]
            ]
        ];

        $response = Http::withHeaders(self::$authorizationToken)->send('GET', config('constants.cb_api').'/instance/agent', [
            'body' => json_encode($body)
        ]);

        if($response->status() !== 200){
            return [];
        }

        return $response;
    }

    /**
     * @return array|mixed
     */
    public function getAlgorithms()
    {
        $response = Http::withHeaders(self::$authorizationToken)->get(config('constants.cb_api').'/catalog/algorithm');
        return $response->json();
    }

    /**
     * @return array|mixed
     */
    public function getAlgorithmInstances()
    {
        $response = Http::withHeaders(self::$authorizationToken)->get(config('constants.cb_api').'/instance/algorithm');
        return $response->json();
    }

    /**
     * This function returns the pipelines from the CB.
     *
     * @return array
     */
    public function getPipelines(){
        $response = Http::withHeaders(self::$authorizationToken)->get(config('constants.cb_api').'/pipeline');

        if($response->status() !== 200){
            return [];
        }

        return $response->json();
    }

    public function getPipelineByAgentId($agentId)
    {
        $body = [
            "where" => [
                "equals" => [
                    "target" => "agent_catalog_id",
                    "expr" => $agentId
                ]
            ]
        ];

        $response = Http::withHeaders(self::$authorizationToken)->send('GET', config('constants.cb_api').'/pipeline', [
            'body' => json_encode($body)
        ]);

        return $response;
    }

    /**
     * This function retrieves a specific ($id) pipeline.
     * If the pipeline is found the function returns the pipeline data (json).
     * If the pipeline is not found the function redirects back and returns an error message.
     *
     * @param $id
     * @return array|\Illuminate\Http\RedirectResponse
     */
    public function findOrFailPipeline($id)
    {
        $response = Http::withHeaders(self::$authorizationToken)->get(config('constants.cb_api').'/pipeline/'.$id);

        if($response->status() >= 300){
            return back()->with('error', 'Pipeline not found in CB');
        }

        return $response->json();
    }

    /**
     * This function performs CRUD API calls for the Pipeline with the CB
     *
     *
     * @param $requestData
     * @param null $action
     * @return string|null
     */
    public function crudPipelineApi($requestData, $action = null){
        $endpoint = config('constants.cb_api').'/pipeline';

        if($action === 'create'){
            $method = 'post';
        } else if($action === 'edit'){
            $method = 'put';
        } else if($action === 'delete'){
            $method = 'delete';
        }else {
            return redirect()->back('error', 'Action passed not allowed.');
        }

        $response = Http::withHeaders(self::$authorizationToken)->$method($endpoint, $requestData);
        $status = null;

        if(isset($response[0])) {
            if ($response[0]['status'] === "Created" && $response[0]['code'] === 201) {
                $status = "success";
            } elseif (($response[0]['status'] === "OK" && $response[0]['code'] === 200) ||
                ($response[0]['status'] === "Not Modified" && $response[0]['code'] === 304)) {
                $status = "success";
            } elseif ($response[0]['status'] === "Reset Content" && $response[0]['code'] === 205) {
                $status = "success";
            }
        }
        return $status;
    }

    /**
     * This function retrieves a specific ($id) agent instance.
     * If the agentinstance is found the function returns the agentinstance data (json).
     * If the agentinstance is not found the function returns code-404 with message.
     *
     * @param $id
     * @return array|mixed
     */
    public function findOrFailAgentInstance($id)
    {
        $response = Http::withHeaders(self::$authorizationToken)->get(config('constants.cb_api').'/instance/agent/'.$id);

        return $response->json();
    }

    /**
     * This function returns the agentInstanceId based on agentId and EnvId
     *
     * @param $agentId
     * @param $envId
     * @return mixed|null
     * @throws \Exception
     */
    public function getAgentInstanceIdByAgentAndEnv($agentId, $envId)
    {
        $params = [
            "select" => ["id", "exec_env_id","agent_catalog_id"],
            "where" => [
                "equals" => [
                    "target" => "exec_env_id",
                    "expr" => $envId
                ],
            ],
        ];

        $response = Http::withHeaders(self::$authorizationToken)
            ->send('GET', config('constants.cb_api').'/instance/agent', [
                'body' => json_encode($params)
            ]);

        $agentInstances = $response->json();
        if(!empty($agentInstances)){
            foreach ($agentInstances as $instance){
                if($instance['agent_catalog_id'] === $agentId && $instance['exec_env_id'] === $envId){
                    return $instance['id'];
                }
            }
        }

        return null;
    }

    /**
     * This function returns the specific agent details by id from the CB.
     *
     * @return array
     */
    public function getAgentsById($id)
    {
        $response = Http::withHeaders(self::$authorizationToken)->get(config('constants.cb_api').'/catalog/agent/'.$id);
        return $response->json();
    }

    /**
     * @param $env
     * @return mixed
     */
    public function createRootExecEnvironment($env)
    {

        $url = config('constants.cb_api').'/exec-env';
        $response = Http::withHeaders(self::$authorizationToken)->post($url, $env);

        return $response->json();
    }

    /**
     * @param $env
     * @return \Illuminate\Http\Client\Response|\Illuminate\Http\RedirectResponse
     */
    public function deleteRootExecEnvironment($env)
    {
        if(!empty($env)){
            //delete root exec env
            if(!str_contains($env, 'root')){
                dump($env);
                return redirect()->back()->with('warning', 'Error! Cannot delete the chain. There is no root execution environment.');
            }

            /**
             * TODO- After check, delete this. Only needed if it was to delete EXEC ENV not CHAIN.
             */
//            $body = [
//                'where' => [
//                    'equals' => [
//                        "target" => "id",
//                        "expr" =>  $env,
//                    ],
//                ],
//            ];

            $url = config('constants.cb_api').'/chain/'.$env;
            $response = Http::withHeaders(self::$authorizationToken)->delete($url);

            return $response;
        }else{
            dd('error empty body!');
        }
    }
}