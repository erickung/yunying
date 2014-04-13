<?php
class ProductRules
{
	public static function saveExpectedRateReturn($v)
	{
		str_replace(array('-','——','-','~'), '-', $v);
	}
}