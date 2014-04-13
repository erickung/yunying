<?php
function smarty_function_select($params, &$smarty) 
{
	if (empty($params['obj']) || empty($params['name'])) {
		throw new CException(Yii::t('yiiext', "You should specify obj and name parameters."));
	}
	$ar = $params['obj'];
	$name = $params['name'];
	$data = $params['data'];
	$label = $ar->labels[$name];

	$select = "<label for='$name' class='control-label'>$label</label><div class='controls'><select id='$name' name='$name'>";
	foreach ($data as $k => $v)
	{
		$selected = $ar->{$name} == $k ? 'selected="selected"' : '';

		$select .= "<option value='$k' $selected>$v</option>";
	}
	$select .= '</select></div>';

	return $select;
}