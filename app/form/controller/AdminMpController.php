<?php
namespace app\form\controller;

use cmf\controller\AdminBaseController;
use app\form\model\CustomForm;
use app\form\model\CustomFormItem;
use think\Db;

class AdminMpController extends AdminBaseController
{
    public function index(CustomForm $CustomForm)
    {
    	$forms = $CustomForm->all();
    	// dump($forms);
    	$this->assign('forms', $forms);
        return $this->fetch();
    }

    public function add(CustomForm $CustomForm)
    {
      return $this->fetch();
		}
		
		public function addPost($form_info, $form_content,CustomForm $CustomForm, CustomFormItem $CustomFormItem)
		{
			// halt($form_info);
			$title = @$form_info['title'];
			$table_name = @$form_info['table_name'];
			$note = @$form_info['note'];

			$CustomForm->insert($form_info);

			$field = '';
			$sql = 'CREATE TABLE IF NOT EXISTS '.$table_name.'(`id` INT UNSIGNED AUTO_INCREMENT,%s PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8';
			for ($i=0; $i < count($form_content); $i++) { 
				$CustomFormItem->insert($form_content[$i]);

				$element = @$form_content[$i]['element'];
				$name = @$form_content[$i]['name'];
				$type = @$form_content[$i]['type'];
				$maxlength = @$form_content[$i]['maxlength'];
				$range = @$form_content[$i]['range'];
				$value = @$form_content[$i]['value'];
				$maxlength = @$form_content[$i]['maxlength'];

				$x_type = '';
				if ($element && $name) {
					if ($element == 'input' || $element == 'textarea') {
						switch ($type) {
							case 'number':
								$x_type = 'INT';
								$maxlength = $maxlength ? $maxlength : 11;
								break;
							default:
								$x_type = 'VARCHAR';
								$maxlength = $maxlength ? $maxlength : 255;
								break;
						}
					}
					else if ($element == 'picker') {
						$x_type = 'VARCHAR';
						$maxlength = $maxlength ? $maxlength : 255;
					}
					else {
						$x_type = 'VARCHAR';
						$maxlength = $maxlength ? $maxlength : 255;
					} 
					
					$field .= sprintf('`%s` %s(%s) NOT NULL,', $name,$x_type,($maxlength ? $maxlength : 255));
				}
			}
			
			$sql = sprintf($sql, $field);
			$res = Db::query($sql);
			halt($res);
		}
}