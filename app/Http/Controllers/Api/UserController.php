<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\UserRequest;
use App\Users;
use Illuminate\Http\Request;
//use Illuminate\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as BaseController;

class UserController extends BaseController
{
    public function store(UserRequest $request)
    {
        $verifyData = \Cache::get($request->verification_key);

        if (!$verifyData) {
            return '验证码已失效';
//            return $this->response->error('验证码已失效', 422);
        }
//        dd($verifyData['code'].'-----'.$request->verification_code);
        if (!hash_equals((string)$verifyData['code'], $request->verification_code)) {
            // 返回401

            return '验证码错误';
        }

        $user = Users::create([
            'name' => $request->name,
            'phone' => $verifyData['phone'],
            'password' => bcrypt($request->password),
        ]);
    
        // 清除验证码缓存
        \Cache::forget($request->verification_key);

        return '成功';
    }
}
