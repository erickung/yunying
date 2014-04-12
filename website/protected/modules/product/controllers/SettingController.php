<?php

class SettingController extends FController
{
	public function actionProcessList()
	{
		$process = ProcessAR::model()->findAll();
		$count = ProcessAR::model()->count();
		
		$this->render('setting', array(
				'data'=>$process,
				'count'=>$count,
		));
	}
	
	public function actionProcessInfo()
	{
		if (isset(Request::$get['id']) && Request::$get['id']>0)
		{
			$process = ProcessAR::model()->findByPk(Request::$get['id']);
			$this->assign('process', $process);
		}
		$this->render('process_info');
	}
}