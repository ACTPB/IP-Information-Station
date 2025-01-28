<?php
header('Content-Type: application/json');

$apiKey = getenv('eacbd8d3be6308');

function get_ip_info($ip) {
    global $apiKey;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://ipinfo.io/{$ip}?token={$apiKey}");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $ip = filter_var($data['ip'] ?? null, FILTER_VALIDATE_IP);

    if (!$ip) {
        echo json_encode(["error" => "Invalid IP address provided"]);
        http_response_code(400);
    } else {
        $info = get_ip_info($ip);

        if ($info && isset($info['error'])) {
            echo json_encode(["error" => $info['error']]);
            http_response_code(500);
        } else if ($info) {
            // Save the full info to a file in the IPK directory
            $filename = "IPK/" . $ip . ".json";
            if (!file_put_contents($filename, json_encode($info))) {
                echo json_encode(["error" => "Failed to write file"]);
                http_response_code(500);
            } else {
                // Send only the IP back to the frontend
                echo json_encode(["ip" => $ip]);
                http_response_code(200);
            }
        } else {
            echo json_encode(["error" => "Failed to retrieve IP information"]);
            http_response_code(500);
        }
    }
} else {
    echo json_encode(["error" => "Invalid request method"]);
    http_response_code(405);
}
?>
