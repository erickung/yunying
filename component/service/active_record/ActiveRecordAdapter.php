<?php
abstract class ActiveRecordAdapter
{	
	protected $active_record;
	#protected $searchs = array();
	#protected $custom_cond = array();
	#protected $reset = array();
	
	public function __construct($active_record, $params)
	{
		$this->active_record = $active_record;
		if (!empty($params))
		{
			foreach ($params as $k => $v)
				$this->{$k} = $v;
		}
	}
	
	protected function processCustom()
	{
		if (empty($this->custom_cond)) return;
		
		foreach ($this->custom_cond as $k => $v)
		{
			if (isset($this->searchs[$k]))
			{
				$vaule = $this->searchs[$k];
				if (is_array($v))
				{
					$this->searchs[$k] = array('AND', $v[0]);
					if (isset($v[1])) $this->searchs[$k][0] = $v[1];
					$this->searchs[$k][2] = $vaule;
				}
				else
				{
					$this->searchs[$k] = array('AND', $v, $vaule);
				}
			}
		}
	}
	
	abstract protected function getCondition();
	
	abstract protected function limit();
	
	abstract protected function order();
	
	abstract protected function offset();
	
}