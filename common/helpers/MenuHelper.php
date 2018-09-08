<?php
namespace common\helpers;

use common\models\Menu;
/**
 * @brief 菜单管理类
 * @version 1.0
 */
class MenuHelper
{
	
	/**
	 * 获取指定组的菜单列列表
	 * @param int $group 组标识 
	 */
	public static function getMenus()
	{
	    $tree = Menu::getMenuTree();
	    return $tree;
	}

}