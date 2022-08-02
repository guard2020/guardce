<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::group([], function()
{
    Route::get('/register', 'Auth\RegisterController@showRegistrationForm')->name('register');
    Route::post('/register', 'Auth\RegisterController@register')->name('register');
    Route::get('/login', 'Auth\LoginController@login')->name('login');
    Route::post('/checkLogin', 'Auth\LoginController@checkLogin')->name('login.check');
    Route::get('/logout', 'Auth\LoginController@logout')->name('logout');


    Route::get('/', 'securityDashboardController@index')->name('dashboard');
    Route::get('/getDataCounts', 'securityDashboardController@getData')->name('data.counts');


    Route::group([
        'as' => 'anomalies.',
        'prefix' => 'anomalies-dashboard'
    ], function() {
        Route::get('/', 'anomaliesAnalysisController@index')->name('index');
    });

    Route::group([
        'as' => 'notifications.',
        'prefix' => 'notifications'
    ], function() {
        Route::get('/', 'notificationsController@index')->name('index');
        Route::get('/index/dt', 'notificationsController@ajaxNotificationsTable')->name('index.dt');
        Route::get('/reload/index/dt', 'notificationsController@ajaxReloadNotificationsTable')->name('reload.index.dt');
        Route::get('/checkNew', 'notificationsController@ajaxCheckNewNotifications')->name('checkNew');
    });



});


Route::group([
    'as' => 'data-trace.',
    'prefix' => 'data-trace'
], function() {
    // Route::redirect('/', '/data-trace/requests', 301);
    Route::get( '/{path?}', 'dataTraceController@index')->where('path', '.*')->name('index');
});

Route::group([
    'as' => 'service-topology.',
    'prefix' => 'servicetopology'
], function(){
    Route::get('/', 'serviceTopologyController@index')->name('index');
    Route::post('/chain/discover/', 'serviceTopologyController@discoverChain')->name('chain.discover');
    Route::delete('/chain/delete/{hostname}', 'serviceTopologyController@destroy')->name('chain.delete');
    Route::get('/chain/', 'serviceTopologyController@discoveryChainIndex')->name('chain.index');
    Route::get('/chain/index/dt', 'serviceTopologyController@datatables')->name('chain.index.dt');
});

Route::group([
    'as' => 'security-pipeline.',
    'prefix' => 'security-pipeline'
], function(){
    Route::post('/algorithm-parameter', 'securityPipelineController@getAlgorithmParameter')->name('algorithm-parameter');
    Route::post('/update-config-param', 'securityPipelineController@updateResourceConfig')->name('update-config-param');
    Route::get('/', 'securityPipelineController@index')->name('index');
    Route::get('/index/dt', 'securityPipelineController@datatables')->name('index.dt');
    Route::get('/create/', 'securityPipelineController@create')->name('create');
    Route::post('/store/', 'securityPipelineController@store')->name('store');
    Route::get('/{securityPipeline}/edit', 'securityPipelineController@edit')->name('edit');
    Route::post('/agent-parameter', 'securityPipelineController@getAgentParameter')->name('agent-parameter');
    Route::post('/status', 'securityPipelineController@updatePipelineStatus')->name('status');
    Route::get('/status/get', 'securityPipelineController@getPipelineStatus')->name('status.get');
    Route::post('/{securityPipeline}', 'securityPipelineController@update')->name('update');
    Route::delete('/{securityPipeline}', 'securityPipelineController@destroy')->name('destroy');
    Route::get('/{securityPipeline}/reload', 'securityPipelineController@reloadPipelineInstance')->name('reloadPipelineInstance');

    Route::get('/add-config-panel', 'securityPipelineController@addConfigPanel')->name('add-config-panel');
});

Route::group([
    'as' => 'users.',
    'prefix' => 'users'
], function(){
    Route::get('/index/dt', 'UserController@datatables')->name('profile.index.dt');
    Route::get('list/', 'UserController@index')->name('index');
    Route::get('profile/', 'UserController@profile')->name('profile');
    Route::get('setting/', 'UserController@setting')->name('setting');
    Route::post('/{user}/profile', 'UserController@update')->name('update');
    Route::post('/{user}/setting', 'UserController@updateSetting')->name('updateSetting');
    Route::get('users/', 'UserController@userList')->name('users');
    Route::delete('/{user}', 'UserController@destroy')->name('destroy');
    Route::post('/update-status', 'UserController@updateStatus')->name('update-status');
    Route::post('/update-role', 'UserController@updateRole')->name('update-role');
});