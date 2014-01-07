<?php

class AdminModule extends CWebModule
{
	public function init()
	{
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
			'admin.models.*',
			'admin.ar.*',
		));
	}

	public function beforeControllerAction($controller, $action)
	{
		if(parent::beforeControllerAction($controller, $action))
		{
			//var_dump(Yii::getPathOfAlias('application.modules.' . $this->getId() . '.views'));
			
			//$controller->smarty->template_dir = Yii::getPathOfAlias('application.modules.' . $this->getId() . '.views');
			//var_dump();exit;
			// this method is called before any module controller action is performed
			// you may place customized code here
			return true;
		}
		else
			return false;
	}
}
