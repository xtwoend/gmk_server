<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Device;
use App\Resource\DeviceResource;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;

class DeviceController
{
    public function index(RequestInterface $request)
    {
        $rpp = (int) $request->input('rowsPerPage', 25);
        $model = Device::select('*');
        
        if($request->has('sortBy')) {
            $column = $request->input('sortBy');
            $dir = $request->input('sortType');
            $model = $model->orderBy($column, $dir);
        }

        $model = $model->paginate($rpp);

        return response(DeviceResource::collection($model));
    }

    public function store(RequestInterface $request)
    {
        $device = new Device;
        $device->fill($request->all());
        $device->save();

        return response(new DeviceResource($device));
    }

    public function show($id)
    {
        $device = Device::findOrFail($id);
        return response(new DeviceResource($device));
    }

    public function update($id, RequestInterface $request)
    {
        $device = Device::findOrFail($id);
        $device->fill($request->all());
        $device->save();

        return response(new DeviceResource($device));
    }

    public function delete($id)
    {
        $device = Device::findOrFail($id);
        if($device->delete()) {
            return response(['status' => 'success']);
        }

        return response(['status' => 'failed']);
    }
}
