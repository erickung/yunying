<?php
function smarty_function_require($params,&$smarty)
{
	if (empty($params['file'])) {
		throw new CException(Yii::t('yiiext', "You should specify file parameters."));
	}
	$file = $params['file'];
	$path = '';
	$files = explode('.', $file);
	$file = array_pop($files);
	$dir = array_pop($files);
	$path = implode('.', $files) . '.views.' . $dir;

	return $smarty->fetch(Yii::getPathOfAlias($path).'/'.$file.'.htm');
}