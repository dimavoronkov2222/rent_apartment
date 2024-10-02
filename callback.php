<?php
require_once 'vendor/autoload.php';
$client = new Google_Client();
$client->setClientId('22409082771-vv1pbt1u7r71kmub6aubr2lv2uq13vl1.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/your-project/callback.php');
$payload = $client->verifyIdToken($_POST['id_token']);
if ($payload) {
    $userid = $payload['sub'];
    session_start();
    $_SESSION['userid'] = $userid;
    echo json_encode(['status' => 'success', 'user_id' => $userid]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid ID token.']);
}