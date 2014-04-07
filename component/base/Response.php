<?php
class Response
{
	public static function resp($boolean, $msg='', $url)
	{
		($boolean) ? Response::postSuccess($msg, $url) : Response::postFailure('提交失败！');
	}
	
	public static function postSuccess($msg='', $url)
	{
		self::showPostMsg("eric.response.success('提交成功！', '$url')");
	}
	
	public static function postFailure($msg='')
	{
		
	}
	
	private static function showPostMsg($js)
	{
		$Html = 
<<<HTML
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		</head>
		<script>parent.$js;</script>
		</html>
		
HTML;
		echo $Html;
	}
	
	/*
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
	*/
	
	public static function error($msg = '')
	{
		echo json_encode(array('success' => false, 'msg' => $msg));
		exit();
	}
	
}