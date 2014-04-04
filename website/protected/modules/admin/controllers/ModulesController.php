<?php
class ModulesController extends CMSController {
	public function __construct($id, $module = null) 
	{
		/*
		$_GET = $this->saddslashes($_GET);
		$_POST = $this->saddslashes($_POST);
		$_COOKIE = $this->saddslashes($_COOKIE);
		parent :: __construct($id, $module);
		*/
	} 

	public function actionIndex() {

	} 

	public function actionList() {
		$command = $this->dbCommand("select * from cms_modules order by parent_module_id asc, module_id asc");
		$category = $command -> queryAll();
		
		if(isset($_GET['format']) && $_GET['format'] == 'list')
		{
			echo json_encode($category);
			return;
		}
		$tree = TreeServ::Instance()
					->load(array('data'=>$category, 'node'=>'module_id', 'pnode'=>'parent_module_id'))
					->genTree(); 
		echo json_encode(array('children' => $tree));
	} 

	public function actionModulesAdd(){
		$module_name = trim($_POST['module_name']);
		$num = $this->dbCommand("select count(*) as num from cms_modules where parent_module_id = 0 and module_name = '{$module_name}'")->queryScalar();
		if(intval($num) === 0){
			$this->dbCommand("insert into cms_modules (module_name, parent_module_id, is_action, controller, action, modify_username) values ('{$module_name}', 0, 0, '', '', '')")->execute();
			Response::success();
		}else{
			Response::failure ("该模块名称已存在，请换一个");
		}
	}

	public function actionControlerAdd(){
		$param = $this->createForm();
		$param['modify_username'] = '';
		$sql = $this -> createSql('cms_modules', $param, 'insert');
		$this->dbCommand($sql)->execute();
		$this->success();
	}

	public function actionControlerModify(){
		$id = intval($_GET['module_id']);
		$param = $this->createForm();
		unset($param['parent_module_id']);
		$sql = $this -> createSql('cms_modules', $param, 'update') . " where module_id = {$id}";
		$this->dbCommand($sql)->execute();
		$this->success();
	}

	private function createForm(){
		$param = $_POST['param'];
		if(empty($param['module_name'])) $this->error('提供的数据不完整！');
		$param['is_action'] = intval($param['is_action']);
		if($param['is_action']){
			if(empty($param['controller']) || empty($param['action'])) $this->error('提供的数据不完整！');
		}else{
			$param['controller'] = '';
			$param['action'] = '';
		}
		return $param;
	}

	public function actionUpdateData(){
		$post = @file_get_contents("php://input");
		$post = get_magic_quotes_gpc() ? stripslashes($post) : $post;
		$root = @json_decode($post, true);
		if(is_array($root)){
			$id = intval($root['data']['module_id']);
			$row = $this->dbCommand("select module_id from cms_modules where module_id = '{$id}' ")->queryRow();
			if(empty($row)) $this->error('要操作的数据已经不存在了！');

			if(isset($root['data']['module_name'])){
				$field = 'module_name';
				$value = trim($root['data']['module_name']);
			}else{
				$this->error('bad request');
			}
			$this->dbCommand("update cms_modules set {$field} = '{$value}' where module_id = {$id} ")->execute();
			$this->success();
		}else{
			$this->error('bad request');
		}
	}
	
	public function actionGetControllerData(){
		$id = intval($_GET['module_id']);
		$row = $this->dbCommand("select * from cms_modules where module_id = '{$id}' ")->queryRow();
		if(empty($row)) $this->error('要操作的数据已经不存在了！');
		$array = array();
		foreach($row as $key=>$val){
			$array['param[' . $key . ']'] = $val;
		}
		$this->success($array);
	}

	public function actionControllerRemove(){
		$id = intval($_POST['module_id']);
		$num = $this->dbCommand("select count(*) as num from cms_modules where parent_module_id = {$id} ")->queryScalar();
		if(intval($num) > 0) $this->error("出错了，请先删除其子级");

		$this->dbCommand("delete from cms_modules where module_id = {$id} ")->execute();
		$this->success();
	}

	private function success($data = array()){
		echo json_encode(array('success'=> true, 'data' => $data));
		exit();
	}

	private function error($msg){
		echo json_encode(array('success' => false, 'msg' => $msg));
		exit();
	}

	private function sstripslashes($string) {
		if (is_array($string)) {
			foreach($string as $key => $val) {
				$string[$key] = $this->sstripslashes($val);
			} 
			return $string;
		} else {
			return stripslashes($string);
		} 
	} 

	private function createSql($table, &$preArray , $type = 'insert') {
		if ($type == 'insert' || $type == 'replace') {
			$sql = array();
			foreach($preArray as $key => $val) {
				$sql[0][] = $key;
				$sql[1][] = $val;
			} 
			return "{$type} into {$table} (`" . implode("`,`", $sql[0]) . "`) values ('" . implode("','", $sql[1]) . "')";
		} else {
			$sql = '';
			foreach($preArray as $key => $val) {
				$sql .= "`$key`='$val',";
			} 
			$sql = substr($sql, 0, -1);
			return "update {$table} SET $sql ";
		} 
	} 
} 

class categoryTree {
	private $originalList;
	private $pk;
	private $parentKey;
	private $childrenKey;

	function __construct($pk = "cid", $parentKey = "pid", $childrenKey = "children") {
		if (!empty($pk) && !empty($parentKey) && !empty($childrenKey)) {
			$this -> pk = $pk;
			$this -> parentKey = $parentKey;
			$this -> childrenKey = $childrenKey;
		} else {
			return false;
		} 
	} 
	// 载入初始数组
	public function load($data) {
		if (is_array($data)) {
			$this -> originalList = $data;
		} 
	} 

	public function deepTree($root = 0) {
		if (!$this -> originalList) {
			return false;
		} 
		$originalList = $this -> originalList;
		$tree = array();
		$refer = array(); 

		foreach($originalList as $k => $v) {
			if (!isset($v[$this -> pk]) || !isset($v[$this -> parentKey]) || isset($v[$this -> childrenKey])) {
				unset($originalList[$k]);
				continue;
			} 
			$v[$this -> pk] = intval($v[$this -> pk]);
			$v[$this -> parentKey] = intval($v[$this -> parentKey]);
			$refer[$v[$this -> pk]] = &$originalList[$k];
		} 

		foreach($originalList as $k => $v) {
			if ($v[$this -> parentKey] == $root) { 
				$tree[] = &$originalList[$k];
			} else {
				if (isset($refer[$v[$this -> parentKey]])) {
					$parent = &$refer[$v[$this -> parentKey]];
					$parent[$this -> childrenKey][] = &$originalList[$k];
				}
			} 
		} 
		return $tree;
	} 

	public function formatTree(&$cate){
		foreach($cate as &$val){
			if(empty($val[$this -> childrenKey])){
				$val['leaf'] = true;
			}else{
				$this->formatTree($val[$this -> childrenKey]);
			}	
		}
	}
} 
