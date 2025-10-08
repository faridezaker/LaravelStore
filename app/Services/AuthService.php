<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class AuthService extends BaseService
{
    public function __construct()
    {
        $this->model = User::class;
    }

    public function create($data)
    {
        $data['password'] = bcrypt($data['password']);
        return parent::create($data);
    }

}
