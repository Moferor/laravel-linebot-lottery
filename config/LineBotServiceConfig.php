<?php

use LINE\LINEBot\Constant\MessageType;

/*
 * 預設開啟quickReply按鈕的關鍵字
 */
$openQuickButtonText = '開啟服務';

return [

    /*
 |------------------------------------------------------------------------------------------------------
 | 路由
 |------------------------------------------------------------------------------------------------------
 | 各支援的event、Factory以及event子服務路由
 |
*/

    'ClassRoute' => [

        'message' =>
            [
                'Event' => 'Jose13\LaravelLineBotLottery\Services\LineSupportEvents\EventsType\TheMessageEvent',
                'Factory' => 'Jose13\LaravelLineBotLottery\Services\Factory\MessageEventTypeFactory',
                //messageTpe Response class Route key名稱 參照 line-bot-sdk  LINE\LINEBot\Constant\MessageType text location..
                //創建class時 字首大寫+Response
                'text' => 'Jose13\LaravelLineBotLottery\Services\LineSupportEvents\EventsType\MessageEventType\TextResponse'
            ],
        'postback' =>
            [
                'Event' => 'Jose13\LaravelLineBotLottery\Services\LineSupportEvents\EventsType\ThePostbackEvent',
                'Factory' => 'Jose13\LaravelLineBotLottery\Services\Factory\PostbackTypeWithGameFactory',
                //postback action Response class Route
                'DateTimePickQuickAction' => 'Jose13\LaravelLineBotLottery\Services\LineSupportEvents\EventsType\PostbackActionType\DateTimePickQuickAction',
                'NormalQuickAction' => 'Jose13\LaravelLineBotLottery\Services\LineSupportEvents\EventsType\PostbackActionType\NormalQuickAction',
            ],
        'follow' =>
            [
                'Event' => 'Jose13\LaravelLineBotLottery\Services\LineSupportEvents\EventsType\TheFollowEvent',
            ],
        'join' =>
            [
                'Event' => 'Jose13\LaravelLineBotLottery\Services\LineSupportEvents\EventsType\TheJoinEvent',
            ],
        'TaiwanLottery' =>
            [
                'SuperLottery' => 'Jose13\LaravelLineBotLottery\Services\CrawlersService\TaiwanLottery\SuperLottery',
                'BigLottery' => 'Jose13\LaravelLineBotLottery\Services\CrawlersService\TaiwanLottery\BigLottery',
                'FiveThreeNine' => 'Jose13\LaravelLineBotLottery\Services\CrawlersService\TaiwanLottery\FiveThreeNine',
            ],
        'Template' =>
            [
                'Factory' => 'Jose13\LaravelLineBotLottery\Services\Factory\TemplateTypeFactory',
                'FullBubble' => 'Jose13\LaravelLineBotLottery\Services\LineTemplateService\TemplateType\FullBubble',
                'OnlyBallBubble' => 'Jose13\LaravelLineBotLottery\Services\LineTemplateService\TemplateType\OnlyBallBubble'
            ]
    ],


    /*
     |----------------------------------------------------------------------------------
     | 設定 各MessageType 在不同聊天室裡是否開啟支援
     |----------------------------------------------------------------------------------
     |
     | 目前已將 MessageApi 所有預設支援的種類都代入每個聊天種類，參照 line-bot-sdk  LINE\LINEBot\Constant\MessageType
     | 如果要在該聊天室裡啟用請設定為true，如不啟用請設定為false
     | 目前預設建立的僅處理TEXT Services\LineSupportEvents\MessageEventType\TextResponse;
     | 如果想建立其他支援的type，除了將其改為true之外，請在上方同樣目錄下 建立開頭大寫後續+Response的類別， 如 LocationResponse
     | 否則 Services\LineBotService\MessageTypeExitsHandle 依然會判定該類別不存在而不進行處理
     |
     */
    'MessageTypeSupportChat' =>
        [
            'UserChats' =>
                [
                    MessageType::TEXT => true, //true or false
                    MessageType::TEMPLATE => false,
                    MessageType::IMAGEMAP => false,
                    MessageType::STICKER => false,
                    MessageType::LOCATION => false,
                    MessageType::IMAGE => false,
                    MessageType::AUDIO => false,
                    MessageType::VIDEO => false,
                    MessageType::FLEX => false,
                ],
            'GroupChats' =>
                [
                    MessageType::TEXT => true,
                    MessageType::TEMPLATE => false,
                    MessageType::IMAGEMAP => false,
                    MessageType::STICKER => false,
                    MessageType::LOCATION => false,
                    MessageType::IMAGE => false,
                    MessageType::AUDIO => false,
                    MessageType::VIDEO => false,
                    MessageType::FLEX => false,
                ],
            'RoomChats' =>
                [
                    MessageType::TEXT => true,
                    MessageType::TEMPLATE => false,
                    MessageType::IMAGEMAP => false,
                    MessageType::STICKER => false,
                    MessageType::LOCATION => false,
                    MessageType::IMAGE => false,
                    MessageType::AUDIO => false,
                    MessageType::VIDEO => false,
                    MessageType::FLEX => false,
                ]
        ],


    /*
     |------------------------------------------------------------------------------------------------------
     | 設定對應的文字回應
     |------------------------------------------------------------------------------------------------------
     | 各房間回應訊息內容
     | 不想啟用開啟quickReply按鈕 $openQuickButtonText 設定為false
     |
    */

    'TextResponse' => [

        // 給LineEventsService\MessageEventType\TextResponse 確認輸入的文字是否為預設開啟quickReply按鈕的關鍵字用的;
        'quickReplyButton' => $openQuickButtonText,


        'UserChats' =>
            [
                $openQuickButtonText => true, //bool true/false || false = 關掉快速回應按鈕服務
                'hello' => 'hi',
                '你幾歲' => '10歲'

            ],
        'GroupChats' =>
            [
                $openQuickButtonText => true,
                'hello' => '你好阿',
                '你幾歲' => '秘密哦'
            ],
        'RoomChats' =>
            [
                $openQuickButtonText => true,
                'hello' => 'YO',
                '你幾歲' => '永遠18歲'
            ]
    ],


    /*
     |------------------------------------------------------------------------------------------------------
     | 設定各個聊天室支援的 quickReply 按鈕
     |------------------------------------------------------------------------------------------------------
     | 目前此套件支援的所有服務按鈕名稱如下 :
     | - 最新威力彩
     | - 最新大樂透
     | - 最新539
     | - 最新三種遊戲
     | - 近3期三種遊戲
     | - 近5期威力彩
     | - 近5期大樂透
     | - 近5期539
     | - 近10期三種遊戲
     | - 指定威力彩日期
     | - 指定大樂透日期
     | - 指定539日期
     | 如果要房間內要支援功能按鈕的話，請在房間內加入功能名稱及設定為true
     | 不想支援的話請設定為 false
    */

    'QuickReply' =>
        [
            'UserChats' =>
                [
                    '最新威力彩' => true,//true or false
                    '最新大樂透' => true,
                    '最新539' => true,
                    '最新三種遊戲' => true,
                    '近3期三種遊戲' => true,
                    '近5期威力彩' => true,
                    '近5期大樂透' => true,
                    '近5期539' => true,
                    '近10期三種遊戲(純球號)' => true,
                    '指定威力彩日期' => true,
                    '指定大樂透日期' => true,
                    '指定539日期' => true,
                ],
            'GroupChats' =>
                [
                    '最新威力彩' => true,
                    '最新大樂透' => true,
                    '最新539' => true,
                    '最新三種遊戲' => true,
                    '近3期三種遊戲' => true,
                    '近5期威力彩' => true,
                    '近5期大樂透' => true,
                    '近5期539' => true,
                    '近10期三種遊戲(純球號)' => true,

                    '指定威力彩日期' => false,
                    '指定大樂透日期' => false,
                    '指定539日期' => false,

                ],
            'RoomChats' =>
                [

                    '最新威力彩' => true,
                    '最新大樂透' => true,
                    '最新539' => true,
                    '最新三種遊戲' => true,

                    '近3期三種遊戲' => false,
                    '近5期威力彩' => false,
                    '近5期大樂透' => false,
                    '近5期539' => false,
                    '近10期三種遊戲(純球號)' => false,
                    '指定威力彩日期' => false,
                    '指定大樂透日期' => false,
                    '指定539日期' => false,
                ],

        ],


    /*
     |------------------------------------------------------------------------------------------------------
     | 預設輸出提示內文
     |------------------------------------------------------------------------------------------------------
     | - onlyUsePhone => 第一次啟用 quickReply時，提示僅限手機使用
     | - TheDateNotDraw => 使用指定日期獲得球號按鈕時，提示選取的日期並無開獎,且附上支援遊戲正確開獎時間
     | - DrawTime => joinEvent 或是 followEvent 附代的訊息
     |
    */

    'DefaultTipText' =>
        [
            'OnlyUsePhone' => '請使用手機選擇下方服務按鈕' . PHP_EOL . '(電腦版無支援快速回復按鈕)',

            'GameDrewTime' => '遊戲獎項開獎時間如下:' . PHP_EOL . '威力彩:禮拜一、禮拜四' . PHP_EOL . '大樂透:禮拜二、禮拜五' . PHP_EOL . '今彩539:禮拜一 ~ 禮拜六',

            'WelcomeTipMessage' =>
                '歡迎使使用LotteryMan!' . PHP_EOL .
                '根據聊天室種類的不同，' . PHP_EOL .
                '支援的按鈕功能及文字訊息回覆，' . PHP_EOL .
                '將依照管理員設置而有所不同，' . PHP_EOL .
                'LotteryMan加入時將會提示。' . PHP_EOL
        ]


];

