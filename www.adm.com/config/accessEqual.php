<?php
/**
 * Created by PhpStorm.
 * 本文件是记录一些操作的同样权限  比如   user_edit   这个操作 是编辑，然后 提交的时候 写了另外一个方法 叫 user_editsubmit 则  user_edit 和  user_editsubmit 应该是同一个权限，就不用再后台
 * 在录入2个菜单了  就要录入一个菜单就可以了 ，当然也可以不在这里配置，直接在后台多配置一个菜单，这样 一个编辑权限就要选择2个菜单权限了有点麻烦 哈。  看个人需求 在后台配置要更加的灵活
 * 就是配置起来繁琐 这里写就比较简单 后台配置起来
 * User: huangweigang
 * Date: 2018/7/20 0020
 * Time: 上午 10:42
 */
return [
    'group_editGroup' => [ //编辑用户组 是同一个权限
        'group_submit',
    ],
    'user_password' => [//修改密码 然后执行修改密码
        'user_ajaxUpdateUserPass'
    ],
    'group_addUser' => [//添加管理员
        'group_submitUser',
    ],
    //主播认证 修改用户信息
    'member_edit' => [
        'member_ajaxEdit',
    ],
    //提现管理做成一个权限
    'exchange_index' => [
        'exchange_getPayType',
    ],
    //添加渠道
    'channel_add' => [
        'channel_ajaxAdd',
    ],
    //修改渠道
    'channel_edit' => [
        'channel_ajaxEdit'
    ],
    //新增公告
    'channel_addadvertise' => [
        'channel_safeadvertise'
    ],
    //修改落地页
    'channel_landbindinfo' => [
        'channel_landbindUpd'
    ],
    //渠道提现审核
    'channel_shenhe' => [
        'channel_batchshenhe'
    ],
    'channel_pay'   => [
        'channel_batchpay'
    ],
    //添加金币
    'gold_add' => [
        'gold_ajaxAdd',
    ],
    'gold_edit' => [
        'gold_ajaxEdit'
    ],
    //金币管理 产品标题设置
    'goldproductmap_index' => [
        'goldproductmap_edit',
        'goldproductmap_add',
        'goldproductmap_delete',
    ],
    //禁止ip注册
    'seal_ip' => [
        'seal_addSealIP',
        'seal_delSealIP',
    ],
    //清除支付宝
    'seal_alipay' => [
        'seal_ajaxAlipay',
        'seal_ajaxEditAlipay',
    ],
    //金币不足
    'seal_gold' => [
        'seal_ajaxGold',
    ],
    //用户详细信息
    'seal_phone' => [
        'seal_ajaxPhone',
    ],
    //邀请绑定
    'seal_invite' => [
        'seal_ajaxInvite'
    ],
    //补积分
    'seal_point' => [
        'seal_ajaxPoint',
    ],
    //更改星级
    'seal_star' => [
        'seal_ajaxStar',
    ],
    //清除账号
    'seal_account' => [
        'seal_ajaxAccount'
    ],
    'seal_accountcache' => [
        'seal_ajaxAccountCache'
    ],
    //修改性别
    'seal_gender' => [
        'seal_ajaxGender',
    ],
    //更改用户操作时间
    'seal_visit' => [
        'seal_ajaxVisit',
    ],
    'seal_commission' => [
        'seal_addSealCommission',
        'seal_delSealCommission',
        'seal_updateSealCommission',
    ],
    'seal_commission2' => [
        'seal_addSealCommission',
        'seal_delSealCommission',
        'seal_updateSealCommission',
    ],
    'seal_commission3' => [
        'seal_addSealCommission',
        'seal_delSealCommission',
        'seal_updateSealCommission',
    ],
    'seal_deduction' => [
        'seal_delSealDeduction',
        'seal_addSealDeduction',
        'seal_updateSealDeduction',
    ],
    'seal_sensitive' => [],
    'seal_message' => [
        'addSealMessage',
        'delSealMessage'
    ],
    'keymessage_keywords' => [
        'keymessage_ajaxAddKeyword',
        'keymessage_ajaxUpdateKeyword',
        'keymessage_ajaxDelKeyword',
    ],
    'config_beauty' => [
        'config_ajaxAddPhone',
        'config_ajaxUpdPhone',
    ],
    'banner_add' => [
        'banner_updatephoto',
        'banner_ajaxStatus',
        'banner_edit',
        'banner_ajaxEdit',
    ],

    'family_index' => [
        'family_update',
        'family_user',
        'family_member',
        'family_useredit',
        'family_addfamilyuser',
        'family_delfamilyuser',
        'family_reward',
        'family_giveout',
        'family_ajaxAddUsers',
        'family_getReward'
    ],
    'weeklystar_index' => [
        'weeklystar_ajaxAdd',
        'weeklystar_ajaxDel',
        'weeklystar_ajaxCache'
    ],
    'message_image' => [
        'message_ajaxStatus2'
    ],
    'message_text' => [
        'message_ajaxStatus',
        'message_keywords',
        'message_ajaxAddKeyword',
        'message_ajaxDelKeyword',
        'message_ajaxUpdateKeyword',
    ]
];