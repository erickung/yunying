<?php
function smarty_function_input($params, &$smarty)
{

	if (empty($params['name'])) {
		throw new CException(Yii::t('yiiext', "You should specify name  parameters."));
	}

	$name = $params['name'];
	if (isset($params['obj']))
	{
		$ar = $params['obj'];
		$value = !is_null($ar->{$name}) ? CHtml::encode($ar->{$name}) : '';
		$label = $ar->labels[$name];
	} 
	else 
	{
		$value = ($params['value']) ? CHtml::encode($params['value']) : '';
		$label = $params['label'];
	}



	$text = 
<<<TEXT
	<label for="$name" class="control-label">$label</label>
	<div class="controls">
		<input type="text" name="$name" id="$name" class="input" value="$value">
	</div>
TEXT;

	return $text;
}