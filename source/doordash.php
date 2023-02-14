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

// Unique ID that will be used for new delivery request
$unique_id = uniqid(); // TODO: Replace with generated system ID

// TODO: Update example data with actual delivery details
// Create Delivery API Reference: https://developer.doordash.com/en-US/api/drive#tag/Delivery/operation/CreateDelivery
$request_body = json_encode([
    "external_delivery_id" => $unique_id,
    "pickup_address"=> "901 Market Street 6th Floor San Francisco, CA 94103",
    "pickup_business_name"=> "Wells Fargo SF Downtown",
    "pickup_phone_number"=> "+16505555555",
    "pickup_instructions"=> "Enter gate code 1234 on the callbox.",
    "dropoff_address"=> "901 Market Street 6th Floor San Francisco, CA 94103",
    "dropoff_business_name"=> "Wells Fargo SF Downtown",
    "dropoff_phone_number"=> "+16505555555",
    "dropoff_instructions"=> "Enter gate code 1234 on the callbox.",
    "order_value"=> 1999
  ]);
  
  // Build Headers for API Request
  $headers = array(
    "Content-type: application/json",
    "Authorization: Bearer ".$jwt
  );
  
  // Make API Request
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, "https://openapi.doordash.com/drive/v2/deliveries/");
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $request_body);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $result = curl_exec($ch);
?>
<html>
<head>
    <title>DoorDash Drive API JWT PHP Sample</title>
</head>
<body>
    <h2>DoorDash API JWT</h2>
    
    <div style="width: 400px; overflow-wrap: break-word; border: 1px solid black; padding: 10px"><?php echo $jwt; ?></div>

    <h2>Create Test Order Result</h2>

    <pre><code><?php echo "<script>document.write(JSON.stringify(".$result.", null, 4))</script>"; ?></code></pre>
    
    <h2>Learn more</h2>
    <ul>
        <li><a href="https://developer.doordash.com/en-US/docs/drive/tutorials/get_started">Get Started with the DoorDash API</a></li>
        <li><a href="https://github.com/JoshAtDoorDash/DoorDashAPI-PHP-Sample">DoorDash API PHP Sample</a></li>
    </ul>

</body>
</html>
