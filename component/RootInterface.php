<?php
interface RootInterface
{
	
}

interface ActiveRecordInterface
{
	public function isTableFiled($name);
	
	public function primaryKey();
	
	public function setAttribute($name, $value);
	
	public function getAttributes();
	
	public function getIsNewRecord();
}

interface ActiveRecordAppInterface
{
	public function getCondition(array $custom_cond, array $resets);
	
	public function copyAttributesFromAR($from, $to);
	
	public function copyAttributesFromArray($from, $to);
}