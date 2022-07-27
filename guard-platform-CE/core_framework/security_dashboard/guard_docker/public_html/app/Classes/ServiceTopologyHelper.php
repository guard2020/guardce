<?php


namespace App\Classes;


class ServiceTopologyHelper
{

    public static function addPosition($id){
        switch($id){
            case 'user-vm':
                return  [
                    'x' => 225,
                    'y' => 300
                ];
                
            case 'simulation-model-tool':
                return [
                    'x' => 75,
                    'y' => 300
                ];
                
            case 'backend-test':
                return [
                    'x' => 0,
                    'y' => 0
                ];
                
            case 'backend-backup':
                return [
                    'x' => 375,
                    'y' => 300
                ];
                
            case 'digit-cyber':
                return [
                    'x' => 300,
                    'y' => 150
                ];
                
            case 'alpha-service':
                return [
                    'x' => 75,
                    'y' => 150
                ];
            case 'vision-tech':
                return [
                    'x' => 525,
                    'y' => 150
                ];
                
            case 'lora':
                return [
                    'x' => 525,
                    'y' => 300
                ];
                
            case 'network-manager':
                return [
                    'x' => 450,
                    'y' => 0
                ];
                
            case 'web-server':
                return [
                    'x' => 600,
                    'y' => 0
                ];
            case 'frontend':
                return [
                    'x' => 300,
                    'y' => 0
                ];
            case 'backend-prod':
                return [
                    'x' => 150,
                    'y' => 0
                ];
            case 'mobile-phone':
                return [
                    'x' => 50,
                    'y' => 450
                ];
            default:
                return [
                    'x' => 250,
                    'y' => 450
                ];
        }
    }

}