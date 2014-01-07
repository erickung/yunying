<?php
class UsergroupController extends BMSController
{
	public function actionIndex()
	{
		$this->assign('columns', json_encode( array_values(self::userFields()) ));
		$this->assign('fields', json_encode( array_keys(self::userFields()) ));
		$this->assign('extjs', 1);
		$this->render('index');
	}
	
	private static function userFields()
	{
		return array(
				'user_id' =>	array('dataIndex' => 'user_id', 'sortable' =>true, 'width'=>60, text=>'ID')		,
				'user_email' =>	array('dataIndex' => 'user_email', 'sortable' =>true, 'flex'=>1, text=>'邮箱')		,
				'user_name' =>	array('dataIndex' => 'user_name', 'sortable' =>false, 'flex'=>1, text=>'中文名')		,
				'last_login_time' =>	array('dataIndex' => 'last_login_time', 'sortable' =>true, 'flex'=>1, text=>'最后登录时间')		,
		);
	}
}