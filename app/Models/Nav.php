<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nav extends Model
{
    # todo:
    # 1.用户添加菜单，选择菜单类型。
    # 2.用户编辑菜单 ,可编辑名称、类型、内容
    # 3.菜单为单页和链接类型可以，存在父级菜单，其他菜单父级为0 全局只支持最多二级菜单
    # 4.后台添加单页。在菜单中可以选择显示、也可分享页面给用户


    const TYPE_EMPTY = 0;    // 普通菜单 可添加单页 链接
    const TYPE_MENU = 1;     // 分类菜单（固定存在的）
    const TYPE_ARCHIVE = 2;  // 归档页面（固定存在的）
    const TYPE_PAGE = 3;     // 单页（ajax请求单页列表，再选择单页， url为单页id）
    const TYPE_LINK = 4;     // 链接（直接添加链接）
    const LIMIT_NUM = 14;    // 最大菜单数
}