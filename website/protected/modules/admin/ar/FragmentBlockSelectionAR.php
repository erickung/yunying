<?php
class FragmentBlockSelectionAR extends FragmentBlockSelection
{
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	public function getSelectInfo()
	{
		$selection_info = $this->findByPk($this->fbs_id);
		$fb = FragmentBlock::model()->findByPk($selection_info->db_id);
		$selection_info->fbs_key = str_replace($fb->db_key . '.', '', $selection_info->fbs_key);
		
		return $selection_info;
	}
	
	public function getBlockSelectInfo(FragmentBlockSelection $selection_info = null)
	{		
		$custom = array();
		$fragfield = new FragmentFieldAR();
		$select_data = $fragfield->getSelectFields(array('db_id' => $this->db_id));
		if (!$select_data) return null;
		
		foreach ($select_data as &$select)
		{
			$select_value = (!is_null($selection_info) && isset($selection_info->where[$select->dbc_id])) ? $selection_info->where[$select->dbc_id] : '';
			$select = CMS::convetARToArray($select);
			$select['dbc_data'] = unserialize($select['dbc_data']);
				
			foreach ($select['dbc_data'] as &$v)
			{
				$v['selected'] = ($v['value'] == $select_value) ? 1 : 0;
			}
		}
		
		return $select_data;
	}
	
	protected function addSelection($role = null)
	{
		$this->setFbsKey();
		$this->save();
	}
	
	protected function updateSelection($role = null)
	{
		$this->setFbsKey();
		$this->modifyByPk();
	}
	
	private function setFbsKey()
	{
		$fb = FragmentBlock::model()->findByPk($this->db_id);
		$this->fbs_key = $fb->db_key . '.' . $this->fbs_key;
	}
}