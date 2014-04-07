<?php
class PassController extends FController
{
	public function actionInfo()
	{
		$this->render('info');
	}
	
	public function actionEdit()
	{
		if (WebUser::Instance()->user->hashPwd(Request::$post['origin_pass'])
			!= 	WebUser::Instance()->user->password)
			Response::postFailure('原密码不正确！');
		if (Request::$post['pass'] != Request::$post['re_pass'])
			Response::postFailure('两次输入密码不一致！');

		WebUser::Instance()->user->password 
			= WebUser::Instance()->user->hashPwd(Request::$post['re_pass']);
		
		$flag = WebUser::Instance()->user->saveCommit('modifyByPk');
		
		Response::resp($flag, '', '/');
	}
}