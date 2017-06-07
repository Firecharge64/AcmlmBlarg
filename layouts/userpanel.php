<?php
$userMenu = array();

if($loguserid)
{
	if (HasPermission('admin.viewdashboard'))
	{
		$userMenu[actionLink('dashboard')] = __('Dashboard');
	    if (HasPermission('user.editprofile'))
	{
		$userMenu[actionLink('editprofile')] = __('Edit profile');
		if (HasPermission('user.itemshop'))
			$userMenu[actionLink('shop')] = __('Item Shop');
	}        
    $userMenu[actionLink('warnings')] = __('View warnings');
	$userMenu[actionLink('private')] = __('Private messages');
	$userMenu[actionLink('favorites')] = __('Favorites');

	$bucket = 'userMenu'; include("./lib/pluginloader.php");
}

$layout_userpanel = $userMenu;
?>
