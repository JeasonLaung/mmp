<?php
namespace app\form\controller;
use cmf\controller\AdminBaseController;
use app\form\model\DiyForm;
use think\Db;

class AdminMpController extends AdminBaseController{
  protected $protected_field = ['id','create_time','update_time','form_id','status','user_id','_msg','step'];

  public function index($keyword='',DiyForm $DiyForm) {
    if ($keyword) {
      $forms = $DiyForm->where('title','like','%'.$keyword."%")->select();
    } else {
      $forms = $DiyForm->all();
    }
    $this->assign('keyword', $keyword); 
    $this->assign('forms', $forms); 
    return $this->fetch();
  }

  public function off($id,DiyForm $DiyForm)
  {
    $res = $DiyForm->where('id', $id)->data(['status' => 0,'update_time'=>time()])->update();
    return $res ? success('', '关闭成功') : error('关闭失败');
  }
  public function copy($id,DiyForm $DiyForm)
  {
    $res = $DiyForm->where('id', $id)->find()->toArray();
    $res['title'] = $res['title'].'_副本';
    $res['create_time'] = time();
    $res['update_time'] = '';
    unset($res['id']);
    $res = $DiyForm->insert($res);
    return $res ? success('', '复制成功') : error('复制失败');
  }

  public function on($id,DiyForm $DiyForm)
  {
    $res = $DiyForm->where('id', $id)->data(['status' => 1,'update_time'=>time()])->update();
    return $res ? success('', '开启成功') : error('开启失败');
  }
  public function delete($id,DiyForm $DiyForm)
  {
    $res = $DiyForm->where('id', $id)->delete();
    return $res ? success('', '删除成功') : error('删除失败');
  }
  public function deleteTable($id, $jeason=false,DiyForm $DiyForm)
  {
    $form = $DiyForm->where(['id' => $id])->find();
    $table_name = $form['table_name'];
    if (!$jeason) {
      $res = $DiyForm->where(['table_name' => $table_name])->select();
      if (!empty($res) && count($res) > 1) {
        return error('你的删除操作危险！该表绑定了很多表单，请联系开发人员操作');
      }
      $res = Db::table($table_name)->select();
      if (!empty($table)) {
        return error('你的删除操作危险！该表已存在多个数据，请联系开发人员操作');
      }
    }
    $res = Db::query('drop table '.$table_name);
    return $res ? success('', '删除成功') : error('删除失败');
  }
  public function add(DiyForm $DiyForm)    
  {      
    return $this->fetch();		
  }

  public function addPost(
    $table_data = [], 
    $draw_data = [],
    $field_data = '',
    $validate_data = '',
    DiyForm $DiyForm)	{
    // halt([$table_data, $draw_data, $validate_data]);
    // $title = @$form_info['title'];	
    // $table_name = @$form_info['table_name'];
    // $note = @$form_info['note'];
    // $DiyForm->insert($table_data);
    // $field = '';
    if (!$table_data) {
      return error('没有table信息');
    }
    if (!$draw_data) {
      return error('没渲染信息');
    }
    $title = @$table_data['title']; 
    $table_name = @$table_data['table_name'];
    $note = @$table_data['note'];
    $table_data['draw'] = json_encode($draw_data);
    $table_data['validate'] = json_encode($validate_data);
    $table_data['field'] = $field_data;
    $table_data['create_time'] = time();
    $DiyForm->insert($table_data);


    $sql = "show tables like '". $table_name ."';";
    $has_table = Db::query($sql);


    if (!$has_table) {
      $fields_sql = '';
            // 创建表
      $sql = "CREATE TABLE IF NOT EXISTS ".$table_name."(
      `id` INT UNSIGNED AUTO_INCREMENT COMMENT '唯一id',
      `create_time` INT COMMENT '创建时间',
      `update_time` INT COMMENT '更新时间',
      `form_id` INT NOT NULL COMMENT '来自diy表单的那个id',
      `status` TINYINT DEFAULT 0 COMMENT '状态',
      `user_id` INT NOT NULL COMMENT '用户id',
      `_msg` VARCHAR(500) COMMENT '备注信息',
      `step` INT DEFAULT 0 COMMENT '进度',
      %s PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8";
      for ($i=0; $i < count($draw_data); $i++) {
        $el = @$draw_data[$i]['el'];
        if ($el === 'note') {
          continue;
        }
        $title = @$draw_data[$i]['title'];
        $name = @$draw_data[$i]['name'];
        $type = @$draw_data[$i]['type'];
        $form_type = @$draw_data[$i]['form_type'] ? $draw_data[$i]['form_type'] : 'VARCHAR';
        $maxlength = @$draw_data[$i]['maxlength'] ? $draw_data[$i]['maxlength'] : 255;
        $range = @$draw_data[$i]['range'];
        $value = @$draw_data[$i]['value'];
        if ($draw_data[$i]['el'] == 'file') {
          $maxlength = 1500;
        } else if ($draw_data[$i]['el'] == 'note') {
          $form_type = @$draw_data[$i]['form_type'] ? $draw_data[$i]['form_type'] : 'TEXT';
          $maxlength = 10000;
        }
        
        if ($el && $name) {
          $fields_sql .= sprintf('`%s` %s(%s) %s COMMENT "%s",', $name,$form_type,$maxlength, $value ? ('DEFAULT "' . $value . '"') : '',$title);
        }
      }
      $sql = sprintf($sql, $fields_sql);
      $res = Db::query($sql);
      return success('', '添加成功');
    }
    else {
      // 字段我的
      $x_fields = Db::table($table_name)->getTableFields();
      // 是否有新的字段
      for ($i=0; $i < count($draw_data); $i++) {
        $title = @$draw_data[$i]['title'];
        $name = @$draw_data[$i]['name'];
        $type = @$draw_data[$i]['type'];
        $form_type = @$draw_data[$i]['form_type'] ? $draw_data[$i]['form_type'] : 'VARCHAR';
        $maxlength = @$draw_data[$i]['maxlength'] ? $draw_data[$i]['maxlength'] : 255;
        $range = @$draw_data[$i]['range'];
        $value = @$draw_data[$i]['value']; 
        // 新字段就添加
        if (!in_array($fields[$i], $x_fields)) {
          // 新增字段
          $sql = "ALTER TABLE ".$table_name." ADD `".$name."`  ".$form_type."(".$maxlength.") ".($value ? ('DEFAULT "' . $value . '"') : '')." COMMENT '".$title."'";
          try {
            Db::query($sql);
          }
          catch (\PDOException $e) {
            return error('新增字段' . $title.$name . "时发生错误,已存在");
          }
        }
      }
      return success('', '新建成功');
    }
  }

