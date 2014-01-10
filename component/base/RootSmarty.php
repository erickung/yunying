<?php
require_once(Yii::getPathOfAlias('root.ext.smarty').DS.'smarty/Smarty.class.php');
define('SMARTY_TMPL_DIR', Yii::getPathOfAlias('application.views'));

class RootSmarty extends Smarty
{
	private static $javascrpts = '';
	private static $jsfiles = array();
	private static $cssfiles = array();
	private $controller;
	
	public function __construct(RootController $controller)
	{
		parent::__construct();
		$this->config();
		$this->addPluginsDir(Yii::getPathOfAlias('application.components.tags'));
		$this->controller = $controller;
		$this->init();
	}
	
	public function init()
	{
		Yii::registerAutoloader('smartyAutoload');
		$this->setTemplateDir($this->controller->getViewPath());
		$this->initView();
	}
	
	public function assignAssets()
	{
		$this->assign('scripts', "<script>" . self::$javascrpts . "</script>");
		$this->assign('css_arr', self::$cssfiles);
	}
	
	public static function addJavascript($scripts)
	{
		self::$javascrpts .= "\n$scripts";
	}
	
	public static function addJsFile($jsfile)
	{
		if (is_array($jsfile))
			array_merge(self::$jsfiles, $jsfile);
		else
			array_push(self::$jsfiles, $jsfile);
	}
	
	public static function getJavascript()
	{
		return self::$javascrpts;
	}
	
	public static function addCssFile($css)
	{
		if (is_array($css))
			array_merge(self::$cssfiles, $css);
		else
			array_push(self::$cssfiles, $css);
	}
	
	public function getMainContents($view, $data = NULL)
	{
		if($data && !empty($data))
			$this->var_init($data);
	
		return $this->fetch($this->getView($view));
	}
	
	public function getView($view)
	{
		return strpos($view, "/") ? $view . '.htm' : $this->controller->getId() . '/' . $view . '.htm';
	}
	
	public function getMainViewPath()
	{
		return SMARTY_TMPL_DIR;
	}
	
	private function initView()
	{
		$this->assign('controller', $this);
		$this->assign('layouts', $this->controller->getViewPath() . DS . 'layouts');
		$views_inits = Root::loadConf('view_init');
		$this->var_init($views_inits);
	}
	
	private function config()
	{
		$this->template_dir = SMARTY_TMPL_DIR;
		$this->compile_dir = COMPILE_PATH;
		$this->cache_dir = CACHE_PATH;
		$this->left_delimiter  =  '<%';
		$this->right_delimiter =  '%>';
	}
	
	public function var_init($inits)
	{
		if(empty($inits)) return false;
		
		foreach ($inits as $k => $v)
			$this->assign($k, $v);
	}
} 