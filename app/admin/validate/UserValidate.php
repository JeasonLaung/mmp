<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2019 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 小夏 < 449134904@qq.com>
// +----------------------------------------------------------------------
namespace app\admin\validate;

use think\Validate;

class UserValidate extends Validate
{
    protected $rule = [
        'user_nickname' => 'require',
        'user_login' => 'require|unique:user,user_login',
        'user_pass'  => 'require',
        'mobile' => 'require|checkPhone:thinkphp',
    ];
    protected $message = [
        'user_nickname.require' => '用户昵称不能为空',
        'user_login.require' => '账号不能为空',
        'user_login.unique'  => '账号已存在',
        'user_pass.require'  => '密码不能为空',
        'mobile.require' => '手机不能为空',
        'mobile.checkPhone'   => '手机号码格式错误'
    ];

    protected $scene = [
        'add'  => ['user_nickname','user_login', 'user_pass', 'mobile'],
        'edit' => ['user_login', 'mobile'],
    ];
    
    // 自定义验证规则
    protected function checkPhone($value,$rule,$data)
    {
        if(!preg_match('/^1\d{10}$/',$value)){
            return '手机号码格式错误';
        }else{
            return true;
        }
    }
}