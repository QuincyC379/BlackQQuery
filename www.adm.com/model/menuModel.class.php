<?php

/**
 * 和菜单权限控制相关的模型
 */

class menuModel extends commonModel
{


    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @date 20180719
     * @author  huangweigang
     * 获取菜单树
     */
    public function getAllMenuTree()
    {
        //获取所有的菜单列表
        $list = model('common')->_select(
            'admin_menus', 'is_delete=0', 'id desc,sort desc', "0,10000"
        );
        foreach ($list as $mk => $menu) {
            if (strlen($menu['machine_name']) > 0) {
                $urlinfo = explode('_', $menu['machine_name']);
                $menu['module'] = $urlinfo[0];
                $menu['action'] = $urlinfo[1];
            } else {
                $menu['module'] = '';
                $menu['action'] = '';
            }
            $list[$mk] = $menu;
        }
        $list = $this->menusSort($list);
        foreach ($list as $mk =>$menu) {
            if(isset($menu['children'])){
                $childrenUrls = array_column($menu['children'],'machine_name');
                $menu['childrenUrls'] = $childrenUrls;
            }
            $list[$mk] = $menu;
        }
        return $list;
    }

    /**
     * 对菜单重新处理 排序 让排序是一级菜单下面带着他的二级菜单这样 并且排序
     * @date   2018-07-19
     * @author huangweigang
     * @param $menus array  传入菜单数组  不是菜单树 是一个 普通的二维数组 不包含嵌套的  来自函数 $this->>getAllMenuTree
     * @return  array 返回 菜单树
     */
    public function menusSort($menus)
    {
        if (!is_array($menus)) $menus = [];
        $firstMenus = [];
        foreach ($menus as $mk => $menu) {
            if ($menu['type'] == 1)//一级菜单
            {
                $firstMenus[$menu['id']] = $menu;
                unset($menus[$mk]);
            }
        }
        //一级菜单排序
        $firstMenusSort = array_column($firstMenus, 'sort');
        array_multisort($firstMenusSort, SORT_DESC, $firstMenus);
        //重置key
        $firstMenusNew = [];
        foreach ($firstMenus as $mk => $menu) {
            $firstMenusNew[$menu['id']] = $menu;
        }
        $firstMenus = $firstMenusNew;
        //构建菜单树
        foreach ($menus as $mk => $menu) {
            $firstMenus[$menu['parent_id']]['children'][] = $menu;
        }
        //对子菜单排序
        foreach ($firstMenus as $mk => $menu) {
            if (!isset($menu['children']))//没有子菜单
            {
                $menu['children'] = [];
                continue;
            }
            $children = $menu['children'];
            $childrenSort = array_column($children, 'sort');
            array_multisort($childrenSort, SORT_DESC, $children);
            $firstMenus[$mk]['children'] = $children;
        }
        return $firstMenus;
    }


}
