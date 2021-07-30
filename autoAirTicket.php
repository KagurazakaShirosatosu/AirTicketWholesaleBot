<?php

function main_handler($event, $context) {
    $requestBody = json_decode($event->body);
    if(isset($requestBody->message->new_chat_member)) {
        $chat = $requestBody->message->chat->id;
        $memberID = $requestBody->message->new_chat_member->id;
        return integratedResponse(200, kick($chat, $memberID));
    }
    else {
        return integratedResponse(200, 'nothing to do.');
    }

}

function kick($chat, $member) {
    $data = array(
        "chat_id" => $chat,
        "user_id" => $member,
        "until_date" => time() + 60,
    );
    $data_string = json_encode($data);
    $ch = curl_init('https://api.telegram.org/bot' . getenv('token') . '/banChatMember');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data_string)
    ));
    return curl_exec($ch);
    curl_close($ch);

}

function integratedResponse($httpStatusCode, $body) {
    $dataToReturn = [
        'isBase64Encoded' => false,
        'statusCode' => $httpStatusCode,
        'headers' => [
            'Content-Type' => 'application/json',
        ],
        'body' => $body
    ];
    return $dataToReturn;
}

?>
