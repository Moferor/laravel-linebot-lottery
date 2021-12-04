# laravel-linebot-lottery

[![packagist](https://img.shields.io/packagist/v/jose13/larave-linebot-lottery?color=orange&include_prereleases)](https://packagist.org/packages/jose13/larave-linebot-lottery)
[![WSL](https://img.shields.io/badge/platform-windows%20%7C%20linux-blue)](https://packagist.org/packages/jose13/larave-linebot-lottery)
[![laravel](https://img.shields.io/badge/laravel-%5E8.0-green)](https://packagist.org/packages/jose13/larave-linebot-lottery)
[![php Version](https://img.shields.io/packagist/php-v/jose13/larave-linebot-lottery)](https://packagist.org/packages/jose13/larave-linebot-lottery)
[![Total Downloads](https://img.shields.io/packagist/dt/jose13/larave-linebot-lottery?color=blue)](https://packagist.org/packages/jose13/larave-linebot-lottery)
[![License](https://img.shields.io/packagist/l/jose13/larave-linebot-lottery)](https://packagist.org/packages/jose13/larave-linebot-lottery)

說明

藉由 Line bot 及爬蟲應用，可於 Line 中獲取樂透號碼(最新球號或指定日期球號)，以及基本可自定義的對話回應功能。

## 準備

### 一個 HTTPS 網址

本人是使用 [ngrok](https://ngrok.com/) 作為臨時的測試網址使用。

### 註冊及設定一個 Messaging API 頻道

前往 [Line Developers](https://developers.line.biz/console/) 建立一個新的 Messaging API
頻道，可以參考 [官方教學](https://developers.line.biz/zh-hant/docs/messaging-api/getting-started/) 。
> 創建完成後進入頻道頁面

<img src="https://user-images.githubusercontent.com/16284391/135816978-0e7d351f-df9a-4874-b136-8c27b8a2456a.png" style="width:60%">

記下頻道頁面中 `Basic setting` 選項裡，`Channel secret` 產生的數值。

記下頻道頁面中 `Messaging API` 選項裡，`Channel access token` 產生的數值。

頻道頁面中 `Messaging API` 選項裡，設定 Webhook :


<img src="https://user-images.githubusercontent.com/16284391/135817170-e5c9fe14-c806-4208-850f-cbfb0498022b.png" style="width:60%">

將 Line 回應設定成下圖 :

<img src="https://user-images.githubusercontent.com/16284391/135817242-436a5d10-5cb7-4279-b25e-8667707e8c07.png" style="width:60%">

### 創建一個 Laravel 專案

開啟`.env` 設定記下的`Channel secret` 及 `Channel access token` ：

```
LINE_BOT_CHANNEL_ACCESS_TOKEN=024IcFhCPF.....
LINE_BOT_CHANNEL_SECRET=cd2a0b42a7835.....
```

> 注意：`LINE_BOT_CHANNEL_ACCESS_TOKEN` 及 `LINE_BOT_CHANNEL_SECRET`  名稱需一致不得更改

## 安裝

使用 Composer 安裝套件：

```bash
composer require jose13/larave-linebot-lottery
```

發布設定檔案(可選擇不發布，將以預設檔呈現功能項目)：

```bash
php artisan vendor:publish --provider=Jose13\\LaravelLineBotLottery\\LinebotServiceProvider
```

## 開始使用

### 立即使用

> 如果選擇不發布設定檔，於安裝步驟結束後即可將自己創建的 `Line bot` 加入 Line 好友開始使用。  
> ( *使用過程中也可發布設定檔進行自定義修改* )

### 自定義設定檔後開始使用

> 發布設定檔案後，至 `config/LineBotServiceConfig.php` 進行自定義調整，完成後即可將自己創建的 `Message Api Line bot` 加入好友開始使用。

自定義各聊天室裡(*User(個人)*、*Group(群組)*、*Room(群聊)*) messageType是否開啟服務。

*`true`為開啟 `false`為關閉功能*
> 注意：
> * 目前套件僅支援 `TEXT`文字訊息自動回應功能。其餘功能設為`true`不會有任何作用
> * 關閉`TEXT` 文字訊息服務，等同於不會開啟`QuickReplyButton`功能按鈕

```php
 'MessageTypeSupportChat' =>
        [
            'UserChats' =>
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
            'GroupChats' =>
                [
                    ....
                ],
            'RoomChats' =>
                [
                    ....
                ]
        ]
```

自定義各聊天室裡，訊息自動回應內容

* `'QuickReplyButtonName'` : 開啟`QuickReplyButton` 按鈕關鍵字，可自定義。
* `'Available'` : 各房間類型啟用或關閉`QuickReplyButton`按鈕功能，`false`關閉狀態下，Line 輸入 `QuickReplyButtonName`關鍵字，將不會有任何反應 。
* `'ResponseContent'` : 自定義各房間類型的文字回復。

```php
    'TextResponse' =>
        [
            'QuickReplyButtonName' => '開啟服務',
            'Available' =>
                [
                    'UserChats' => true,
                    'GroupChats' => true,
                    'RoomChats' => true,
                ],
            'ResponseContent' =>
                [
                    'UserChats' =>
                        [
                            'hello' => 'hi',
                            '吃飽沒' => '吃了一公斤的鐵了!'
                        ],
                    'GroupChats' =>
                        [
                            'hello' => '你好阿',
                            '吃飽沒' => '我吃了10%的用電量了'
                        ],
                    'RoomChats' =>
                        [
                            'hello' => 'YO',
                            '吃飽沒' => '我是機器人不會餓!'
                        ],
                ]
        ],
```

選擇各聊天室開放的按鈕功能
> 目前支援的功能按鈕清單：
> * `最新威力彩`
> * `最新大樂透`
> * `最新539`
> * `最新三種遊戲`
> * `近3期三種遊戲`
> * `近5期威力彩`
> * `近5期大樂透`
> * `近5期539`
> * `近10期三種遊戲`
> * `指定威力彩日期`
> * `指定大樂透日期`
> * `指定539日期`

*啟用功能設為 `true` ，關閉設為為 `false`*

```php
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
                     ......
                ],
            'RoomChats' =>
                [
                     ......
                ]
        ]
```

## Demo

歡迎訊息、文字回應、QuickReply按鈕功能開啟

https://user-images.githubusercontent.com/16284391/135817631-0bac0616-23cf-407e-b2ac-7b9cfe357508.mp4

一般請求資訊服務按鈕

https://user-images.githubusercontent.com/16284391/135817645-71f30e3b-5cd9-478b-9dcc-37a418b99efe.mp4


指定日期請求服務按鈕

https://user-images.githubusercontent.com/16284391/135817661-18059d7d-ddfe-4676-b987-a65e43a07eeb.mp4


