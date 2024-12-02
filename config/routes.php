<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
use App\Controller\AlarmController;
use App\Controller\ScoreController;
use App\Controller\TableController;
use App\Controller\TrendController;
use App\Controller\DeviceController;
use App\Controller\IndexController;
use App\Controller\ReportController;
use Hyperf\HttpServer\Router\Router;
use App\Controller\MqttLogController;
use App\Controller\RestApiController;

Router::addRoute(['GET', 'POST', 'HEAD'], '/', 'App\Controller\IndexController@index');

Router::get('/devices', [DeviceController::class, 'index']);
Router::get('/devices/{id}', [DeviceController::class, 'show']);
Router::post('/devices', [DeviceController::class, 'store']);
Router::put('/devices/{id}', [DeviceController::class, 'update']);
Router::delete('/devices/{id}', [DeviceController::class, 'delete']);

Router::get('/trend/{deviceId}/data', [TrendController::class, 'data']);
Router::get('/table/{deviceId}/data', [TableController::class, 'dataUnion']);
Router::get('/table/{deviceId}/export', [TableController::class, 'export']);
Router::get('/mqtt/{deviceId}/log', [MqttLogController::class, 'data']);

// alarm
Router::get('/alarm/{deviceId}/summary', [AlarmController::class, 'summary']);
Router::get('/alarm/{deviceId}/data', [AlarmController::class, 'data']);
Router::get('/alarm/{deviceId}/export', [AlarmController::class, 'export']);

// score
Router::get('/score/{deviceId}/history', [ScoreController::class, 'history']);
Router::get('/score/{deviceId}/timesheet-history', [ScoreController::class, 'timesheetHistory']);
Router::post('/score/{deviceId}', [ScoreController::class, 'store']);
Router::get('/score/{deviceId}', [ScoreController::class, 'index']);
Router::get('/score/timesheets/{id}', [ScoreController::class, 'score']);

// score setting
Router::get('/setting/score/{deviceId}', [ScoreController::class, 'getSetting']);
Router::post('/setting/score/{deviceId}', [ScoreController::class, 'setSetting']);

// current shift
Router::get('/shift/current', [ScoreController::class, 'getCurrentShift']);

// report
Router::get('/report/{id}', [ReportController::class, 'data']);
Router::get('/report/{id}/list', [ReportController::class, 'list']);
Router::get('/report/{id}/export', [ReportController::class, 'export']);

// sync 
Router::post('/sync/{table}', [RestApiController::class, 'sync']);


Router::get('/parse', [IndexController::class, 'extract']);

Router::get('/favicon.ico', function () {
    return '';
});
