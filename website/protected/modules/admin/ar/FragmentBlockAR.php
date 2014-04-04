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

class FragmentBlockAR extends FragmentBlock
{
	public function getBlockAllRelationAr($with)
	{
		try
		{
			return self::model()->with($with)->find($this->getTableAlias() . ".db_id='{$this->db_id}'");
		}
		catch(CDbException $e)
		{
			return NULL;
		}
	}
	
	public static function getBlockRowByPK($db_id)
	{
		return CMS::convetARToArray(self::model()->findByPk($db_id),array_keys(self::model()->attributeLabels()));
	}
	
	public function getBlockCopy($with_block_data = false)
	{
		$with = array('fragmentBlockSelections', 'fragmentFields');
		if ($with_block_data) array_push($with, 'fragmentDatas');
		$data = $this->getBlockAllRelationAr($with);
		
		$rnt = array();
		$rnt['fragment_block'] = $data->getAttributes();
		unset($rnt['fragment_block'][$this->getTableSchema()->primaryKey]);

		foreach ($this->relations() as $k => $v)
		{
			if (!in_array($k, $with)) continue;
			
			$ar = $data->__get($k);
			$ar_class = new $v[1];
			$attributeLabels = call_user_func(array($ar_class, 'attributeLabels'));
			$list = CMS::convetARToList($ar, array_keys($attributeLabels));
			foreach ($list as &$l)
				unset($l[$ar_class->getTableSchema()->primaryKey]);
			$rnt[$k] = $list;
		}
		
		return $rnt;
	}
	
	public function copyBlock(array $copy)
	{
		$block = $copy['fragment_block'];
		unset($copy['fragment_block']);
		
		if (!$this->db_key) $this->db_key = $block['db_key'];
		$fb = new FragmentBlockAR();
		$this->copyAttributesFromArray($block, $fb);
		$fb->setAttribute('db_cid', $this->db_cid);
		$fb->setAttribute('db_key', $this->db_key);
		$fb->save();
		
		foreach ($this->relations() as $k => $v)
		{
			if (!isset($copy[$k])) continue;
			
			foreach ($copy[$k] as $arr)
			{
				$ar_class = new $v[1];
				$ar_class->copyAttributesFromArray($arr, $ar_class);
				$ar_class->{$v[2]} = $fb->db_id;
				$ar_class->{$ar_class->getMetaData()->tableSchema->primaryKey} = null;
				if($v[1] == 'FragmentBlockSelection')
				{
					$arr = explode('.', $ar_class->fbs_key);
					$ar_class->fbs_key = $fb->db_key . '.' . end($arr);
				}
				
				$ar_class->save();
			}
		}
	}
	
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	public function addFragmentBlock() {
		$this->save();
	}
	
	public function updateFragmentBlock() {
		$this->modifyByPk();
	}
	
	public function deleteFragmentBlock() {
		self::model()->deleteByPk($this->getPrimaryKey());
		FragmentFieldAR::model()->deleteAllByAttributes(array('db_id'=>$this->db_id));
		FragmentDataAR::model()->deleteAllByAttributes(array('db_id'=>$this->db_id));
	}
}