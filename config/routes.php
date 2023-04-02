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
use App\Controller\TableController;
use App\Controller\TrendController;
use App\Controller\DeviceController;
use Hyperf\HttpServer\Router\Router;
use App\Controller\MqttLogController;

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

Router::get('/alarm/{deviceId}/data', [AlarmController::class, 'data']);
Router::get('/alarm/{deviceId}/export', [AlarmController::class, 'export']);

Router::get('/favicon.ico', function () {
    return '';
});
