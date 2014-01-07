<?php

class UserController extends BMSController
{
	public function actionIndex()
	{
		$this->assign('columns', json_encode( array_values(self::userFields()) ));
		$this->assign('fields', json_encode( array_keys(self::userFields()) ));
		$this->assign('extjs', 1);
		$this->render('index');
	}
	
	public function actionEdit()
	{
		$action = $_POST['action'];
		if ($action == 'edit')
		{
			$id = $_POST['user_id'];
			$user = UserAR::model()->findByPk($id);
			$user->user_id = $id;
			$user->user_email = $_POST['user_email'];
			$user->user_name = $_POST['user_name'];

			$user->save();
		}
		else
		{
			$user = new UserAR();
			$user->user_email = $_POST['user_email'];
			$user->user_name = $_POST['user_name'];

			$user->save();
		}

		header('Location: http://cms.funshion.com/admin/user/');
		exit;
	}
	
	public function actionUserlist()
	{
		$order = $_GET['sort'] ? "{$_GET['sort']} {$_GET['dir']}" : " user_id DESC";
		$users = UserAR::model()->findAll(array(
				'order'=>$order,
				'limit'=>$_GET['limit'],
		));

		$rnt = BMS::convetARToArray($users, array_keys(self::userFields()));
		
		echo json_encode($rnt);
	}
	
	public function actionUserinfo()
	{
		$this->addCss('lib/chosen/chosen.css');
		
		$action = $_GET['action'];
		
		if ($action == 'edit')
		{
			$id = $_GET['id'];
			$user = UserAR::model()->findByPk($id);
			
			$this->assign('user', $user);
		}
		
		$this->assign('action', $_GET['action']);
		$this->render('user_info');
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