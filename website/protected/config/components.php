<?php
return array(
		
		'user'=>array(
			// enable cookie-based authentication
			'class' => 'application.components.base.WebUser',
			'allowAutoLogin'=>true,
		),
		
		'smarty'=>array(
		   // 'class'=>'application.extensions.smarty.CSmarty',
				 'class'=>'root.base.RootSmarty',
		),
		// uncomment the following to enable URLs in path-format
		'urlManager'=>array(
			'urlFormat'=>'path',
			//'showScriptName' => false,
			//'urlSuffix' => '',
			'rules'=>array(
				//'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				//'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				//'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
				//'<controller:\w+>/<action:\w+\.js>'=>'<controller>/<action>',
			),
		),
		
		'mongodb' => array(
				'class'            => 'EMongoDB',
				'connectionString' => 'mongodb://192.168.16.161',
				'dbName'           => 'corsair_video',
				'fsyncFlag'        => true,
				'safeFlag'         => true,
				'useCursor'        => false
		),
		
		'db'=> RunTime::getDBConf('pocket'),
		'db_corsair' => RunTime::getDBConf('corsair'),
		
		'errorHandler'=>array(
			// use 'site/error' action to display errors
			'errorAction'=>'site/error',
		),
		'log'=>RunTime::getLogConf(),
	);