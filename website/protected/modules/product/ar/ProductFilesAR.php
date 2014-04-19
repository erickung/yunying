<?php
class ProductFilesAR extends ProductFiles
{	
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	function downFile()
	{
		$file = $this->findByPk($this->file_id);
		$file_name = str_replace(array('',' '), '', $file->file_name);
		header('Content-Description: File Transfer');
		
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename='."$file_name");
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Length: ' . filesize($file->file_path));
		readfile($file->file_path);
	}
}