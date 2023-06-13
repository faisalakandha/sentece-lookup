<?php
function callMeaningCloudAPI($text) {
    $apiKey = "6656b2a36412e218859c9a20a46fedc0";
    $url = "https://api.meaningcloud.com/summarization-1.0";

    $postData = array(
        'key' => $apiKey,
        'txt' => $text
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
}
?>
