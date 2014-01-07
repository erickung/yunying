<?php

class SiteController extends Controller
{
	public function actionPing()
	{
		$source_ip = $target_ip = $source = $range = $type = $start = $end = '';
		$type = (!isset($_GET['type'])) ? 'hour' : $_GET['type'];
		$range = (!isset($_GET['range'])) ? 1 : $_GET['range'];
		if ($range == 1)  
		{
			$start = date('Y-m-d H:i:s', strtotime(date('Ymd', time())));
			$end = date('Y-m-d H:i:s', strtotime(date('Ymd', time()+86400)));
		}
		elseif ($range == 2)
		{
			$dates = CTimestamp::getDate();
			$next_mon = $next_year = 0;
			if ($dates['mon'] == 12)
			{
				$next_mon = 1;
				$next_year = $dates['year']+1;
			}
			else 
			{
				$next_mon = $dates['mon']+1;
				$next_year = $dates['year'];
			}

			$start = date('Y-m-d', CTimestamp::getTimestamp(0, 0, 0, $dates['mon'], '1', $dates['year']));
			$end = date('Y-m-d', CTimestamp::getTimestamp(0, 0, 0, $next_mon, '1', $next_year));
		}
		 
		if (isset($_GET['source'])) $source = $_GET['source'];
		if (strpos($source, '->')) 
			list($source_ip , $target_ip) = explode('->', $source);
		
		$filter = array();
		if ($source_ip) $filter['source_ip'] = $source_ip;
		if ($target_ip) $filter['target_ip'] = $target_ip;
		if ($start) $filter['start'] = $start;
		if ($end) $filter['end'] = $end;
		$PingDataAR = new PingDataAR();
		$criteria = ActiveRecordServ::Instance($PingDataAR)->getConditionByParams(
				$filter, array(), array('start'=>array('modify_time', '>='),'end'=>array('modify_time', '<')));
		$criteria->limit = -1;

		$loss = $resp = array();
		$data = $PingDataAR->findAll($criteria);
		$xticks = array();
		foreach ($data as $ping)
		{
			$line = "{$ping->source_ip}->{$ping->target_ip}";
			if (!isset($loss[$line]))  $loss[$line] = array();
			//var_dump($PingDataAR->getTimeReg($range, $type));exit;
			$time = $PingDataAR->getTime($PingDataAR->getTimeReg($range, $type), $range, $ping->modify_time);
			
			if (!isset($loss[$line][$time])) $loss[$line][$time] = array();
			$loss[$line][$time]['all_loss'] += $ping->loss;
			$loss[$line][$time]['all_transmitted'] += $ping->transmitted;
			$resp[$line][$time]['resp_time'] += $ping->resp_time;
			$resp[$line][$time]['num'] += 1;
			$xticks[$time] = 1;
		}
		
		$xticks = array_keys($xticks);
		$loss = $PingDataAR->getLoss($loss);
		$resp = $PingDataAR->getResp($resp);
		//var_dump($loss[0],$xticks);exit;
		$this->smarty->assign('loss', json_encode($loss));
		$this->smarty->assign('resp', json_encode($resp));
		$this->smarty->assign('xtickts', json_encode($xticks));
		
		$url = "/site/ping?type=$type&range=$range&source=$source";	 
		$this->smarty->assign('url', $url);
		$this->smarty->assign('params', array(
				'range'=>$range,'type'=>$type));
		$this->renderPartial('ping');
	}
	
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
		
		//var_dump(111111111111111111);
		$this->render('index'); 
	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
		if($error=Yii::app()->errorHandler->error)
		{
			if(Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->render('error', $error);
		}
	}

	/**
	 * Displays the contact page
	 */
	public function actionContact()
	{
		$model=new ContactForm;
		if(isset($_POST['ContactForm']))
		{
			$model->attributes=$_POST['ContactForm'];
			if($model->validate())
			{
				$name='=?UTF-8?B?'.base64_encode($model->name).'?=';
				$subject='=?UTF-8?B?'.base64_encode($model->subject).'?=';
				$headers="From: $name <{$model->email}>\r\n".
					"Reply-To: {$model->email}\r\n".
					"MIME-Version: 1.0\r\n".
					"Content-Type: text/plain; charset=UTF-8";

				mail(Yii::app()->params['adminEmail'],$subject,$model->body,$headers);
				Yii::app()->user->setFlash('contact','Thank you for contacting us. We will respond to you as soon as possible.');
				$this->refresh();
			}
		}
		$this->render('contact',array('model'=>$model));
	}

	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		/*
		$model=new LoginForm;

		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['LoginForm']))
		{
			$model->attributes=$_POST['LoginForm'];
			// validate user input and redirect to the previous page if valid
			if($model->validate() && $model->login())
				$this->redirect(Yii::app()->user->returnUrl);
		}
		*/
		// display the login form
		//$this->render('login',array('model'=>$model));
		$this->renderPartial('login');
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}
}