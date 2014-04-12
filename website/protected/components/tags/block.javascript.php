<?php
function smarty_block_javascript($params, $content, $template, &$repeat) 
{
	RootSmarty::addJavascript($content);
}
