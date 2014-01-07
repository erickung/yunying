<?php
class Response
{
	public static function resp($boolean, $data = array())
	{
		($boolean) ? Response::success($data) : Response::failure('提交失败！');
	}
	
	public static function success($data = array(), $custom = array())
	{
		echo json_encode(self::getResponseData($data, true, $custom));
	}
	
	public static function failure($msg = '')
	{
		$msg = array('msg' => $msg);
		echo json_encode(self::getResponseData(array(), false, $msg));
		exit();
	}
	
	public static function error($msg = '')
	{
		echo json_encode(array('success' => false, 'msg' => $msg));
		exit();
	}
	
	private static function getResponseData($data, $flag=true, $custom = array())
	{
		$rnt = array('success'=> $flag);
		$rnt['data'] = $data;
		if (!empty($custom))
		{
			$rnt = array_merge($rnt, $custom);
		}
		
		return $rnt;
	}
	
}