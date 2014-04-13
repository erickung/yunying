<?php
function smarty_function_input_addon($params, &$smarty)
{

	if (empty($params['name']) || empty($params['obj'])) {
		throw new CException(Yii::t('yiiext', "You should specify name and obj parameters."));
	}

	$name = $params['name'];
	$ar = $params['obj'];
	
	$value = !is_null($ar->{$name}) ? CHtml::encode($ar->{$name}) : '';
	$label = $ar->labels[$name];
	$add = $params['add'];

	$text = 
<<<TEXT
	<label for="$name" class="span4 control-label">$label</label>
	<div class="span7 input-append">
		<input class="span5" type="text" name="$name" id="$name" class="input" value="$value">
		<span class="add-on">$add</span>
	</div>
TEXT;

	return $text;
}