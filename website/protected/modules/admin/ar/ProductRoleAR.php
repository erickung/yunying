<?php
class ProductRoleAR extends ProductRole
{
	function updateRole()
	{
		return $this->modifyByPk();	
	}
	
	function addRole()
	{
		return $this->save();
	}
}