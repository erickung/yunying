<?php
class CActiveRecordAdapter extends ActiveRecordAdapter
{
	private $cond = array('AND', '=', '');
	
	public function getCondition()
	{
		$this->processCustom();
		$cond_params = $this->getConditionParams();
		$conds = array();
		if (!empty($cond_params))
		{
			$conds = array(
					'condition' => $cond_params[0],
					'params'=>$cond_params[1],
			);
		}
		$condition = array(
				'order' => $this->order(),
				'limit' => $this->limit(),
				'offset' => $this->offset(),
		);
		
		return array_merge($conds, $condition);
	}
	
	private function getConditionParams()
	{
		if (empty($this->searchs)) return array();
		$params = array();
		$condition = '1=1';
		
		foreach ($this->searchs as $k => $cond)
		{
			$p_k = ':'. str_replace('.', '', $k);
			list($k, $cond) = $this->resetCond($k, $cond);

			$con = $this->cond;
			$opt = '=';
			$value = '';
			$relat = 'AND';
			
			if (is_array($cond) && count($cond) == 3)
			{
				$con = $cond;
				$value = $con[2];
				if ($con[1] == 'like') $value = "%{$value}%";
				
				$params[$p_k] = $value;
			}
			else
			{
				$params[$p_k] = $cond;
			}
			
			$relat = $con[0];
			$opt = $con[1];
			
			$condition .= ' ' . $relat . ' ' . $k . ' ' . $opt . ' ' . $p_k ;
		}
	
		return array($condition, $params);
	}
	
	private function resetCond($k, $cond)
	{
		if (!isset($this->reset[$k])) return array($k, $cond);
		$reset = $this->reset[$k];
		
		if (is_array($reset))
		{
			if ($reset[0]) $k = $reset[0];
			if (isset($reset[1])) 
			{
				$value = $cond;
				if (is_array($cond))
					$value = $cond[2];
				
				$cond = $this->cond;
				$cond[1] = $reset[1];
				$cond[2] = $value;
			}
		}
		else
			$k = $reset;
		
		return array($k, $cond);
	}
	
	protected function order()
	{
		$sort = isset($_GET['sort']) ? $_GET['sort'] : $this->active_record->primaryKey();
		return $sort . ' ' . $_GET['dir'];
	}
	
	protected function limit()
	{
		return isset($_GET['limit']) ? $_GET['limit'] : 20;
	}
	
	protected function offset()
	{
		return isset($_GET['start']) ? $_GET['start'] : 0;
	}
}