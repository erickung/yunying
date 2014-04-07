<?php
//////////////////////////////////////////////////////////////////////////////////////////
// Author: 孔繁兴
// Copyright 2005-, Funshion Online Technologies Ltd. All Rights Reserved
// 版权 2005-，北京风行在线技术有限公司 所有版权保护
// This is UNPUBLISHED PROPRIETARY SOURCE CODE of Funshion Online Technologies Ltd.;
// the contents of this file may not be disclosed to third parties, copied or
// duplicated in any form, in whole or in part, without the prior written
// permission of Funshion Online Technologies Ltd.
// 这是北京风行在线技术有限公司未公开的私有源代码。本文件及相关内容未经风行在线技术有
// 限公司事先书面同意，不允许向任何第三方透露，泄密部分或全部; 也不允许任何形式的私自备份.
//////////////////////////////////////////////////////////////////////////////////////////
 
class ModulesAR extends Modules
{
	const IS_ACTION_MODULE = 1;
	const IS_ROOT_MODULE = 0;
	public $href;
	
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	public function getModules()
	{
		return self::model()->findAll();
	}
	
	public function addModule()
	{
		return $this->add();
	}
	
	public function editModule()
	{
		$this->modifyByPk();
	}
	
	public function isRootModule()
	{
		return $this->parent_module_id == self::IS_ROOT_MODULE;
	}

	public function isActionModule()
	{
		return $this->is_action == self::IS_ACTION_MODULE;
	}
}