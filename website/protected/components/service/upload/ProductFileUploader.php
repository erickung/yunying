<?php
class ProductFileUploader extends FileUploader
{
	private $product_id;			
	
	function getFileName()
	{
		if (!$this->upload_file_name) $this->upload_file_name = rand(0, 1000000);
		$str = $this->product_id.$this->upload_file_name. WebUser::Instance()->user->user_id . microtime();
		return md5($str);
	}
	
	protected function getUploadDir()
	{
		return 'product' . DS . $this->product_id % 100 . DS;
	}
	
	public function checkFile()
	{
		return true;
	}
	
	/*
	public function upload()
	{
		$this->initFile();
		$chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
		$chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;
		
		if (!$out = @fopen("{$this->full_file_name}.part", $chunks ? "ab" : "wb")) {
			die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
		}
		
		if (!empty($_FILES)) {
			if ($_FILES["file"]["error"] || !is_uploaded_file($_FILES["file"]["tmp_name"])) {
				die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
			}
		
			// Read binary input stream and append it to temp file
			if (!$in = @fopen($_FILES["file"]["tmp_name"], "rb")) {
				exit('ddd');
				die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
			}
		} else {	
			if (!$in = @fopen("php://input", "rb")) {
				die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
			}
		}
		
		while ($buff = fread($in, 4096)) {
			fwrite($out, $buff);
		}
		
		@fclose($out);
		@fclose($in);
		
		// Check if file has been uploaded
		if (!$chunks || $chunk == $chunks - 1) {
			// Strip the temp .part suffix off 
			rename("{$this->full_file_name}.part", $this->full_file_name);
		}
		
	}*/
}