<?php
$url = "https://scamshield-luez.onrender.com/";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$info = curl_getinfo($ch);
curl_close($ch);

echo "<h1>API Test Result</h1>";
echo "<p>URL: $url</p>";
echo "<p>HTTP Code: " . $info['http_code'] . "</p>";
echo "<p>Response Type: " . $info['content_type'] . "</p>";
echo "<pre>Raw Response:\n" . htmlspecialchars($response) . "</pre>";
?>
