<?php

namespace App\Http\Controllers;

use App\Http\API\ContextBrokerApiTrait;
use App\Http\API\SecurityControllerApiTrait;
use App\Pipelines;
use App\Role;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;


/**
 * Class securityPipelineController
 * @package App\Http\Controllers
 * @author Tomas Lima (GUARD Project)
 */
class securityPipelineController extends Controller
{
    use ContextBrokerApiTrait, SecurityControllerApiTrait;

    /**
     * @use Pipeline listing on index page
     * @return Application|Factory|View
     */
    public function index(){
        $data['userPermission'] = $this->checkUserPermission();
        $data['agent_instances'] = count($this->getAgentInstances());

        if($this->checkBasicData()){
            $data['error'] = false;
        }else{
            $data['error'] = true;
        }

        return view('security-pipeline.index', $data);
    }

    /**
     * @use Prepare pipeline listing on index page
     * @return JsonResponse
     */
    public function datatables()
    {
        //assure data newly created, is added
        sleep(1);

        $pipeLineData = $this->getPipelines();
        $pipeLineData = $this->addChainToPipeline($pipeLineData);
        $pipeLineData = (isset($pipeLineData['code']) && $pipeLineData['code'] === 404) ? [] : $pipeLineData;

        $data = datatables($pipeLineData)
            ->addColumn('actions', function($pipeline) {
                $status = null;
                return view('security-pipeline.partials.index-actions', [
                    'pipeline' => $pipeline,
                    'userPermission' => $this->checkUserPermission(),
                    'reloadStatus' => $status,
                ])->render();
            })
            ->editColumn('name', function($pipeline) {
                return isset($pipeline['name']) ? $pipeline['name'] : "-";
            })
            ->editColumn('chain', function($pipeline) {
                return isset($pipeline['chain']) ? $pipeline['chain']: '-';
            })
            ->addColumn('agents', function ($pipeline) {
                if(isset($pipeline['agent_catalog_id'])){
                    $agentCatalogId = $pipeline['agent_catalog_id'];
                } else if(isset($pipeline['agent_configs']['agent_catalog_id'])){
                    $agentCatalogId = $pipeline['agent_configs']['agent_catalog_id'];
                } else {
                    $agentCatalogId = '-';
                }

                return $agentCatalogId;
            })
            ->editColumn('created_at', function($pipeline) {
                return isset($pipeline['created_at']) ? date('d-m-Y H:i:s', $pipeline['created_at']) : "";
            })
            ->editColumn('updated_at', function($pipeline) {
                return isset($pipeline['updated_at']) ? date('d-m-Y H:i:s', $pipeline['updated_at']) : "";
            })
            ->editColumn('user', function($pipeline) {
                return isset($pipeline['user']) ? $pipeline['user'] : "unknown";
            })
            ->addColumn('status', function($pipeline) {
                return view('security-pipeline.partials.index-status', [
                    'pipeline' => $pipeline,
                ])->render();
            })
            ->rawColumns(['actions', 'created', 'status','agents'])
            ->toJson();

        return $data;
    }

    public function addChainToPipeline($pipelines)
    {
        if(empty($pipelines)){
            return [];
        }
        foreach ($pipelines as $key=>$pipeline){
            $instances = [];
            foreach ($pipeline['agent_configs'] as $agentConfig){
                $instances[] = $agentConfig['agent_instance_id'];
            }

            foreach ($instances as $instance){
                $resultEnvInstance = $this->getEnvByInstanceId($instance);
                if($resultEnvInstance->status() === 200){
                    $resultEnvs = $this->getExecuteEnvironment($resultEnvInstance->json()[0]['exec_env_id']);
                    if($resultEnvs->status() === 200){
                        if($resultEnvs->json()[0]['id'])
                            $pipelines[$key]['chain'][] = $resultEnvs->json()[0]['hostname'];
                    }
                }
            }
        }

        return $pipelines;

    }

