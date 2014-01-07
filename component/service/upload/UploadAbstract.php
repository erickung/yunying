<?php
abstract class UploadAbstract
{
	protected $file_obj;
	protected $file_name;
	protected $ext;
	protected $full_file_name;
	protected $error;
	protected $max_size;
	protected $params = array();
	protected $upload_path;
	
	private $ins_name;

	public function setInsName($ins_name)
	{
		$this->ins_name = $ins_name;
		return $this;
	}
	
	abstract public function getFileName();
	
	abstract public function checkFile();
	
	abstract public function getFileRef();
	
	public function setMaxSize($size)
	{
		$this->max_size = intval($size);
		return $this;
	}
	
	public function setUploadPath($upload_path)
	{
		$this->upload_path = $upload_path;
		return $this;
	}
	
	public function setParams(array $params)
	{
		$this->params = array_merge($this->params, $params);
		return $this;
	}
	
	public function getFullName()
	{
		return $this->full_file_name;
	}
	
	public function getError()
	{
		return $this->error;
	}
	
	public function upload()
	{
		$this->initFile();
		if(is_object($this->file_obj) && get_class($this->file_obj)==='CUploadedFile')
		{
			if (!$this->checkFile()) return false;
			return $this->file_obj->saveAs($this->getFullName());//保存到服务器
		}
	
		return false;
	}
	
	public function getFilePath()
	{
		return $this->getUploadDir() . $this->getFileName() . '.' . $this->ext;
	}
	
	private function initFile()
	{
		$this->file_obj = CUploadedFile::getInstanceByName($this->ins_name);//读取图像上传域,并使用系统上传组件上传
		$this->ext = strtolower($this->file_obj->extensionName);	//上传文件的扩展名
		$this->file_name = $this->getFileName() . '.' . $this->ext;
		$this->full_file_name =  $this->getFullUploadDir() . $this->file_name;
	}
	
	protected function checkSize()
	{
		if ($this->file_obj->getSize() > $this->max_size)
		{
			$this->error = '不能大于4M';
			return false;
		}
		return true;
	}
	
	protected function getUploadDir()
	{
		return date('Ym') . DS;
	}
	
	protected function getFullUploadDir()
	{
		$dir = rtrim($this->upload_path, '/') . '/' . $this->getUploadDir();
		if (!is_dir($dir)) self::make_dir($dir);
		return $dir;
	}
	
	/**
	 * @Purpose: 循环创建目录，直到最底层
	 * @Param: string $dir 要创建目录的绝对路径。如：/root/temp/cache
	 * @Return: 成功返回true，异常返回false
	 */
	public static function make_dir($dir)
	{
		return is_dir($dir) || (self::make_dir(dirname($dir)) && mkdir($dir, 0777));
	}
}