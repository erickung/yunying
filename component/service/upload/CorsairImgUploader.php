<?php
class CorsairImgUploader extends ImgUploader
{
	private $pic_id;
	private $pic_dir =  array(
			'SPICHEAD_FOLDER' => 'staff/head/',
			'SPHOTO_FOLDER' => 'staff/photos/',
			'SBGND_FOLDER' => 'staff/background/',
			'SFOCUS_FOLDER' => 'staff/focus/',
			'LIVE_FOLDER' => 'live/',
			'LIVE_PRE_FOLDER' => 'live/pre/',
			'AD_FOLDER' => 'ad/',
			'MEDIA_FOLDER' => 'media/program/',
			'TAG_FOLDER' => 'tags/',
			'UBGND_FOLDER' => 'user/background/',
			'MSERIAL_FOLDER' => 'media/serials/',
			'VIDEO_FOLDER' => 'video/',
			'MEDIA_TOP_PIC_FOLDER' => 'media/top/',
			'VIDEO_UGC_FOLDER' => 'video_ugc/',
			'EVENT_FOLDER' => 'event/',
			'POST_FOLDER' => 'post/',
			'ENT_FOLDER' => 'ent/',
			'SPECIAL_FOLDER' => 'special/',
			'FOCUS_FOLDER' => 'focus/',
			'LOGO_FOLDER' => 'logo/',
			'USERCARD' => 'usercard/',
			'SMGBB_FOLDER' => 'smgbb/',
			'VOTE_FOLDER' => 'voteimg/',
			'SOURCE_FOLDER' => 'source/',
			'NEWS_FOLDER' => 'news/',	
	);
	private $videoMaps = array(
			'normal'=>'video',
			'news'=>'news',
			'ugc'=>'video_ugc',
	);
	
	public function __construct()
	{
		parent::__construct();
		$this->pic_id = Corsair::getRedisCounter(CMSConsts::COUNT_PIC);
	}
	
	public function getFilePath()
	{
		return $this->getUploadDir().$this->pic_id . '.' . $this->ext;
	}
	
	public function getFileRef()
	{
		return PICTURE_URL . $this->getFilePath();
	}
	
	public function setUploadPath($upload_path)
	{
		$this->upload_path = PICTURES;
		return $this;
	}
	
	protected function getUploadDir()
	{
		$pic_folder = '';
		if (isset($this->params['videoid'])) {
			$corsair_pic_type = $this->getVideoPicTypeByVideoid($this->params['videoid']);
			if(empty($corsair_pic_type)) Response::failure("不属于正常微视频类型");
			$this->params['corsair_pic_type'] = $corsair_pic_type;
		}
		if (isset($this->params['corsair_pic_type']))
			$pic_folder = $this->pic_dir[strtoupper($this->params['corsair_pic_type'] . '_FOLDER')];
			
		return $pic_folder . Corsair::callAssist('hash_path', "{$this->pic_id}") . '/';
	}
	
	public function getFileName()
	{
		return $this->pic_id;
	}
	
	public function getVideoPicTypeByVideoid($videoid)
	{
		$corsair_pic_type = "";
		Yii::import('application.modules.media.ar.*');
		$video_class = VideoAR::getVideoClassbyVideoid($_GET['videoid']);
		if(in_array($video_class, array_keys($this->videoMaps)))
			$corsair_pic_type = $this->videoMaps[$video_class];
		return $corsair_pic_type;
	}
}