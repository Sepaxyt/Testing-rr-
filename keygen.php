<?php
header('Content-Type: application/json');
$secret = "KAAL_2024_SECURE_KEY"; // Must match C++ code

if ($_GET['secret'] !== $secret) {
    die(json_encode(["error" => "Unauthorized"]));
}

function generateLicenseKey() {
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $key = '';
    for ($i = 0; $i < 20; $i++) {
        if ($i > 0 && $i % 5 == 0) $key .= '-';
        $key .= $chars[rand(0, strlen($chars) - 1)];
    }
    return $key;
}

$licenseKey = generateLicenseKey();
$expiryDate = date('Y-m-d', strtotime('+30 days'));

$keys = json_decode(file_get_contents('keys.json'), true) ?: [];
$keys[$licenseKey] = [
    'created' => date('Y-m-d H:i:s'),
    'expiry' => $expiryDate,
    'hwid' => null,
    'active' => true,
    'tier' => 'VIP'
];

file_put_contents('keys.json', json_encode($keys, JSON_PRETTY_PRINT));
echo json_encode([
    'license_key' => $licenseKey,
    'expiry_date' => $expiryDate
]);
?>
