<?php
require __DIR__ . '/../lib/google-api-php/vendor/autoload.php';
require __DIR__ . '/../credentials.php';

/**
 * Returns an authorized API client.
 * @return Google_Client the authorized client object
 */
function getClient() {
    $client = new Google_Client();
    $client->setApplicationName('Google Drive API PHP Quickstart');
    $client->setScopes(Google_Service_Drive::DRIVE_READONLY);
    $client->setAuthConfig('../credentials.json');
    $client->setAccessType('offline');
    $client->setPrompt('select_account consent');

    // Load previously authorized token from a file, if it exists.
    // The file token.json stores the user's access and refresh tokens, and is
    // created automatically when the authorization flow completes for the first
    // time.
    $tokenPath = '../token.json';
    if (file_exists($tokenPath)) {
        $accessToken = json_decode(file_get_contents($tokenPath), true);
        $client->setAccessToken($accessToken);
    }

    // If there is no previous token or it's expired.
    if ($client->isAccessTokenExpired()) {
        // Refresh the token if possible, else fetch a new one.
        if ($client->getRefreshToken()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
        } else {
            // Request authorization from the user.
            $authUrl = $client->createAuthUrl();
            printf("Open the following link in your browser:\n%s\n", $authUrl);
            print 'Enter verification code: ';
            $authCode = trim(fgets(STDIN));

            // Exchange authorization code for an access token.
            $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
            $client->setAccessToken($accessToken);

            // Check to see if there was an error.
            if (array_key_exists('error', $accessToken)) {
                throw new Exception(join(', ', $accessToken));
            }
        }
        // Save the token to a file.
        if (!file_exists(dirname($tokenPath))) {
            mkdir(dirname($tokenPath), 0700, true);
        }
        file_put_contents($tokenPath, json_encode($client->getAccessToken()));
    }
    return $client;
}

function hasEntryToday($link) {
    $query = "SELECT * FROM entries WHERE date = CURRENT_DATE()";
    $result = mysqli_query($link, $query);
    
    if ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
        return true;
    } else {
        return false;
    }
}

function countWords() {
    $client = getClient();
    $service = new Google_Service_Drive($client);

    $fileId = $GLOBALS["ROADTOMORTI_file_id"];
    $file = $service->files->get($fileId); 

    $content = $service->files->get($fileId, array("alt" => "media"));  // Added
    $body = $content->getBody();
    $wordCount = str_word_count($body, 0, '0..9¿¡!?áéíóúñ"\'-.,:[]çÇÁÉÍÓÚÑ');
    return $wordCount;
}

function logWords($wordCount) { 
    $link = new mysqli('localhost', $GLOBALS["ROADTOMORTI_mysql_user"], $GLOBALS["ROADTOMORTI_mysql_pass"], $GLOBALS["ROADTOMORTI_mysql_db"]) or die ('Die');
    mysqli_set_charset($link, "UTF8");

    if (hasEntryToday($link)) {
        $update = "UPDATE entries SET count='" . $wordCount . "',time=NOW() WHERE date = CURRENT_DATE()";
        mysqli_query($link, $update);
    } else {
        $insert = "INSERT entries VALUES (NOW(), " . $wordCount . ", NOW())";
        mysqli_query($link, $insert);
    }
}

?>