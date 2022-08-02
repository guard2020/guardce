<?php


namespace App\Http\Controllers;

use App\Classes\ServiceTopologyHelper;
use App\Http\API\ContextBrokerApiTrait;
use Illuminate\Support\Facades\Auth;
use App\Role;
use Illuminate\Http\Request;

class serviceTopologyController extends Controller
{

    use ContextBrokerApiTrait;

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
//        $rawEnv = $this->getExecuteEnvironmentsTopology();
        $rawEnvs = $this->getExecuteEnvironments();
        $allEnvs =  $this->separateRootEnvs($rawEnvs);
        $env = $allEnvs[0];
        $rootEnvs = $allEnvs[1];
        $environments = $this->addServicePositionEnvironment($env);
        $connections = $this->getConnection();
        $networks = $this->getNetworkLink();
        $connectionList = $this->prepareConnectionList($connections, $networks, $env);

        //check if needed CB data is available to create topology.
        if(count($env)){
            $data = [
                'environments' => $environments,
                'networks' => $networks,
                'connections' => $connectionList,
                'rootEnvs' => $rootEnvs,
                'userPermission' => $this->checkUserPermission(),
                'error' => false
            ];

            return view('service-topology.index', $data);
        }else{
            $data = [
                'environments' => [],
                'networks' => [],
                'connections' => [],
                'rootEnvs' => [],
                'userPermission' => $this->checkUserPermission(),
                'error' => true
            ];

            return view('service-topology.index', $data);
        }
    }

    /**
     * @param $envs
     * @return array
     */
    public function separateRootEnvs($envs)
    {
        $standardEnvs = [];
        $rootEnvs = [];
        foreach ($envs as $key=>$env){
            if(str_contains($env['id'], 'root')){
                $rootEnvs[] = $env;
            }else{
                $standardEnvs[] = $env;
            }
        }

        return [$standardEnvs, $rootEnvs];
    }

    /**
     * @param $environments
     * @return mixed
     */
    public function addServicePositionEnvironment($environments)
    {

        foreach ($environments as $key=>$environment){
            $environments[$key]['position'] = ServiceTopologyHelper::addPosition($environments[$key]['id']);
        }

        return $environments;
    }

    /**
     * @param $connections
     * @param $networks
     * @param $envs
     * @return array
     */
    public function prepareConnectionList($connections, $networks, $envs)
    {
        $list = [];
        $index = 0;
        foreach ($connections as $connection){

            //check if connection envs exist in env list. If does not exist should not be part of the connections.
            if(!$this->checkConEnvs($connection, $envs)){
                continue;
            }


            if(strpos($connection['id'], '@')) {

                foreach ($connections as $secondConnection) {
                    if (!strpos($secondConnection['exec_env_id'], 'root')) {

                        if ($secondConnection['network_link_id'] === $connection['network_link_id'] && $secondConnection['exec_env_id'] !== $connection['exec_env_id']) {
                            $list[$index]['node1'] = $connection['exec_env_id'];
                            $list[$index]['node2'] = $secondConnection['exec_env_id'];
                            $list[$index]['connection_type'] = $this->getConnectionType($connection['network_link_id'], $networks);
                            $index++;
                            break;
                        }
                    }
                }
            }else if(strpos($connection['id'],'2')){
                $nodes = explode('2', $connection['id']);
                if($this->getExecuteEnvironment($connection['exec_env_id'])->status() === 200 && $this->getExecuteEnvironment($nodes[1])->status() === 200){
                    $list[$index]['node1'] = $connection['exec_env_id'];
                    $list[$index]['node2'] = $nodes[1];
                    $list[$index]['connection_type'] = $this->getConnectionType($connection['network_link_id'], $networks);
                    $index++;
                }
            }else if(strpos($connection['id'], '>')) {
                $nodes = explode('>', $connection['id']);
                if (strpos($nodes[1], '@')) {
                    $list[$index]['node1'] = $connection['exec_env_id'];
                    $temp = explode('@', $nodes[1]);
                    $list[$index]['node2'] = $temp[1];
                    $list[$index]['connection_type'] = $temp[0];
                    $index++;
                } else {
                    $networkType = $this->getConnectionType($connection['network_link_id'], $networks);
                    $networkLinks = $this->getNetworkLinkInConnection($connection['network_link_id'], $connections);

                    if (!empty($networkLinks) and count($networkLinks) > 1) {
                        $list[$index]['node1'] = $networkLinks[0]['exec_env_id'];
                        $list[$index]['node2'] = $networkLinks[1]['exec_env_id'];
                        $list[$index]['connection_type'] = $networkType;
                        $index++;
                    }
                    $list = array_unique($list, SORT_REGULAR);//TODO-Test.
                }
            }else{
                foreach ($connections as $secondConnection) {
                    if (!strpos($secondConnection['exec_env_id'], 'root')) {

                        if ($secondConnection['network_link_id'] === $connection['network_link_id'] && $secondConnection['exec_env_id'] !== $connection['exec_env_id']) {
                            $list[$index]['node1'] = $connection['exec_env_id'];
                            $list[$index]['node2'] = $secondConnection['exec_env_id'];
                            $list[$index]['connection_type'] = $this->getConnectionType($connection['network_link_id'], $networks);
                            $index++;
                            break;
                        }
                    }
                }
            }
        }
        $list = array_values($list);
        return $list;
    }

    /**
     * @param $networkLink
     * @param $networks
     * @return string
     */
    public function getConnectionType($networkLink, $networks)
    {
        $result = "";
        foreach ($networks as $network){
            if($network['id'] === $networkLink){
                $result = $network['type_id'];
            }
        }
        return $result;
    }

    /**
     * @param $networkLinkId
     * @param $connections
     * @return array
     */
    public function getNetworkLinkInConnection($networkLinkId, $connections){
        $foundNetworks = [];
        foreach($connections as $connection){
            if($connection['network_link_id'] ===  $networkLinkId){
                $foundNetworks[] = $connection;
            }
        }

        return $foundNetworks;
    }

    /**
     * @param $con
     * @param $networks
     * @param $envs
     * @return bool
     */
    public function checkNetworAndEnvs($con, $networks, $envs)
    {
        $networkExists = false;
        $envExists = false;
        foreach ($networks as $net){
            if($con['network_link_id'] === $net['id']){
                $networkExists = true;
            }
        }

        foreach ($envs as $env){
            if($con['exec_env_id'] === $env['id']){
                $envExists = true;
            }
        }

        if(isset($networkExists) && isset($envExists)){
            return true;
        }

        return false;
    }

    /**
     * @param $con
     * @param $envs
     * @return bool
     */
    public function checkConEnvs($con, $envs)
    {
        foreach ($envs as $env){
            if($con['exec_env_id'] === $env['id']){
                return true;
            }
        }
        return false;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * Thsi function is responsible for starting the discover process of a new chain.
     * It will create the ROOT EXEC ENV and store it in the CB Manager. And this will trigger the discovery in the Context Broker.
     */
    public function discoverChain(Request $request)
    {
        $chain = [];

        $chain['id'] = 'root-'.$request->input('hostname');
        $chain['hostname'] = $request->input('hostname');
        //type will be VM
        $chain['type_id'] = 'vm';
        //enabled will be set to true
        $chain['enabled'] = true;
        $chain['lcp']['port'] = $request->input('port');
        $chain['lcp']['https'] = $request->input('https') ? true : false;

        $create = $this->createRootExecEnvironment($chain);

        sleep(1);
        return redirect()->back()->with('success', 'Chain successfully created');


    }

    /**
     * @param $hostname
     * @return \Illuminate\Http\RedirectResponse
     * This function deletes an existing Service Chain. It will check if the chain is connected to any existing pipeline and update or delete this pipeline also.
     */
    public function destroy($hostname)
    {
        $rootId = 'root-'.$hostname;
        //check if root exec env exists with id root-$hostname
        $env = $this->getExecuteEnvironment($rootId);

        if($env->status() !== 200){
            return redirect()->back()->with('warning', 'Error! Chain to be deleted not found. Chain: '.$hostname);
        }

        //if successful deletion, remove connection of pipelines to the deleted agent/algo instances OR if the pipeline is only connected to the delete agent instances, delete the pipeline.
        $resultEnvironments = $this->getExecuteEnvironmentByHostname($hostname);
        $agentInstances = [];

        if($resultEnvironments->status() !== 200){
            return redirect()->back()->with('warning', 'No execute Environment Environment found.');
        }

        if(is_array($resultEnvironments->json())){
            foreach ($resultEnvironments->json() as $environment){
                $resultInstance = $this->getAgentInstanceByEnv($environment['id']);
                if($resultInstance->status() === 200){
                    $agentInstances[] = $resultInstance->json()[0];
                }
            }
        }

        if(empty($agentInstances)){
            //no agent instances related to the deleted chain, thus no pipeline related either.

            //call delete method with param root-$hostname
            $delete = $this->deleteRootExecEnvironment($rootId);

            if($delete->status() !== 200){
                return redirect()->back()->with('warning', 'Error! Error! Chain does not exist. CB Manager response: '.$delete->status());
            }


            sleep(1);
            return redirect()->back()->with('success', 'Chain deleted successfully! There were no pipelines related to the chain.');
        }

//        //delete agent from pipelines

        $pipelines = [];
        foreach ($agentInstances as $instance){
            //delete agent instance from pipeline
            $resultPipelines = $this->getPipelineByAgentId($instance['agent_catalog_id']);
            if($resultPipelines->status() === 200){
                $pipelines[] = $resultPipelines->json()[0];
            }
        }


        if(empty($pipelines)){
            //call delete method with param root-$hostname
            $delete = $this->deleteRootExecEnvironment($rootId);

            if($delete->status() !== 200){
                return redirect()->back()->with('warning', 'Error! Error! Chain does not exist. CB Manager response: '.$delete->status());
            }
            sleep(1);
            return redirect()->back()->with('success', 'Chain deleted successfully! There were no pipelines related to the chain.');
        }

        foreach ($pipelines as $pipelineKey =>$pipeline){
            if(empty($pipeline['agent_configs'])){
                //delete pipeline
                $deletePipeline = [
                    'where' => [
                        'equals' => [
                            "target" => "id",
                            "expr" =>  $pipeline['id'],
                        ],
                    ],
                ];

                $status = $this->crudPipelineApi($deletePipeline, 'delete');
                if($status !== 205 || $status !== 200){
                    return redirect()->back()->with('warning', 'Error! Deletion not successful. Deletion of related pipeline did not work. Error code: '.$status);
                }

                //call delete method with param root-$hostname
                $delete = $this->deleteRootExecEnvironment($rootId);

                if($delete->status() !== 205 || $delete->status() !== 200){
                    return redirect()->back()->with('warning', 'Error! Chain does not exist. CB Manager response: '.$delete->status());
                }
            }

            if(count($pipeline['agent_configs']) === 1){
                //pipeline has only one instance (which is going to be deleted.)
                //delete pipeline
                $deletePipeline = [
                    'where' => [
                        'equals' => [
                            "target" => "id",
                            "expr" =>  $pipeline['id'],
                        ],
                    ],
                ];

                $status = $this->crudPipelineApi($deletePipeline, 'delete');
                if($status !== 'success'){
                    return redirect()->back()->with('warning', 'Error! Deletion not successful. Deletion of related pipeline did not work. Error code: '.$status);
                }

                //call delete method with param root-$hostname
                $delete = $this->deleteRootExecEnvironment($rootId);

                if($delete->status() !== 205 || $delete->status() !== 200){
                    return redirect()->back()->with('warning', 'Error! Chain does not exist. CB Manager response: '.$delete->status());
                }


            }else{
                //pipeline has more than one instance. Only remove the instances of the pipeline that are being deleted.
                foreach($pipeline['agent_configs'] as $agentKey => $agentConfig){
                    foreach ($agentInstances as $agentInstance){
                        if($agentConfig['agent_instance_id'] === $agentInstance['id']){
                            unset($pipelines[$pipelineKey][$agentKey]);
                        }
                    }

                    //special case. All agent instances managed by the pipeline were from exec envs related to the chain. Pipelines need to be deleted.
                    if(empty($pipelines[$pipelineKey]['agent_configs'])){
                        //delete
                        $deletePipeline = [
                            'where' => [
                                'equals' => [
                                    "target" => "id",
                                    "expr" =>  $pipeline['id'],
                                ],
                            ],
                        ];

                        $status = $this->crudPipelineApi($deletePipeline, 'delete');
                        if($status !== 'success'){
                            return redirect()->back()->with('warning', 'Error! Deletion not successful. Deletion of related pipeline did not work. Error code: '.$status);
                        }

                        //call delete method with param root-$hostname
                        $delete = $this->deleteRootExecEnvironment($rootId);

                        if($delete->status() !== 205 || $delete->status() !== 200){
                            return redirect()->back()->with('warning', 'Error! Chain does not exist. CB Manager response: '.$delete->status());
                        }

                    }else{
                        $status = $this->crudPipelineApi($pipelines[$pipelineKey], 'edit');
                        if($status !== 'success'){
                            return redirect()->back()->with('warning', 'Error! Deletion not successful. Deletion of related pipeline did not work. Error code: '.$status);
                        }

                        //call delete method with param root-$hostname
                        $delete = $this->deleteRootExecEnvironment($rootId);

                        if($delete->status() !== 205 || $delete->status() !== 200){
                            return redirect()->back()->with('warning', 'Error! Chain does not exist. CB Manager response: '.$delete->status());
                        }
                    }
                }
            }
        }

        sleep(1);
        return redirect()->back()->with('success', 'Chain deleted successfully! There were no pipelines related to the chain.');
    }


    public function discoveryChainIndex()
    {
        $data['userPermission'] = $this->checkUserPermission();

        return view('service-topology.chain-discovery.index', $data);
    }

    public function datatables()
    {
        $rawEnvs = $this->getExecuteEnvironments();
        $chainEnvs = $this->separateRootEnvs($rawEnvs)[1];

        $data = datatables($chainEnvs)
            ->addColumn('actions', function($chain) {
                $status = null;
                return view('service-topology.chain-discovery.partials.index-actions', [
                    'chain' => $chain,
                    'userPermission' => $this->checkUserPermission(),
                ])->render();
            })
            ->editColumn('hostname', function($chain) {
                return isset($chain['hostname']) ? $chain['hostname'] : "-";
            })
            ->editColumn('port', function($chain) {
                return isset($chain['lcp']['port']) ? $chain['lcp']['port']: '-';
            })
            ->editColumn('https', function($chain) {
                return isset($chain['lcp']['htpps']) ? $chain['lcp']['htpps'] : "false";
            })
            ->rawColumns(['actions'])
            ->toJson();

        return $data;
    }

    /**
     * @use This function used to check the user access permission based on roles
     * @return bool|null
     */
    private function checkUserPermission()
    {
        $isAdmin = null;
        $userRole = Role::query()->where('id', Auth::user()->role_id)->first();
        if(!empty($userRole) && $userRole->name === 'Administrator'){
            $isAdmin = true;
        }

        return $isAdmin;
    }
}