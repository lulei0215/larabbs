<?php

namespace App\Http\Controllers\Api;

use app\admin\controller\Base;
use App\Models\Traits\ActiveUserHelper;
use App\Api\Helpers\ApiResponse;
use Dingo\Api\Routing\Helpers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller as BaseController;

class Controller extends BaseController
{
    use Helpers,ApiResponse;

}