    /**
     * @use Prepare data (agents,instances and environments) to use in create/edit function
     * @return array
     * @throws \Exception
     */
    public function preparedData(){
        $agents = $this->getAgents();
        $agentInstances = $this->getAgentInstances();
        $environmentsCB = $this->getExecuteEnvironments();
        $algorithms = $this->getAlgorithms();
//        $algorithmInstances = $this->getAlgorithmInstances();
        $agentsEnvironment = $this->agentWiseExecEnvironment($agents, $environmentsCB, $agentInstances);
        $activeAlgos = $this->getActiveAlgorithms($algorithms);

        $data = [
            'agents' => $agents,
            'agentWiseInsAndEnv' => $agentsEnvironment,
            'agentsJson' => json_encode($agents, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK),
            'algorithms' => $algorithms,
//            'algorithmInstances' => $algorithmInstances,
            'activeAlgorithms' => $activeAlgos,
        ];

        return $data;
    }


    /**
     * @return Application|Factory|RedirectResponse|View
     *@use Create new pipeline
     */
    public function create()
    {
        if($this->checkUserPermission()){

            if($this->checkBasicData()){
                $data = $this->preparedData();
                return view('security-pipeline.create', $data);
            }else{
                return redirect()->route("security-pipeline.index")->with('warning', 'Data issue! The CB Manager does not have the necessary data to create pipelines');
            }
        }

        return redirect()->route("security-pipeline.index")->with('warning', 'You are not authorize to access this page!');
    }


    /**
     * To be able to create pipelines, there needs to be at least one agent, agent instance, exec env,
     */
    public function checkBasicData()
    {
        $execEnvs = $this->countExecuteEnvironments();
        $agents = count($this->getAgents());
        $agentInstances = count($this->getAgentInstances());

        if($execEnvs && $agents && $agentInstances){
            return true;
        }else{
            return false;
        }
    }

    /**
     * @use Store new pipeline information to context broker end
     * @param Request $request
     * @return RedirectResponse
     * @throws \Exception
     */
    public function store(Request $request){
        $this->validateCreateUpdate($request->all())->validate();
        $pipeline = new Pipelines();

        $pipeline->uuid = $request->get('agent_id').'-'.strtotime('now');
        $pipeline->name = $request->get('name');
        $pipeline->policy = $request->get('policy');
        $pipeline->description = $request->get('name');
        $pipeline->status = "created";
        $pipeline->user_id = Auth::user()->id;
        $pipeline->agents = ['id'=> $request->get('agent_id')];

        if($pipeline->save()) {
            $preparedData = $this->prepareJsonDataForApi($request, $pipeline);
            $status = $this->crudPipelineApi($preparedData, 'create');

            if($status === 'success'){
                return redirect()->route("security-pipeline.index")->with('success', 'Security pipeline created successfully!');
            }
            return redirect()->back()->with('error', 'Failed to save pipeline on CB! Try again');
        }
        return redirect()->back()->with('error', 'Failed to save pipeline! Try again');
    }

    /**
     * @param $id
     * @return Application|Factory|RedirectResponse|View
     * @use Edit existing pipeline
     */
    public function edit($id){
        if($this->checkUserPermission()){

            if($this->checkBasicData()){
                $data = $this->preparedData();
                $data['pipeline'] = $this->findOrFailPipeline($id)[0];
                $data['pipeline']['agent_id'] = isset($data['pipeline']['agent_catalog_id']) ? $data['pipeline']['agent_catalog_id'] : $data['pipeline']['agent_configs']['agent_catalog_id'];
                return view('security-pipeline.edit', $data);
            }else{
                return redirect()->route("security-pipeline.index")->with('warning', 'The CB Manager data is not complete for creating/editing Pipelines!');
            }
        }
        return redirect()->route("security-pipeline.index")->with('warning', 'You are not authorize to access this page!');
    }

