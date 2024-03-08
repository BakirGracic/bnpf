<?php

function notifyErrorTelegram($msg) {
    $url = "https://api.telegram.org/bot".TELEGRAM_BOT_TOKEN."/sendMessage?chat_id=".TELEGRAM_CHAT_ID."&text=" . urlencode($msg);
    
    try {
        file_get_contents($url);
    } catch (\Throwable $th) {
        // fvck it!
    }
}
