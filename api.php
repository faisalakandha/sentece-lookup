<?php
function callMeaningCloudAPI($req) {

$parameters = $req->get_params();
// return $parameters['my_nonce'];
 if (wp_verify_nonce( $parameters['my_nonce'], 'kamranfaisal' ) ) {

    $apiKey = "6656b2a36412e218859c9a20a46fedc0";
    $url = "https://api.meaningcloud.com/summarization-1.0";

    $postData = array(
        'key' => $apiKey,
        'txt' => $parameters['txt'],
        'sentences' => $parameters['sentences']
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response);
 }
   return json_encode( 'Warning !! You are not authorized !' );
}


add_action('rest_api_init', function () {

    register_rest_route('sentencesummary/v1', 'summary', array(
        'methods' => 'POST',
        'callback' => 'callMeaningCloudAPI',
        'args' => array(),
        'permission_callback' => function ($request) {
            // Perform rate limiting check
            $max_requests = 5; // Maximum number of requests allowed
            $time_frame = 60; // Timeframe in seconds (e.g., 60 seconds = 1 minute)

            $user_ip = $_SERVER['REMOTE_ADDR'];
            $requests_made = get_user_meta($user_ip, 'api_requests_made', true);
            $last_request_time = get_user_meta($user_ip, 'api_last_request_time', true);

            // Check if the user has exceeded the maximum requests
            if ($requests_made >= $max_requests) {
                return false;
            }

            // Check if the user has made a request within the specified timeframe
            if (time() - $last_request_time < $time_frame) {
                return false;
            }

            // Increment the request count and update the last request time
            update_user_meta($user_ip, 'api_requests_made', $requests_made + 1);
            update_user_meta($user_ip, 'api_last_request_time', time());

            return true;
        },
    ));
});

?>