    /**
     * @use Update pipeline information
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     * @throws \Exception
     */
    public function update(Request $request, $id){

        $this->validateCreateUpdate($request->all())->validate();
        $pipelineData = $this->findOrFailPipeline($id)[0];
        $pipeline = Pipelines::updateOrCreate(
            [
                'uuid' => $pipelineData['id']
            ],
            [
                'uuid' => $pipelineData['id'],
                'agents' => ['id'=> $request->get('agent_id')],
                'name' => $request->get('name'),
                'policy' => $request->get('policy'),
                'description' => $request->get('name'),
                'user_id' => Auth::user()->id,
                'status' => $pipelineData['status'],
                'created_at' => $pipelineData['created_at'],
                'updated_at' => Carbon::now()
            ]
        );

        if($pipeline->exists) {
            $preparedData = $this->prepareJsonDataForApi($request, $pipeline);
            //only for create. --TODO-change this later in the prepare, as this is now checked by the CB.

            unset($preparedData['created_at']);

            $status = $this->crudPipelineApi($preparedData, 'edit');

            if($status === 'success'){
                return redirect()->route("security-pipeline.index")->with('success', 'SecurityPipeline updated successfully!');
            }
            return redirect()->back()->with('error', 'Failed to updated Information On CB! Try again');
        }
        return redirect()->back()->with('error', 'Failed to updated Information! Try again');

    }

    /**
     * @use Validate the form required input field value.
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validateCreateUpdate(array $data)
    {
        $rules = [
            'name' => 'required',
            'environment_id' => 'required'
        ];

        $message = [
            'environment_id.required' => 'Agent Instance is required!',
            'name.required' => 'Name is required'
        ];

        return Validator::make($data, $rules, $message);
    }

    /**
     * @use Delete the specific pipeline from context broker
     * @param $id
     * @return RedirectResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        if(!$this->checkUserPermission()){
            return redirect()->route("security-pipeline.index")->with('warning', 'You are not authorize to access this page!');
        }

        $currentStatus = $this->getPipelineStatus($id);

        if($currentStatus === 'started'){
            //need to stop pipeline
            $pipeline = Pipelines::query()->findOrFail($id);
            $pipeline['status'] = 'stop';
            $this->stopPipelineSC($pipeline);

            $scStatus = '';
            $count = 0;

            while($scStatus != 'started' || $scStatus != 'stopped'){
                $count++;
                if($count > 5){
                    sleep(5);
                    $scStatus = $this->getPipelineStatus($id);
                    break;
                }else{
                    $scStatus = $this->getPipelineStatus($id);
                }
            }

            if($scStatus == 'stopped'){
                $deletePipeline = [
                    'where' => [
                        'equals' => [
                            "target" => "id",
                            "expr" =>  $id,
                        ],
                    ],
                ];

                $status = $this->crudPipelineApi($deletePipeline, 'delete');
                if($status === 'success'){
                    $pipeline = Pipelines::query()->where('uuid', $id)->first();
                    if(!empty($pipeline)){
                        $pipeline->delete();
                    }
                    return redirect()->back()->with('success','SecurityPipeline deleted successfully!');
                }
                return redirect()->back()->with('warning','Unable to delete from CB! Please try again');
            }else{
                return redirect()->back()->with('warning','Unable to delete from CB! The pipeline was not stopped successfully. Please try again.');
            }



        }else{
            //just delete
            $deletePipeline = [
                'where' => [
                    'equals' => [
                        "target" => "id",
                        "expr" =>  $id,
                    ],
                ],
            ];

            $status = $this->crudPipelineApi($deletePipeline, 'delete');
            if($status === 'success'){
                $pipeline = Pipelines::query()->where('uuid', $id)->first();
                if(!empty($pipeline)){
                    $pipeline->delete();
                }
                return redirect()->back()->with('success','SecurityPipeline deleted successfully!');
            }

            return redirect()->back()->with('warning','Unable to delete from CB! Please try again');
        }
    }

    /**
     * @use using to prepare the Agent wise Environment data
     * @param $agents
     * @param $environments
     * @param $agentInstances
     * @return mixed
     */
    public function connectEnvironmentsAgents($agents, $environments, $agentInstances)
    {
        for($i = 0; $i<count($environments); $i++){
            $index = 0;
            foreach ($agentInstances as $instance){
                if($instance['exec_env_id'] === $environments[$i]['id']){
                    foreach ($agents as $agt){
                        if($instance['agent_catalog_id'] === $agt['id']){
                            $environments[$i]['agents'][$index] = $agt;
                            $environments[$i]['agents'][$index]['instance'] = $instance;
                            $index++;
                        }
                    }
                }
            }
        }

        return $environments;
    }

