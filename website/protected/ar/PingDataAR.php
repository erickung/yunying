<?php
class PingDataAR extends PingData
{
	function getTimeReg($range, $type)
	{
		//var_dump($range, $type);
		if ($range == 1)	// 1天
			 return "H";
		if ($range == 2)	// 7天
		{
			if ($type == 'hour') return 'mdH';
			if ($type == 'day') return 'Ymd'; 
		}
		if ($range == 3)
			return 'Ymd';
	}
	
	function getTime($time_reg, $range, $time)
	{
		return date($time_reg, strtotime($time));
			
	}
	
	function getLoss($loss)
	{
		$rnt = array();
		$i = 0;
		foreach ($loss as $line => $data)
		{
			$rnt[$i]['line'] = $line;
			$rnt[$i]['data'] = array();
			foreach ($data as $h => $d)
			{
				array_push($rnt[$i]['data'], array($h, sprintf("%.2f", ($d['all_loss']/$d['all_transmitted'])*100)));
			}
			$i++;
		}
		return $rnt;
	}
	
	function getResp($resp)
	{
		$rnt = array();
		$i = 0;
		foreach ($resp as $line => $data)
		{
			$rnt[$i]['line'] = $line;
			$rnt[$i]['data'] = array();
			foreach ($data as $h => $d)
			{
				array_push($rnt[$i]['data'], array($h, sprintf("%.2f", ($d['resp_time']/$d['num']))));
			}
			$i++;
		}
		return $rnt;
	}
	

}