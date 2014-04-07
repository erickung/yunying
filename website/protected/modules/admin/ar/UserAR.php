<?php
/**
 * 
 * @author eric
 *
 */

class UserAR extends User
{
	public $roles;
	
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	protected function addUser($role = null)
	{
		$this->password = $this->hashPwd($this->password);
		$this->save();
		$this->saveUserRoles($role);
	}
	
	protected function updateUser($role = null)
	{
		$this->modifyByPk();
		$this->saveUserRoles($role);
	}
	
	function getAllUserRoles($column=3)
	{
		$roles = RoleAR::model()->findAll();
		$user_roles = $this->getUserRoles();
		$rnt = array();
		$j = 0;
		foreach ($roles as $i => $role)
		{
			if ($i%$column == 0) $j++;
			if (!isset($rnt[$j])) $rnt[$j] = array();
			
			$r = array(
				'role_id'=>$role->role_id, 
				'role_name'=>$role->role_name,
				'checked'=>false,
			);
			if (isset($user_roles[$role->role_id]))
				$r['checked'] = true;

			array_push($rnt[$j], $r);

		}

		return $rnt;
	}
		
	function getUserRoles()
	{
		$roles = array();
		try 
		{
			$roles = RoleAR::model()->with(
					array('users'=>array('condition'=>"users.user_id={$this->user_id}"))
			)->findAll();
		}
		catch(CDbException $e)
		{
			return array();
		}
		
		$rnt = array();
		foreach ($roles as $role)
		{
			$rnt[$role->role_id] = $role->role_name;
		}
		
		return $rnt;
	}
	
	private function saveUserRoles($roles, $type = 'update')
	{
		if (!is_null($roles))
		{
			if ($type == 'update')
				UserRoles::model()->deleteAllByAttributes(array('user_id'=>$this->user_id));
				
			if (is_array($roles))
			{
				foreach ($roles as $m)
				{
					$user_role = new UserRoles();
					$user_role->role_id = $m;
					$user_role->user_id = $this->user_id;
					$user_role->save();
				}
			}
			else
			{
				$user_role = new UserRoles();
				$user_role->role_id = $roles;
				$user_role->user_id = $this->user_id;
				
				$user_role->save();				
			}
		}
	}
	
	function getUserModules()
	{
		try
		{
			return ModulesAR::model()->with(
					array(
							'roles',
							'roles.users'=>array('condition'=>"users.user_id={$this->user_id}")
					)		
			)->findAll(array('order'=>'ordering desc'));
		}
		catch(CDbException $e)
		{
			return array();
		}
	}
	
	public function hashPwd($pwd)
	{
		return sha1($pwd);
	}
}
