<?php
defined('fileaway') or die('Water, water everywhere, but not a drop to drink.');
$manager = 0;
while(!$manager)
{
	if(current_user_can('administrator')) $manager = 1; 
	if($manager) break;
	$allowed_roles = explode(',', $this->op['manager_role_access']);
	if(is_array($allowed_roles))
	{
		foreach($allowed_roles as $role)
		{ 
			if(current_user_can(trim($role)))
			{ 
				$manager = 1; 
				break; 
			}
		}
	}
	if($manager) break;
	$allowed_users = explode(',', $this->op['manager_user_access']);
	if(is_array($allowed_users))
	{
		foreach($allowed_users as $user)
		{ 
			if($fa_userid == trim($user))
			{ 
				$manager = 1; 
				break; 
			}
		}
	}
	if($manager) break;
	if($password)
	{ 
		if($password == $this->op['managerpassword'])
		{
			if($role_override)
			{
				if(preg_match("/fa-userrole/i", $role_override))
				{ 
					if($logged_in)
					{ 
						$manager = 1; 
						break; 
					}
				}
				else
				{
					$override_roles = preg_split('/(, |,)/', trim($role_override), -1, PREG_SPLIT_NO_EMPTY); 
					if(is_array($override_roles))
					{
						foreach($override_roles as $role)
						{
							if(current_user_can($role))
							{ 
								$manager = 1; 
								break; 
							}
						}
					}
					if($manager) break;
				}
			}
			if($user_override)
			{
				if(preg_match("/fa-userid/i", $user_override))
				{ 
					if($fa_userid == get_current_user_id())
					{ 
						$manager = 1; 
						break;
					}
				}
				else
				{
					$override_users = preg_split('/(, |,)/', trim($user_override, ' '), -1, PREG_SPLIT_NO_EMPTY);
					if(is_array($override_users))
					{
						foreach($override_users as $user)
						{ 
							if($fa_userid == $user)
							{ 
								$manager = 1; 
								break;
							}
						}
					}
					if($manager) break;
				}
			}
		}
	}
	break;
}
if($manager && !$dirman_access) $dirman = 1;
elseif($manager && $dirman_access)
{ 
	$dirman = 0; 
	$dirman_roles = preg_split('/(, |,)/', $dirman_access, -1, PREG_SPLIT_NO_EMPTY); 
	if(is_array($dirman_roles))
	{
		foreach($dirman_roles as $drole)
		{ 
			if(current_user_can($drole))
			{ 
				$dirman = 1; 
				break; 
			}
		}
	}
}
else $dirman = 0;
if($manager)
{ 
	$directories = true; 
	$bulkdownload = false;
}