<?php 
class RootTools
{
	public static function diffArraySets(array $old, array $new) {
		$arr_del = array_diff($old, $new);
		$arr_add = array_diff($new, $old);
		return array($arr_del, $arr_add);
	}
	
	public static function get_variable_name(&$var, $scope = NULL) 
	{
       if (NULL == $scope) $scope = $GLOBALS;
 
       $tmp  = $var;
       $var   = "tmp_exists_" . mt_rand();
       $name = array_search($var, $scope, TRUE);
       $var   = $tmp;
 
       return $name;
	}
	
	public static function converToComma($str){
		return explode(',', preg_replace('/(，|,| |　)+/', ',', $str));
	}
	
	public static function date($v, $format=null)
	{
		$format = is_null($format) ? 'Y-m-d' : $format;
		return empty($v) ? '' : date($format, $v);
	}
}