    /**
     * @use using to prepare agent wise Execution Environment data
     * @param $agents
     * @param $environments
     * @param $agentInstances
     * @return array
     * @throws \Exception
     */
    public function agentWiseExecEnvironment($agents, $environments, $agentInstances)
    {
        $pipeLineData = $this->getPipelines();
        $agentWiseInstanceAndEnv = [];
        for ($i = 0; $i < count($agents); $i++) {
            $index = 0;
            $agentWiseInstanceAndEnv[$i]['agents'] = $agents[$i];

            foreach ($agentInstances as $keyIns => $instance) {

                if ($instance['agent_catalog_id'] === $agents[$i]['id']) {
                    $agentWiseInstanceAndEnv[$i]['instance'][$index] = $instance;

                    foreach ($environments as $keyEnv => $env) {
                        if ($instance['exec_env_id'] === $env['id']) {
                            $env['agent_id'] = $agents[$i]['id'];
                            $instanceExists = $this->checkAgentInstanceExistsInPipeline($pipeLineData, $env);
                            $env['duplicate'] = $instanceExists;
                            if(isset($instanceExists['status'])){
                                $env['duplicate'] = $instanceExists['status'];
                                $env['pipelineName'] = $instanceExists['pipelineName'];
                            }
                            $agentWiseInstanceAndEnv[$i]['environment'][$index] = $env;
                            $index++;
                        }
                    }
                }
            }
        }
        return $agentWiseInstanceAndEnv;
    }

    /**
     * @param $algorithms
     * @param $algorithmInstances
     * @return array
     */
    public function getActiveAlgorithms($algorithms)
    {
        $pipeLineData = $this->getPipelines();
        $activeAlgos = [];

        foreach ($pipeLineData as $pipeline){
            foreach ($algorithms as $algo){
                if(isset($pipeline['algorithm_catalog_id']) && $algo['id'] === $pipeline['algorithm_catalog_id']) {
                    $activeAlgos[] = $algo['id'];
                }
            }
        }

        return $activeAlgos;
    }

    /**
     * @param $pipelineData
     * @param $agentInstance
     * @return array|false
     * @throws \Exception
     */
    public function checkAgentInstanceExistsInPipeline($pipelineData, $agentInstance){

        $agentInstanceExists = false;
        $agentInstanceId = $this->getAgentInstanceIdByAgentAndEnv($agentInstance['agent_id'], $agentInstance['id']);
        foreach($pipelineData as $key => $pipeline){

            $pipelineAgentInstances = [];
            if(isset($pipeline['agent_configs']) && !empty($pipeline['agent_configs'])) {
                $pipelineAgentInstances = $pipeline['agent_configs'];
            } else if(!empty($pipeline['agent_instances']) && is_array($pipeline['agent_instances'])) {
                $pipelineAgentInstances = $pipeline['agent_instances'];
            }

            $pipelineInstances = array_column($pipelineAgentInstances, 'agent_instance_id');
            if (in_array($agentInstanceId, $pipelineInstances)) {
                return $agentInstanceExists = ['status' => true, 'pipelineName' => $pipeline['name']];
            }
        }

        return $agentInstanceExists;
    }

