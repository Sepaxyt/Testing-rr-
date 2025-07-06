<?php
header('Content-Type: application/json');
$secret = "KAAL_2024_SECURE_KEY"; // Must match C++ code

$input = json_decode(file_get_contents('php://input'), true);
if ($input['secret'] !== $secret) {
    die(json_encode(["error" => "Unauthorized"]));
}

$keys = json_decode(file_get_contents('keys.json'), true) ?: [];
$response = [
    'success' => false,
    'expired' => false
];

if (isset($keys[$input['license_key']])) {
    $license = $keys[$input['license_key']];
    
    if ($license['active']) {
        if (empty($license['hwid'])) {
            // First activation
            $keys[$input['license_key']]['hwid'] = $input['hwid'];
            $response['success'] = true;
            $response['expiry_date'] = $license['expiry'];
            $response['tier'] = $license['tier'];
        } else {
            // Check HWID match
            $response['success'] = ($license['hwid'] === $input['hwid']);
            $response['expiry_date'] = $license['expiry'];
            $response['tier'] = $license['tier'];
        }
        
        $response['expired'] = (strtotime($license['expiry']) < time());
        file_put_contents('keys.json', json_encode($keys, JSON_PRETTY_PRINT));
    }
}

echo json_encode($response);
?>
