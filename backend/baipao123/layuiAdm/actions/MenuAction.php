<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/4/14
 * Time: 下午5:27
 */

namespace layuiAdm\actions;

use Yii;


class MenuAction extends ApiAction
{
    public $module;

    public function run() {
        $menu = [
            [
                "title"  => "首页",
                "icon"   => "&#xe68e;",
                "href"   => "/site/home",
                "spread" => false
            ],
            [
                "title"    => "轮播管理",
                "icon"     => "icon-icon10",
                "href"     => "",
                "spread"   => false,
                "children" => [
                    [
                        "title"  => "轮播列表",
                        "icon"   => "&#xe612;",
                        "href"   => "/slider/list",
                        "spread" => false
                    ]
                ]
            ],
            [
                "title"    => "企业管理",
                "icon"     => "icon-icon10",
                "href"     => "",
                "spread"   => false,
                "children" => [
                    [
                        "title"  => "企业列表",
                        "icon"   => "&#xe612;",
                        "href"   => "/company/list",
                        "spread" => false
                    ],
                    [
                        "title"  => "待审核列表",
                        "icon"   => "&#xe612;",
                        "href"   => "/company/verify-list",
                        "spread" => false
                    ],
                    [
                        "title"  => "岗位列表",
                        "icon"   => "&#xe612;",
                        "href"   => "/job/list",
                        "spread" => false
                    ],
                ]
            ],
            [
                "title"    => "用户管理",
                "icon"     => "icon-icon10",
                "href"     => "",
                "spread"   => false,
                "children" => [
                    [
                        "title"  => "用户列表",
                        "icon"   => "&#xe612;",
                        "href"   => "/user/list",
                        "spread" => false
                    ],
                    [
                        "title"  => "岗位记录",
                        "icon"   => "&#xe612;",
                        "href"   => "/user/job-list",
                        "spread" => false
                    ],
                    [
                        "title"  => "工时记录",
                        "icon"   => "&#xe612;",
                        "href"   => "/user/daily-list",
                        "spread" => false
                    ],
                    [
                        "title"  => "打卡记录",
                        "icon"   => "&#xe612;",
                        "href"   => "/clock/list",
                        "spread" => false
                    ]
                ]
            ],
            [
                "title"    => "账户管理",
                "icon"     => "icon-icon10",
                "href"     => "",
                "spread"   => false,
                "children" => [
                    [
                        "title"  => "账户列表",
                        "icon"   => "&#xe612;",
                        "href"   => "/admin/list",
                        "spread" => false
                    ]
                ]
            ]
        ];

        echo json_encode($menu);
    }
}