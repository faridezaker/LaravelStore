<?php

namespace App\Services;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

abstract class BaseService
{
    protected $model;

    public function all($perPage=3)
    {
        return $this->model::query()->paginate($perPage);
    }

    public function find($id)
    {
        return $this->model::findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model::create($data);
    }

    public function update($brand, array $data)
    {
        $brand->update($data);
        return $brand->fresh();
    }

    public function delete($id)
    {
        $item = $this->find($id);
        return $item->delete();
    }

}