  public function edit($id = '',DiyForm $DiyForm) {
    $form = $DiyForm->find($id);
    if (!$form) {
      return $this->error('不存在这个id的自定义表单');
    }
    $this->assign('form', $form);
    $this->assign('id', $id);
    return $this->fetch();
  }

  public function editPost(
    $id,
    $table_data = [], 
    $draw_data = [],
    $delete_data = [],
    $field_data = [],
    $validate_data = [],
    DiyForm $DiyForm) {
    if (!$table_data) {
      return error('没有table信息');
    }
    if (!$draw_data) {
      return error('没渲染信息');
    }


    // 原始数据
    $form = $DiyForm->find($id);
    // 字段我的
    $x_fields = Db::table($form['table_name'])->getTableFields();
    // 删除字段
    $sql = "alter table ".$form['table_name']."";
    $tmp = '';
    for ($i=0; $i < count($delete_data); $i++) {
      if (!in_array($delete_data[$i], $x_fields)) {
        continue;
      }
      $tmp .= ' drop column '.$delete_data[$i].',';
      // 最后一个
      if ($i == count($delete_data) - 1) {
        $tmp = substr($tmp, 0, -1);
      }
    }
    try {
      $res = Db::query($sql . $tmp);
    }
    catch (\Expection $e) {
      return error('修改字段' . $title.$name . "时发生错误");
    }

    // 修改新增字段
    for ($i=0; $i < count($draw_data); $i++) {
      $el = @$draw_data[$i]['el'];
      if ($el === 'note' || $el === 'download') {
        continue;
      }
      $title = @$draw_data[$i]['title'];
      $name = @$draw_data[$i]['name'];
      $type = @$draw_data[$i]['type'];
      $form_type = @$draw_data[$i]['form_type'] ? $draw_data[$i]['form_type'] : 'VARCHAR';
      $maxlength = @$draw_data[$i]['maxlength'] ? $draw_data[$i]['maxlength'] : 255;
      $range = @$draw_data[$i]['range'];
      $value = @$draw_data[$i]['value'];

      $x_name = @$draw_data[$i]['x_name'];
      // 修改
      if ((@$x_name && @$x_name != $name) || (@$x_name && in_array($name, $x_fields))) {
        $sql = "ALTER TABLE ".$table_data['table_name']." CHANGE `".$x_name."` `".$name."` ".$form_type."(".$maxlength.") ".($value ? ('DEFAULT "' . $value . '"') : '')." COMMENT '".$title."'";
        try {
          $res = Db::query($sql);
        }
        catch (\PDOException $e) {
          return error('修改字段' . $title.$name . "时发生错误");
        }
      }
      else {
        if (in_array($name, $x_fields)) {
          continue;
        }
        // 新增字段
        $sql = "ALTER TABLE ".$table_data['table_name']." ADD `".$name."`  ".$form_type."(".$maxlength.") ".($value ? ('DEFAULT "' . $value . '"') : '')." COMMENT '".$title."'";
        try {
          Db::query($sql);
        }
        catch (\PDOException $e) {
          return error('新增字段' . $title.$name . "时发生错误,已存在");
        }
      }
    }

    // 重载数据
    $title = @$table_data['title']; 
    $table_name = @$table_data['table_name'];
    $note = @$table_data['note'];
    $table_data['draw'] = json_encode($draw_data);
    $table_data['validate'] = json_encode($validate_data);
    $table_data['field'] = $field_data;
    $table_data['update_time'] = time();
    try {
      $DiyForm->where('id', $id)->data($table_data)->update();
    }
    catch (\Expection $e) {
      return error('在保存form的时候出错了');
    }
    return success('','编辑成功');
  }
}