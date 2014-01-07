<?php
class MongoActiveRecordAdapter extends ActiveRecordAdapter 
{
	public function getCondition()
	{
		$this->processCustom();
		
		$criteria = new EMongoCriteria;
		$sort = $this->order();
		$criteria->sort($sort[0], $sort[1])->limit($this->limit())->offset($this->offset());
		
		return $this->getConditionParams($criteria);
	}
	
	private function getConditionParams($criteria)
	{
		if (empty($this->searchs)) return $criteria;
	
		foreach ($this->searchs as $k => $cond)
		{
			$opt = '==';
			$value = '';
			$relat = 'AND';
			if (is_array($cond) && count($cond) == 3)
			{
				$relat = $cond[0];
				$opt = $cond[1];
				$value = $cond[2];

				switch ($opt)
				{
					case 'like':
						$criteria->$k = new MongoRegex("/{$value}.*/i");
					default :
						$criteria->$k($opt, $value);
				}
			}			
		}
	
		return $criteria;
	}
	
	protected function order()
	{
		$sort = isset($_GET['sort']) ? $_GET['sort'] : $this->active_record->primaryKey();
		$dir = (isset($_GET['dir']) && strtolower(trim($_GET['dir'])) == 'asc') ? EMongoCriteria::SORT_ASC : EMongoCriteria::SORT_DESC;
		return  array($sort, $dir);
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