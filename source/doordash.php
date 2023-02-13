<?php
// Credentials provided from https://developer.doordash.com/portal/integration/drive/credentials
$developer_id = 'UPDATE_WITH_DEVELOPER_ID'; // TODO: Update value with Developer ID
$key_id = 'UPDATE_WITH_KEY_ID'; // TODO: Update value with Key ID
$signing_secret = 'UPDATE_WITH_SIGNING_SECRET'; // TODO: Update value with Signing Secret

function base64UrlEncode(string $data): string
{
    // Base64URL format string by:
    //   * replacing all pluses with dashes
    //   * replacing all slashes with underscores
    $base64Url = strtr(base64_encode($data), '+/', '-_');

    // Return string with all equal characters (padding) removed
    return rtrim($base64Url, '=');
}

function base64UrlDecode(string $base64Url): string
{
    // return decoded string by:
    //   * replacing all dashes with pluses
    //   * replacing all underscores with slashes
    return base64_decode(strtr($base64Url, '-_', '+/'));
}

// create JWT header part, including DoorDash version header
$header = json_encode([
    'alg' => 'HS256',
    'typ' => 'JWT',
    'dd-ver' => 'DD-JWT-V1'
]);

// create JWT Payload part, set expiration to 30 minutes (1800 seconds) from current time
$payload = json_encode([
    'aud' => 'doordash',
    'iss' => $developer_id,
    'kid' => $key_id,
    'exp' => time() + 1800,
    'iat' => time()
]);

// Base64URL Encode JWT Header Part
$base64UrlHeader = base64UrlEncode($header);

// Base64URL Encode JWT Payload Part
$base64UrlPayload = base64UrlEncode($payload);

// Create Signature
$signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, base64UrlDecode($signing_secret), true);

// Base64URL Encode Signature Part
$base64UrlSignature = base64UrlEncode($signature);

// Build JWT
$jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
?>
<html>
<head>
    <title>DoorDash Drive API JWT PHP Sample</title>
</head>
<body>
    <h2>DoorDash API JWT</h2>
    
    <textarea rows="4" cols="100"><?php echo $jwt; ?></textarea>

    <p><b>Learn more:</b><p>
    <ul>
        <li><a href="https://developer.doordash.com/en-US/docs/drive/tutorials/get_started">Get Started with the DoorDash API</a></li>
        <li><a href="https://github.com/JoshAtDoorDash/DoorDashAPI-PHP-Sample">DoorDash API PHP Sample</a></li>
    </ul>
</body>
</html>
