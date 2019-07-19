<?php

    if (!defined('HS')) {
        die('Tidak boleh diakses langsung.');
    }

    // debuging
    function myPre($value)
    {
        echo '<pre>';
        print_r($value);
        echo '</pre>';
    }

    // request 
    function apiRequest($method, $data)
    {
        if (!is_string($method)) {
            error_log("Nama method harus bertipe string!\n");

            return false;
        }

        if (!$data) {
            $data = [];
        } elseif (!is_array($data)) {
            error_log("Data harus bertipe array\n");

            return false;
        }


        $url = 'https://api.telegram.org/bot'.$GLOBALS['token'].'/'.$method;

        $options = [
            'http' => [
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data),
            ],
        ];
        $context = stream_context_create($options);

        $result = file_get_contents($url, false, $context);

        return $result;
    }

    // get update
    function getApiUpdate($offset)
    {
        $method = 'getUpdates';
        $data['offset'] = $offset;

        $result = apiRequest($method, $data);

        $result = json_decode($result, true);
        if ($result['ok'] == 1) {
            return $result['result'];
        }

        return [];
    }

    // unpin pesan group
    function unpinMessage($chatid)
    {
        $method = 'unpinChatMessage';
        $data = ['chat_id' => $chatid];
        $result = apiRequest($method, $data);
        return json_decode($result);
    }

    // pin message group
    function pinMessage($chatid, $reply_id)
    {
        $method = 'pinChatMessage';
        $data = ['chat_id' => $chatid, 'message_id' => $reply_id];
        $result = apiRequest($method, $data);
        return json_decode($result);
    }

    // info Chat 
    function getChat($chatid)
    {
        $method = 'getChat';
        $data = ['chat_id' => $chatid];
        $result = apiRequest($method, $data);
        return json_decode($result);
    }

    // info member di dalam suatu chat
    function getChatMember($chatid, $userid)
    {
        $method = 'getChatMember';
        $data = ['chat_id' => $chatid, 'user_id' => $userid];
        $result = apiRequest($method, $data);
        return json_decode($result);
    }

    // send menssage
    function sendApiMsg($chatid, $text, $msg_reply_id, $parse_mode = false, $disablepreview = false)
    {
        $method = 'sendMessage';
        $data = ['chat_id' => $chatid, 'text'  => $text];

        if ($msg_reply_id) {
            $data['reply_to_message_id'] = $msg_reply_id;
        }
        if ($parse_mode) {
            $data['parse_mode'] = $parse_mode;
        }
        if ($disablepreview) {
            $data['disable_web_page_preview'] = $disablepreview;
        }

        $result = apiRequest($method, $data);
    }

    // action [ typing, upload_video, etc ]
    function sendApiAction($chatid, $action = 'typing')
    {
        $method = 'sendChatAction';
        $data = [
            'chat_id' => $chatid,
            'action'  => $action,

        ];
        $result = apiRequest($method, $data);
    }

    // keyboard
    function sendApiKeyboard($chatid, $msg_reply_id, $text, $keyboard = [], $inline = false)
    {
        $method = 'sendMessage';
        $replyMarkup = [
            'keyboard'        => $keyboard,
            'resize_keyboard' => true,
        ];

        $data = [
            'chat_id'    => $chatid,
            'text'       => $text,
            'parse_mode' => 'Markdown',
            'reply_to_message_id' => $msg_reply_id,

        ];

        $inline
        ? $data['reply_markup'] = json_encode(['inline_keyboard' => $keyboard])
        : $data['reply_markup'] = json_encode($replyMarkup);

        $result = apiRequest($method, $data);
    }

    // edit message
    function editMessageText($chatid, $message_id, $text, $keyboard = [], $inline = false)
    {
        $method = 'editMessageText';
        $replyMarkup = [
            'keyboard'        => $keyboard,
            'resize_keyboard' => true,
        ];

        $data = [
            'chat_id'    => $chatid,
            'message_id' => $message_id,
            'text'       => $text,
            'parse_mode' => 'Markdown',

        ];

        $inline
        ? $data['reply_markup'] = json_encode(['inline_keyboard' => $keyboard])
        : $data['reply_markup'] = json_encode($replyMarkup);

        $result = apiRequest($method, $data);
    }

    // hide keyboard
    function sendApiHideKeyboard($chatid, $msg_reply_id, $text)
    {
        $method = 'sendMessage';
        $data = [
            'chat_id'       => $chatid,
            'text'          => $text,
            'parse_mode'    => 'Markdown',
            'reply_markup'  => json_encode(['hide_keyboard' => true]),
            'reply_to_message_id' => $msg_reply_id,

        ];

        $result = apiRequest($method, $data);
    }

    // kirim animasi
    function sendApiAnimation($chatid, $file_id, $caption, $msg_reply_id)
    {
        $method = 'sendAnimation';
        $data = [
            'chat_id' => $chatid,
            'animation'  => $file_id,
            'caption' => $caption,
            'reply_to_message_id' => $msg_reply_id,
        ];

        if ($msg_reply_id) {
            $data['reply_to_message_id'] = $msg_reply_id;
        }

        $result = apiRequest($method, $data);
    }

    // kirim video
    function sendApiVideo($chatid, $file_id, $caption, $msg_reply_id)
    {
        $method = 'sendVideo';
        $data = [
            'chat_id' => $chatid,
            'video'  => $file_id,
            'caption' => $caption,
            'reply_to_message_id' => $msg_reply_id,
        ];

        if ($msg_reply_id) {
            $data['reply_to_message_id'] = $msg_reply_id;
        }

        $result = apiRequest($method, $data);
    }

    // kirim dokumen
    function sendApiDocument($chatid, $file_id, $caption, $msg_reply_id)
    {
        $method = 'sendDocument';
        $data = [
            'chat_id' => $chatid,
            'document'  => $file_id,
            'caption' => $caption,
            'reply_to_message_id' => $msg_reply_id,
        ];

        if ($msg_reply_id) {
            $data['reply_to_message_id'] = $msg_reply_id;
        }

        $result = apiRequest($method, $data);
    }

    // kirim audio
    function sendApiAudio($chatid, $file_id, $caption, $msg_reply_id) 
    {
        $method = 'sendAudio';
        $data = [
            'chat_id' => $chatid,
            'audio'  => $file_id,
            'caption' => $caption,
            'reply_to_message_id' => $msg_reply_id,
        ];

        if ($msg_reply_id) {
            $data['reply_to_message_id'] = $msg_reply_id;
        }

        $result = apiRequest($method, $data);
    }

    // kirim photo
    function sendApiPhoto($chatid, $file_id, $caption, $msg_reply_id)
    {
        $method = 'sendPhoto';
        $data = [
            'chat_id' => $chatid,
            'photo'  => $file_id,
            'caption' => $caption,
            'reply_to_message_id' => $msg_reply_id,
        ];

        if ($msg_reply_id) {
            $data['reply_to_message_id'] = $msg_reply_id;
        }

        $result = apiRequest($method, $data);
    }

    // kirim sticker
    function sendApiSticker($chatid, $sticker, $msg_reply_id)
    {
        $method = 'sendSticker';
        $data = [
            'chat_id'  => $chatid,
            'sticker'  => $sticker,
        ];

        if ($msg_reply_id) {
            $data['reply_to_message_id'] = $msg_reply_id;
        }

        $result = apiRequest($method, $data);
    }
