<?php
/**
 * 
 * @author eric
 *
 */

class RoleAR extends Roles
{	
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	function getRoleModules()
	{
		$modules = array();
		try 
		{
			$modules = ModulesAR::model()->with(
					array('roles'=>array('condition'=>"roles.role_id={$this->role_id}"),
					)
			)->findAll();
		}
		catch(CDbException $e)
		{
			CMS::error("get role modules error: role_id:'{$this->role_id}'");
			return array();
		}

		$rnt = array();
		foreach ($modules as $m)
		{
			$rnt[$m->module_id] = $m->module_name;
		}
		return $rnt;
	}
	
	function getAllRoleModules($column=3)
	{
		$modules = ModulesAR::model()->findAll(array('order'=>'parent_module_id asc'));
		//RootTools::dump($modules);exit;
		
		$role_modules = ($this->role_id) ? $this->getRoleModules() : array();
		
		$tmp = $rnt = array();
		$j = 0;
		
		$active = 0;
		foreach ($modules as $i => $module)
		{
			$r = array(
					'module_id'=>$module->module_id,
					'module_name'=>$module->module_name,
					'checked'=>false,
			);
			if (isset($role_modules[$module->module_id]))
				$r['checked'] = true;
			
			$node = array('node'=>$r,'data'=>array(),);
			if (!$module->is_action) 
			{
				if ($module->parent_module_id == 0)
				{
					$node['node']['active'] = 0;
					if(empty($role_modules) && $i == 0) $node['node']['active'] = 1;
					$node['node']['cnum'] = 0;
					if (!isset($rnt[$module->module_id]))
						$rnt[$module->module_id] = $node;
				}
				else
				{
					if ($r['checked'] == true) 
					{
						$rnt[$module->parent_module_id]['node']['cnum']++;
						if (!$active) 
						{
							$rnt[$module->parent_module_id]['node']['active'] = 1;
							$active = 1;
						}
					}
					$tmp[$module->module_id] = $module->parent_module_id;
					$rnt[$module->parent_module_id]['data'][$module->module_id] = $node;
				}
			}
			else 
			{
				$pid = $module->parent_module_id;
				$ppid = $tmp[$pid];
				array_push($rnt[$ppid]['data'][$pid]['data'], $node);
			}
		}
		
		return $rnt;
	}
	
	function addRole($modules = null)
	{
		$this->save();
		$this->saveRoleModules($modules);
	}
	
	function updateRole($modules = null)
	{
		$this->modifyByPk();
		$this->saveRoleModules($modules, 'update');
	}
	
	private function saveRoleModules($modules, $type = 'update')
	{
		if (!is_null($modules) || !empty($modules))
		{
			if ($type == 'update')
				RoleModule::model()->deleteAllByAttributes(array('role_id'=>$this->role_id));
			
			if (is_array($modules))
			{
				$ids = $pids = array();
				foreach ($modules as $m)
				{
					$ids[$m] = 1;
					$module = ModulesAR::model()->findByPk($m);
					$pids[$module->parent_module_id] = $ids[$module->parent_module_id] = 1;
				}
				foreach ($pids as $pid => $v)
				{
					$module = ModulesAR::model()->findByPk($pid);
					$ids[$module->parent_module_id] = 1;
				}
				foreach ($ids as $id=>$v)
				{
					$role_module = new RoleModule();
					$role_module->role_id = $this->role_id;
					$role_module->module_id = $id;
					$role_module->save();
				}
			}
			else
			{
				$role_module = new RoleModule();
				$role_module->role_id = $this->role_id;
				$role_module->module_id = $modules;
				$role_module->save();
			}
		}
	}
}