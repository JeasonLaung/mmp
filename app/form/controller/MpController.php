<?php
namespace app\form\controller;

use api\common\controller\BaseController;
use think\Validate;
use think\Db;

use app\form\model\DiyForm;
class MpController extends BaseController
{
  public function _empty($id='', DiyForm $DiyForm)
  {
    if ($id=='index') {
      return json(['msg'=>'没有formid']);
    }
    $res = $DiyForm->field('id,title,draw,name,figure')->find($id);
    if ($res) {
      $res['draw'] = json_decode($res['draw'], true);
      return json(['code'=>1,'data'=>$res]);
    } else {
      return error('该表单正在维护中');
    }
  }


  public function save($id='',DiyForm $DiyForm)
  {
    $uid = request()->uid;
    if (empty($id)) {
      return json(['msg'=>'没有formid']);
    }
    $res = $DiyForm->find($id);
    $field = $res['field'];
    $table_name = $res['table_name'];
    $validate = $res['validate'];
    $data = $this->request->only($field, 'post');
    $data['user_id'] = $uid;
    $data['update_time'] = $data['create_time'] = time();
    $data['form_id'] = $id;
    $data['step'] = json_encode([['status'=>0,'time'=>$data['create_time']]]);
    // "{'name|名称':'require,min:1'}"
    if ($validate) {
      $validate = json_decode($validate, true);
      $validate = Validate::make($validate);
      $msg = $validate->check($data);
      // halt($msg);
      if ($msg === false) {
        return json(['code'=>0,'msg'=>$validate->getError()]);
      }
    }

    Db::startTrans();
    try{
          $result = Db::table($table_name)->insertGetId($data);
          Db::table('cmf_user_order')->insert(['title'=>$res['title'],'user_id'=>$uid,'object_id'=>$result,'table_name'=>$table_name,'apply_time'=>$data['create_time']]);
          // 提交事务
          Db::commit();
    } catch (\Exception $e) {
          // 回滚事务
          Db::rollback();
          return $e->getMessage();
    }
    return $result ? json(['code'=>1,'data'=>$result, 'msg' => '提交成功']) : json(['code'=>0,'msg'=>@$msg]);
  }
}