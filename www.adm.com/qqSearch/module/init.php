<?php

	//权限配置
	function JudgePermission($key)
	{
		$Permission = json_decode($_SESSION['groupInfo']['group_access'],true);
		if(is_array($Permission) && in_array($key, $Permission)){
			return true;
		}

		return false;
	}