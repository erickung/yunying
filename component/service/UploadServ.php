<?php
class UploadServ
{
	public $path;
	//public $file;
	public $file_name;
	public $ext;
	public $full_file_name;
	private $type;
	private $file_obj;
	private $error = '';
	private $dir = '';
	private $types = array(
		CMSConsts::IMAGE => array(
				'png','gif','jpg'
				),		
			
	);
	private $params = array();
	private $width = 0;
	private $height = 0;
	private $max_size = 4194304;//2048*2048
	
	public function __construct($type, $path = UPLOAD_PATH)
	{
		$this->initFileObj();
		$this->path = $path;
		$this->setDir(); 
		$this->setFilePath();
		
		if (!isset($this->types[$type]))
			throwException("please use such types: " . implode(',', $this->types));
		$this->type = $type;
	}
	
	public function getError()
	{
		return $this->error;
	}
	
	public function setParams(array $params)
	{
		$this->params = array_merge($this->params, $params);
		return $this;
	}
	
	public function setMaxSize($size)
	{
		$this->max_size = intval($size);
	}
	
	private function initFileObj($ins_name = 'Filedata')
	{
		$this->file_obj = CUploadedFile::getInstanceByName($ins_name);//读取图像上传域,并使用系统上传组件上传
	}
	
	private function setDir()
	{
		$this->dir = date('Ym') . '/';
		if (!is_dir($this->path . DS . $this->dir))
			mkdir($this->path . DS . $this->dir);
	}
	
	private function setFilePath()
	{
		$pathd = UPLOAD_PATH . DS;
		//$filename = time().rand(0,9);
		$filename = self::getFileName();
		$this->ext = strtolower($this->file_obj->extensionName);	//上传文件的扩展名
		$this->file_name = $this->dir . $filename . '.' . $this->ext;
		$this->full_file_name = $this->path . DS . $this->file_name;
	}
	
	private static function getFileName()
	{
		return substr(md5(WebUser::Instance()->user->user_id . time()), 0, 10) . rand(0,9);
	}

	private function checkFile()
	{		
		if ($this->file_obj->getSize() > $this->max_size)
		{
			$this->error = '不能大于4M';
			return false;
		}
		
		$mime_types = CMS::loadConf(Yii::getPathOfAlias('system.utils.mimeTypes') . '.php', true) ;
		$imagesize = getimagesize($this->file_obj->getTempName());
		
		if (!in_array($this->ext, $this->types[$this->type]) || !$imagesize || $imagesize['mime'] != $mime_types[$this->ext])
		{
			$this->error = '不合法的类型';
			return false;
		}
		
		if ((isset($this->params['width']) && abs($this->params['width'] - $imagesize[0]) > 1 && $this->params['width']>0)
			|| (isset($this->params['height']) && abs($this->params['height'] - $imagesize[1]) > 1 && $this->params['height']>0)) 
		{
			$this->error = '上传图片必须是长度为：' . $this->params['width'] . ' 高度为：' . $this->params['height'];
			return false;
		}
		
		return true;
	}
	
	public function upload()
	{
		if(is_object($this->file_obj) && get_class($this->file_obj)==='CUploadedFile')
		{
			if (!$this->checkFile()) return false;
			return $this->file_obj->saveAs($this->full_file_name);//保存到服务器
		}
		
		return false;
	}
}