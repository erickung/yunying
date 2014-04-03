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
			'site' => array('login', 'index'),
	);

	public function __construct($id,$module=null)
	{
		parent::__construct($id, $module);
		$this->init();
	}
	
	protected function beforeAction($action)
	{
		//if ($this->noLoginActions($action)) return true;

		//$this->checkLogin();
		//$this->checkPower();
		//$this->processRequest();
		
		return true;
	}
	
	protected function checkLogin()
	{
		if (!WebUser::Instance()->auth())
			$this->redirect('/', true, 'login');
	}
	
	protected function checkPower()
	{
		if (!WebUser::Instance()->checkPower($this))
			$this->redirect("/", true, 'purview');
	}
	
	protected function processRequest()
	{
		//Request::processGet();
		//Request::processPost();
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
			parent::redirect($url,$terminate);
		}
	}
	
	public function render($view, $data = NULL, $return = false)
	{
		$html = $this->smarty->getMainContents($view, $data);

		$this->assign('content', $html);
		$this->smarty->setTemplateDir(SMARTY_TMPL_DIR);
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

	public function init()
	{
		$this->smarty = new RootSmarty($this);
	}
}