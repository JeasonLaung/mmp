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
namespace app\check\controller;

use think\Db;
use cmf\controller\AdminBaseController;
use app\check\model\CompanyPostModel;

class AdminCheckController extends AdminBaseController
{
    /**
     * 入驻申请列表
     * @adminMenu(
     *     'name'   => '入驻申请审核',
     *     'parent' => '',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => 'file',
     *     'remark' => '入驻申请列表',
     *     'param'  => ''
     * )
     */

    public $checkedArray = array(0 => '未审核', 1 => '通过', 2 => '驳回');

    public function company_list($keyword = '',$checked = -1,CompanyPostModel $CompanyPostModel){
        $where = [];
        if(!empty($keyword)) $where['company|legal_man|contact|phone'] = ['like','%'.$keyword.'%'];
        if($checked !== -1) $where['checked'] = $checked;
        $list = $CompanyPostModel->where($where)->order('checked asc,create_time desc')->paginate(10);
        $this->assign('list',$list);
        $this->assign('page', $list->render());
        $this->assign('keyword', $keyword);
        $this->assign('checked', (int) $checked);
        $this->assign('checkedArray',$this->checkedArray);
        return $this->fetch();
    }

    

    //使用者申请审核状态修改
    public function checked_change(CompanyPostModel $CompanyPostModel){
        if($this->request->isPost()){
            $data = $this->request->post();
            
            $save = $CompanyPostModel->where('id',$data['id'])->update(array('checked'=>$data['type'],'status'=>1));
            if(!$save) $this->error('审核失败');
            $companyInfo =$CompanyPostModel->find($data['id']);
            if($data['type'] == 1){
                $MsgData['data'] = array(
                    'keyword1' => array('value'=>$companyInfo['company']),
                    'keyword2' => array('value'=>'审核通过'),
                    'keyword3' => array('value'=>'您现在可以前往小程序进行使用了！'),
                );
            }else{
                $MsgData['data'] = array(
                    'keyword1' => array('value'=>$companyInfo['company']),
                    'keyword2' => array('value'=>'审核驳回'),
                    'keyword3' => array('value'=>$data['reject_note'])
                );
            }
            //发送审核通知
            // $access_token = $this->getToken();
            // $url = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token='.$access_token;
            // $MsgData['access_token'] = $access_token;
            // $MsgData['touser'] = $userInfo['wxpopenid'];
            // $MsgData['template_id'] = 'hkP4iiyYpqvyt4E1vmqAzPFqf_vj77gUT9uOwGH6ito';
            // $MsgData['page'] = 'pages/index/index';
            // $MsgData['form_id'] = $userInfo['form_id'];
            // $json = json_encode($MsgData);
            // $res = \Curl::CPost($url,$json,true);
            $save ? $this->success('审核成功') : $this->error('审核失败');
        }else{
            $this->error('访问失败');
        }
    }
    

    //公司禁用/启用
    public function change_status(CompanyPostModel $CompanyPostModel){
        if($this->request->isPost()){
            $data = $this->request->post();
            
            $save = $CompanyPostModel->where('id',$data['id'])->save(['status' => $data['status']],['id' => $data['id']]);
            if(!$save) $this->error('审核失败');
            $save ? $this->success('审核成功') : $this->error('审核失败');
        }else{
            $this->error('访问失败');
        }
    }

}