<?php

namespace App\Services;

use App\Models\Brand;

class BrandService extends BaseService
{
    public function __construct()
    {
        $this->model = Brand::class;
    }
}
