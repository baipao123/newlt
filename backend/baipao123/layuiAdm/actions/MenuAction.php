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
                "icon"     => "my-icon-slider",
                "href"     => "",
                "spread"   => false,
                "children" => [
                    [
                        "title"  => "轮播列表",
                        "icon"   => "my-icon-long-arrow-right",
                        "href"   => "/slider/list",
                        "spread" => false
                    ]
                ]
            ],
            [
                "title"    => "题库管理",
                "icon"     => "my-icon-questions",
                "href"     => "",
                "spread"   => true,
                "children" => [
                    [
                        "title"  => "分类列表",
                        "icon"   => "my-icon-list-light",
                        "href"   => "/question/types",
                        "spread" => false
                    ],
                    [
                        "title"  => "价格管理",
                        "icon"   => "my-icon-price",
                        "href"   => "/question/prices",
                        "spread" => false
                    ],
                    [
                        "title"  => "题目列表",
                        "icon"   => "my-icon-question-list",
                        "href"   => "/question/list",
                        "spread" => false
                    ],
                ]
            ],
            [
                "title"    => "订单管理",
                "icon"     => "my-icon-orders",
                "href"     => "",
                "spread"   => true,
                "children" => [
                    [
                        "title"  => "订单列表",
                        "icon"   => "my-icon-long-arrow-right",
                        "href"   => "/order/list",
                        "spread" => false
                    ],
                    [
                        "title"  => "收入统计",
                        "icon"   => "my-icon-chart-2",
                        "href"   => "/order/count",
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
                        "title"  => "模考记录",
                        "icon"   => "my-icon-exam",
                        "href"   => "/user/job-list",
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
                        "icon"   => "my-icon-long-arrow-right",
                        "href"   => "/admin/list",
                        "spread" => false
                    ]
                ]
            ]
        ];

        echo json_encode($menu);
    }
}