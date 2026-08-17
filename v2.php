<?php
// CORS হেডার
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// 🔒 বটের ক্রেডেনশিয়াল
$bot_id = "2890129";
$public_token = "cfab0a2692a9ba1b3712a6af4e031ad6";
$command = "child_handler";

// GET এবং POST উভয় ডাটা সংগ্রহ
$params = $_REQUEST;

// যদি কোনো প্যানেল raw JSON বডি পাঠায়
$json_input = file_get_contents('php://input');
if (!empty($json_input)) {
    $decoded_json = json_decode($json_input, true);
    if (is_array($decoded_json)) {
        $params = array_merge($params, $decoded_json);
    }
}

// গোপন প্যারামিটার যুক্ত করা
$params['command'] = $command;
$params['public_user_token'] = $public_token;

// Bots.Business API URL তৈরি
$query_string = http_build_query($params);
$target_url = "https://api.bots.business/v1/bots/" . $bot_id . "/new-webhook?" . $query_string;

// cURL রিকোয়েস্ট পাঠানো
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $target_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

if ($curl_error) {
    echo json_encode(["error" => "Gateway Connection Error: " . $curl_error]);
    exit();
}

// রেসপন্স প্রিন্ট
http_response_code($http_code ? $http_code : 200);
echo $response;
?>