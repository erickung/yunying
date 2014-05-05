<?php
class ProductRoleAR extends ProductRole
{
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	function updateRole()
	{
		return $this->modifyByPk();	
	}
	
	function addRole()
	{
		return $this->save();
	}
	
	function getProductRoles()
	{
		$roles = $this->findAll();
		$rnt = array();
		foreach ($roles as $role)
		{
			$rnt[$role->pr_id] = $role->pr_name;
		}
		return $rnt;
	}
}