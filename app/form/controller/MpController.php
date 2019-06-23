<?php
namespace app\form\controller;

use cmf\controller\HomeBaseController;

class MpController extends HomeBaseController
{
    public function _empty($id)
    {
        $json = '{
            "title": "测试",
            "id": "1",
            "name": "test_form1",
            "inputs": [
                {
                "name": "phone",
                "label": "手机",
                "type": "number",
                "el": "input"
                },
                {
                "name": "username",
                "label": "用户名",
                "type": "text",
                "el": "input",
                "maxlength": 20
                },
                {
                "name": "username",
                "label": "型号选择",
                "type": "text",
                "el": "picker",
                "range": ["iphone", "adoird"]
                },
                {
                "name": "password",
                "label": "密码",
                "type": "password",
                "el": "input"
                },
                {
                
                "name": "photos",
                "label": "上传",
                "el": "file"
                }
            ]
        }';
        $data = json_decode($json,true);
        $this->assign('data', $data);
        return $this->fetch();
    }
}