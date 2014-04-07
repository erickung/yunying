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
		$modules = ModulesAR::model()->findAll();
		$role_modules = ($this->role_id) ? $this->getRoleModules() : array();
		
		$rnt = array();
		$j = 0;
		foreach ($modules as $i => $m)
		{
			if ($i%$column == 0) $j++;
			if (!isset($rnt[$j])) $rnt[$j] = array();
			
			$r = array(
				'module_id'=>$m->module_id, 
				'module_name'=>$m->module_name,
				'checked'=>false,
			);
			if (isset($role_modules[$m->module_id]))
				$r['checked'] = true;

			array_push($rnt[$j], $r);

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
				foreach ($modules as $m)
				{
					$role_module = new RoleModule();
					$role_module->role_id = $this->role_id;
					$role_module->module_id = $m;
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