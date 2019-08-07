<?php
defined('fileaway') or die('Water, water everywhere, but not a drop to drink.');
$redirect = $redirect && ($manager || $logged_in || !$this->op['redirect']) ? false : $redirect;
$guest = $redirect ? 'true' : 'false';
$stats = $redirect ? false : $stats;
$stats = $stats == 'true' && $this->op['stats'] == 'true' ? true : false;
if($stats)
{
	if($this->op['ignore_roles'])
	{
		$ignoredr = preg_split('/(, |,)/', trim($this->op['ignore_roles']), -1, PREG_SPLIT_NO_EMPTY);
		foreach($ignoredr as $r)
		{
			if(current_user_can($r)) $stats = false;
			break;
		}
	}
}
if($stats)
{
	if($this->op['ignore_users'])
	{
		$ignoredu = preg_split('/(, |,)/', trim($this->op['ignore_users']), -1, PREG_SPLIT_NO_EMPTY);
		if(in_array($fa_userid, $ignoredu)) $stats = false;
	}
}