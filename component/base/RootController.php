<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class RootController extends CController implements RootInterface
{
	public $smarty;
	public $module_id;
	private $view_path;
	
	private static $nologin = array(
			'site' => array('login'),
	);
	public static $defaultModules = array(
			'custom.pass' => array('info'),
	);

	public function __construct($id,$module=null)
	{
		parent::__construct($id, $module);
	}
	
	public function assignModules($modules_detail, $user_modules, $module)
	{	
	//	RootTools::dump($user_modules, $module);exit;
		if ($this->smarty)
		{
			$this->assign('modules_detail', $modules_detail);
			$this->assign('user_modules', $user_modules);
			$this->assign('selected_module', $module);
		}
	}
	
	protected function beforeAction($action)
	{		
		$this->processRequest();
		if ($this->noLoginActions($action)) return true;

		$this->checkLogin();
		$this->checkPower();
		
		return true;
	}
	
	protected function checkLogin()
	{
		if (!WebUser::Instance()->auth())
			$this->redirect('/site/login', true, 403);
	}
	
	protected function checkPower()
	{
		if (!WebUser::Instance()->checkPower($this)) return true;
			//$this->redirect("/", true, 404);
	}
	
	protected function processRequest()
	{
		if (Yii::app()->request->isPostRequest)
		{
			Request::processPost();
			$this->smarty = new RootSmarty($this);
		}
		else 
		{
			if (!Yii::app()->request->isAjaxRequest)
				$this->smarty = new RootSmarty($this);

			Request::processGet();
		}	
	}
	
	private function noLoginActions($action)
	{
		$controller = $this->getId();
		$action = strtolower($this->getAction()->getId());
		
		return isset(self::$nologin[$controller]) && in_array($action, self::$nologin[$controller]);
	}

	public function redirect($url,$terminate=true,$statusCode=302)
	{
		if (Yii::app()->request->isAjaxRequest)
		{
			echo json_encode(array('success' => false,'error' => $statusCode));
			if($terminate)	Yii::app()->end();
		}
		else
		{
			parent::redirect($url,$terminate,$statusCode);
		}
	}
	
	public function render($view, $data = NULL, $return = false)
	{
		$html = $this->smarty->getMainContents($view, $data);

		$this->assign('content', $html);
		$this->smarty->assignAssets();
	
		$this->smarty->display('layouts/main.htm');
	}
	
	public function renderPartial($view,$data=null,$return=false,$processOutput=false)
	{
		$this->smarty->display($this->smarty->getView($view));
	}
	
	public function renderJS($view, $cache_id=null)
	{
		$this->fetchJS($view, true);
	}
	
	public function fetchJS($view, $display=false, $cache_id=null, $compile_id=null, $parent=null)
	{
		$view = $view . '.js';
		return	$this->smarty->fetch($view, $cache_id, $compile_id, $parent, $display);
	}
	
	protected function assign($tpl_var, $value = null, $nocache = false)
	{
		return $this->smarty->assign($tpl_var, $value, $nocache);
	}
	
	protected function fetch($view, $data = NULL, $cache_id = NULL)
	{
		return $this->smarty->getMainContents($view, $data);
	}
	
	public function getViewPath()
	{
		return ($this->view_path) ? $this->view_path : parent::getViewPath() . DS . '..';
	}
	
	public function setViewPath($path)
	{
		$this->view_path = $path;
		$this->smarty->setTemplateDir($path);
	}
}