<?php
defined('fileaway') or die('Water, water everywhere, but not a drop to drink.');
if(!function_exists('sssc_fileaway'))
{
	function sssc_fileaway($atts)
	{
		$fileaway = new fileaway; 
		return $fileaway->sc($atts);
	}
}
if(!function_exists('sssc_attachaway'))
{
	function sssc_attachaway($atts)
	{
		$attachaway = new attachaway; 
		return $attachaway->sc($atts);
	}
}
if(!function_exists('ssfa_fileup'))
{
	function ssfa_fileup($atts)
	{
		$fileup = new fileup; 
		return $fileup->sc($atts);
	}
}
if(!function_exists('ssfa_fileaframe'))
{
	function ssfa_fileaframe($atts)
	{
		$fileafrane = new fileaframe; 
		return $fileaframe->sc($atts);
	}
}