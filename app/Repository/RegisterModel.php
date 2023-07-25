<?php

namespace App\Repository;

use App\Components\AuthenticationComponent;
use App\Components\DataComponent;
use App\Models\User;
use App\Models\UserGroup;
use App\Services\UserService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class RegisterModel
{
    public $request;
    public $account;

    public function addRegister(Request $request) 
    {

        return UserService::register($request);

    }
}