    /**
     * @param $pipelineData
     * @param $algoInstance
     * @return array|false
     */
    public function checkAlgorithmInstanceExistsInPipeline($pipelineData, $algoInstance){
        $algoInstanceExists = false;
        $algoInstanceId = $algoInstance['id'];
        foreach($pipelineData as $key => $pipeline){
            $pipelineAlgorithmInstances = [];
            if(isset($pipeline['algorithm_config']) && !empty($pipeline['algorithm_config'])) {
                $pipelineAlgorithmInstances = $pipeline['algorithm_config'];
            } else if(isset($pipeline['algorithm_configs']) && !empty($pipeline['algorithm_configs'])) {
                $pipelineAlgorithmInstances = $pipeline['algorithm_configs'];
            }

            $pipelineInstances = array_column($pipelineAlgorithmInstances, 'algorithm_instance_id');
            if (in_array($algoInstanceId, $pipelineInstances)) {
                return $agentInstanceExists = ['status' => true, 'pipelineName' => $pipeline['name']];
            }
        }

        return $algoInstanceExists;
    }

    /**
     * @use Prepare agent parameter data and send back on ajax request
     * @param Request $request
     * @return JsonResponse
     * @throws \Throwable
     */
    public function getAgentParameter(Request $request){
        $pipelineParam = [];
        if(!empty($request->input('pipelineId'))){
            $pipeline = $this->findOrFailPipeline($request->input('pipelineId'))[0];
            $pipelineParam['agentId'] = isset($pipeline['agent_catalog_id']) ? $pipeline['agent_catalog_id'] : $pipeline['agent_configs']['agent_catalog_id'];

            if(isset($pipeline['agent_configs']) && is_array($pipeline['agent_configs'])){
                foreach($pipeline['agent_configs'] as $key=>$val){
                    $agentInstanceId = isset($val['agent_instance_id']) ? $val['agent_instance_id'] : $pipeline['agent_instances'][0]['agent_instance_id'];
                    $envInstance = (!empty($this->getEnvByInstanceId($agentInstanceId))) ? $this->getEnvByInstanceId($agentInstanceId)->json()[0]['exec_env_id'] : false;

                    if(!$envInstance){
                        return response()->json(['html' => '', 'status' => 'error']);
                    }


                    if(isset($val['parameters']) && is_array($val['parameters'])){
                        $val['parameters']['agentInstanceId'] = $val['agent_instance_id'];
                        $pipelineParam[$envInstance] = $val['parameters'];
                    }

                   if(isset($val['resources']) && is_array($val['resources'])){
                       $val['resources']['agentInstanceId'] = $val['agent_instance_id'];
                        $pipelineParam['resources'][$envInstance] = $val['resources'];
                    }
                }
            }
        }

        $agentId = $request->input('agentId');
        $agentParams = [];
        $resourceOptions = [];
        if(!empty($request->input('agents'))) {
            $agents = $request->input('agents');
            foreach ($agents as $key => $agent) {
                if (!empty($agent['parameters']) && $agent['id'] === $agentId) {
                    $agentParams['agents'] = $agent;
                    $agentParams['parameters'] = $agent['parameters'];
                }else if(isset($agent['resources']) && !empty($agent['resources']) && $agent['id'] === $agentId){
                    foreach ($agent['resources'] as $resource){
                        $resourceOptions[] = $resource['id'];
                    }
                }
            }
        }

        if(isset($agentParams['agents']['resources']) && !empty($agentParams['agents']['resources'])){
            foreach ($agentParams['agents']["resources"] as $resource){
                $resourceOptions[] = $resource['id'];
            }
        }

        $selectedInstanceArr = [];

        if(!empty($request->input('selectedInstanceArr'))){
            $selectedInstanceArr = $request->input('selectedInstanceArr');
        }

        $status = 'success';
        $html = view('security-pipeline.partials.agent-parameters', [
            'agentParams' => $agentParams,
            'pipeline' => $pipelineParam,
            'resourceOptions' => $resourceOptions,
            'selectedInstanceArr' => $selectedInstanceArr,
        ])->render();

        return response()->json(['html' => $html, 'status' => $status]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Throwable
     */
    public function getAlgorithmParameter(Request $request): JsonResponse
    {
        $input = $request->all();
        $pipelineParam = [];
        if(!empty($input['pipelineId'])){
            $pipeline = $this->findOrFailPipeline($input['pipelineId'])[0];
            $pipelineParam['algorithmId'] = isset($pipeline['algorithm_catalog_id']) ? $pipeline['algorithm_catalog_id'] : null;

            if(isset($pipeline['algorithm_configs']) && is_array($pipeline['algorithm_configs'])){
                foreach($pipeline['algorithm_configs'] as $key=>$val){
                    $algorithmInstanceId = isset($val['algorithm_instance_id']) ? $val['algorithm_instance_id'] : null;
                    $strArray = explode('@', $algorithmInstanceId);
                    $envInstance = end($strArray);

                    if(isset($val['parameters']) && is_array($val['parameters'])){
                        $val['parameters']['$algorithmInstanceId'] = $val['algorithm_instance_id'];
                        $pipelineParam[$envInstance] = $val['parameters'];
                    }

                    if(isset($val['resources']) && is_array($val['resources'])){
                        $val['resources']['$algorithmInstanceId'] = $val['algorithm_instance_id'];
                        $pipelineParam['resources'][$envInstance] = $val['resources'];
                    }
                }
            }
        }

        $algorithmId = isset($input['algorithmId']) ? $input['algorithmId'] : null;
        $algorithmParams = [];
        $algorithmResource = [];

        if(!empty($input['algorithms'])) {
            foreach ($input['algorithms'] as $key => $algorithm) {
                if (!empty($algorithm['parameters']) && $algorithm['id'] === $algorithmId) {
                    $algorithmParams['algorithm'] = $algorithm;
                    $algorithmParams['parameters'] = $algorithm['parameters'];
                }else if(isset($algorithm['resources']) && !empty($algorithm['resources']) && $algorithm['id'] === $algorithmId){
                    foreach ($algorithm['resources'] as $resource){
                        $algorithmResource[] = $resource['id'];
                    }
                }
            }
        }

        if(isset($algorithmParams['algorithms']["resources"]) && !empty($algorithmParams['algorithms']["resources"])){
            foreach ($algorithmParams['algorithms']["resources"] as $key=>$resource){
                $algorithmResource[] = $resource['id'];
            }
        }

        $algoSelectedInstanceArr = [];
        if(!empty($input['algoSelectedInstanceArr'])){
            $algoSelectedInstanceArr = $input['algoSelectedInstanceArr'];
        }

        $status = 'success';
        $html = view('security-pipeline.partials.algorithm-parameters', [
            'algorithmParams' => $algorithmParams,
            'pipeline' => $pipelineParam,
            'resourceOptions' => $algorithmResource,
            'selectedInstanceArr' => $algoSelectedInstanceArr,
        ])->render();

        return response()->json(['html' => $html, 'status' => $status]);
    }

    /**
     *
     * @use Using to update pipeline status to context broker
     * @param Request $request
     * @return JsonResponse
     */
    public function updatePipelineStatus(Request $request)
    {
        $pipelineId = $request->input('pipelineId');
        $pipelineData = $this->findOrFailPipeline($pipelineId)[0];


        /*SPECIAL_CASE SC expects agent_config->parameters->config to be an array. This is not the case when only one entry.
          So make sure that only arrays are sent to the SC and if its not an array change to array.
        */
        if(isset($pipelineData['agent_configs'])){
            foreach ($pipelineData['agent_configs'] as $key=>$config){
                if(isset($config['parameters'])){
                    foreach ($config['parameters'] as $parKey=>$parameter){
                        if(!is_array($parameter['config']['path'])){
                            $pipelineData['agent_configs'][$key]['parameters'][$parKey]['config']['path'] = [$parameter['config']['path']];
                        }
                    }
                }
            }
        }

        if($request->input('status') === 'start'){
            $pipelineData['status'] = 'start';
            $this->startPipelineSC($pipelineData);
        }else if($request->input('status') === 'stop'){
            $pipelineData['status'] = 'stop';
            $this->stopPipelineSC($pipelineData);
        } else if($request->input('status') === 'restart'){
            /* In case of restart first stop and then start the pipeline */
            $stopPipelineData = $pipelineData;
            $stopPipelineData['status'] = 'stop';
            $stopResult = $this->stopPipelineSC($stopPipelineData);

            if(!empty($stopResult) && $stopResult['status'] === 'success'){//--TODO-verify response from SC.
                $startPipelineData = $pipelineData;
                $startPipelineData['status'] = 'start';
                $startResult = $this->startPipelineSC($startPipelineData);
            }
        }

        $scStatus = '';
        $count = 0;

        while($scStatus != 'started' || $scStatus != ' stopped'){
            //wait
            $count++;
            if($count > 5){
                sleep(5);
                $scStatus = $this->getPipelineStatus($pipelineId);
                break;
            }else{
                $scStatus = $this->getPipelineStatus($pipelineId);
            }
        }

        if($scStatus === 'started' || $scStatus === 'stopped'){
            // update pipeline status to context broker
            $status = $this->crudPipelineApi(['id' => $pipelineId, 'status' => $scStatus], 'edit');
            if($status === 'success'){
                $pipeline = $securityPipeline = Pipelines::query()->where('uuid', $pipelineId)->first();
                if(!empty($pipeline)){
                    $pipeline->status = $scStatus;
                    $pipeline->update();
                }

                return response()->json(['status' => "success", 'updatedStatus' => $scStatus]);
            }
        }else{
            $this->crudPipelineApi(['id' => $pipelineId, 'status' => 'created'], 'edit');
            return response()->json(['status' => 'failed', 'updatedStatus' => $scStatus]);
        }

        return response()->json(['status' => 'failed', 'updatedStatus' => $scStatus]);
    }

    /**
     *
     * @param Request $request
     * @param $pipeline
     * @return array
     * @throws \Exception
     * @use Using to prepare the api request data
     */
    public function prepareJsonDataForApi(Request $request, $pipeline){
        $pipeline = Pipelines::with('user')->findOrFail($pipeline->id);
        $agentId = $pipeline['agents']['id'];
        $pipelineData = [];
        $pipelineData['agent_catalog_id'] = $agentId;

        if($request->input("algorithm_id") !== null){
            $pipelineData['algorithm_catalog_id'] = $request->input("algorithm_id");
        }


        $pipelineData['id'] = $pipeline->uuid;
        $pipelineData['name'] = $pipeline->name;
        $pipelineData['policy'] = ($request->input('policy')) ? $request->input('policy') : '';
        $pipelineData['status'] = $pipeline->status;
        $pipelineData['created_at'] = strtotime($pipeline->created_at);
        $pipelineData['updated_at'] = strtotime($pipeline->updated_at);
        $pipelineData['user'] = $pipeline->user->name;

        $agentConfigs = $this->prepareAgentConfig($request, $agentId);
//        $algorithmConfigs = $this->prepareAlgorithmConfig($request);

        $pipelineData['agent_configs'] = $agentConfigs;
//        $pipelineData['algorithm_configs'] = $algorithmConfigs;

        return $pipelineData;
    }

    /**
     * @use This function is used to prepare pipeline data with agents parameters and resources
     * @param Request $request
     * @param $agentId
     * @return array
     * @throws \Exception
     */
    public function prepareAgentConfig(Request $request, $agentId){
        $agentConfigs = [];
        if (!empty($request->input("environment_id")) && is_array($request->input("environment_id"))) {
            foreach ($request->input("environment_id") as $key => $env) {
                $agentConfigs[$key]['agent_instance_id'] = $this->getAgentInstanceIdByAgentAndEnv($agentId, $env);
                if (!empty($request->input("param")) && is_array($request->input("param"))) {
                    $instanceParams = $request->input("param")[$env];
                    foreach ($instanceParams as $keyParam => $value) {
                        $agentConfig = json_decode($value['agent_configs'], true);
                        $agentConfig['input'] = isset($value['input']) ? $value['input'] : "";
                        unset($agentConfig['example']);
                        $agentConfigs[$key]['parameters'][] = $agentConfig;
                    }
                }

                if (!empty($request->input("resource")[$env]) && is_array($request->input("resource")[$env])) {

                    $input = $request->all();
                    $resourceData = $input['resource'][$env];
                    $tempResource = [];
                    foreach($resourceData as $resourceKey => $resources){
                        foreach($resources as $resource) {
                            $resourceId = $resource['resource_id'];
                            $resourceContent = "";
                            if (!empty($resource['content_file'])) {
                                $resourceContent = file_get_contents($resource['content_file']);
                            } else if (!empty($resource['content'])) {
                                $resourceContent = $resource['content'];
                            }

                            $tempResource[] = [
                                'id' => $resourceId,
                                'content' => $resourceContent,
                            ];
                        }

                        $agentConfigs[$key]['resources'] = $tempResource;
                    }
                }
            }
        }

        return $agentConfigs;
    }

    /**
     * @use This function is used to prepare pipeline data with algorithm parameters and resources
     * @param Request $request
     * @return array
     */
    public function prepareAlgorithmConfig(Request $request){
        $algorithmConfigs = [];

        if(!empty($request->input("algorithm_id"))) {
            if (!empty($request->input("algorithm_environment_id"))){
                foreach ($request->input("algorithm_environment_id") as $instanceKey => $algorithmInstanceId){
                    if (!empty($request->input("algorithmParam")) && is_array($request->input("algorithmParam"))) {
                        $algoParams = $request->input("algorithmParam")[$algorithmInstanceId];
                        foreach ($algoParams as $key => $param) {
                            $algoConfig = json_decode($param['algo_config'], true);
                            $algoConfig['input'] = isset($param['input']) ? $param['input'] : "";
                            unset($algoConfig['example']);
                            $algorithmConfigs[$instanceKey]["algorithm_instance_id"] = $algorithmInstanceId;
                            $algorithmConfigs[$instanceKey]['parameters'][$key] = $algoConfig;
                        }
                    }
                }
            }
        }

        return $algorithmConfigs;
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

    /**
     *
     * @use This function retrieves the specific ($id) pipeline
     * Retrieves the specific ($pipelineAgentInstanceId) agent instance
     * Update the agentinstance old value to pipeline agent_config.
     *
     * @param $pipelineId
     * @return RedirectResponse
     */
    public function reloadPipelineInstance($pipelineId){
        if($this->checkUserPermission()) {
            $data['pipeline'] = $this->findOrFailPipeline($pipelineId)[0];
            if (!empty($data['pipeline']['agent_instances'])) {
                $pipelineAgentInstanceId = $data['pipeline']['agent_instances'][0]['agent_instance_id'];
                $pipelineParameters = [];
                $agentInstanceData = $this->findOrFailAgentInstance($pipelineAgentInstanceId);
                if(isset($agentInstanceData['error'])) {
                    return redirect()->back()->with('error', $agentInstanceData['message']);
                }

                if(isset($data['pipeline']['agent_configs']['parameters'])) {
                    $pipelineParameters = $data['pipeline']['agent_configs']['parameters'];
                    $agentInstanceData = $agentInstanceData[0];
                    if (!empty($agentInstanceData['parameters']) && !empty($pipelineParameters)) {
                        foreach ($agentInstanceData['parameters'] as $key => $parameter) {
                            $searchKey = array_search($parameter['id'], array_column($pipelineParameters, 'id'));
                            if (!empty($parameter['value']['old'])) {
                                $pipelineParameters[$searchKey]['input'] = $parameter['value']['old'];
                            }
                        }
                    }
                } else {
                    $pipelineParameters['pipeline'] = $data['pipeline'];
                }

                $data['pipeline']['agent_configs']['parameters'] = $pipelineParameters;
                unset($data['pipeline']['created_at']);

                $status = $this->crudPipelineApi($data['pipeline'], 'edit');

                if ($status === 'success') {
                    return redirect()->route("security-pipeline.index")->with('success', 'SecurityPipeline reloaded successfully!');
                }
                return redirect()->back()->with('error', 'Failed to updated Information On CB! Try again');
            }
            return redirect()->back()->with('error', 'Failed to updated Information On CB! Try again');
        }
        return redirect()->back()->with('error', 'Failed to updated Information! Try again');
    }

}