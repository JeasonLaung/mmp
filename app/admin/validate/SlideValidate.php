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

class SlideValidate extends Validate
{
    protected $rule = [
        'name' => 'require',
        'code' => 'require|unique:slide,code',
    ];

    protected $message = [
        'name.require' => '分类名称必须',
        'code.require'  => '代码不能为空',
        'code.unique'  => '账号已存在'
    ];

    protected $scene = [
        'add'  => ['name,code'],
        'edit' => ['name'],
    ];
}