<?php
namespace App\Http\API;

use Illuminate\Support\Facades\Http;

trait SecurityControllerApiTrait
{
    /**
     * This function is used the update the pipeline status to SC (Smart Controller)
     * @param $pipelineData
     * @return string|null
     */
    public function startPipelineSC($pipelineData)
    {
        $endpoint = config('constants.sc_api').'/startSecurityPipeline';
        $response = Http::put($endpoint, $pipelineData);
        if(isset($response[0]['status'])){
            return $response;
        }else{
            return $response->body();
        }
    }

    public function stopPipelineSC($pipelineData)
    {

        $endpoint = config('constants.sc_api').'/stopSecurityPipeline';
        $response = Http::put($endpoint, $pipelineData);
        if(isset($response[0]['status'])){
            return $response;
        }else{
            return $response->body();
        }
    }

    public function getPipelineStatus($pipelineId)
    {
        try{
            $endpoint = config('constants.sc_api').'/getSecurityPipelineStatus?pipelineId='.$pipelineId;
            $response = Http::get($endpoint);
            if(isset($response[0]['status'])){
                return [];
            }else{
                return $response->body();
            }
        }catch (\Throwable $e){
            report($e);

            return [];
        }
    }
}