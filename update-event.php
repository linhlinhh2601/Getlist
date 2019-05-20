<?php
//$email = $_POST['email'];
//$newSummary =$_POST['newsummary'];

//$body = "tiêu đề mới. \n \n $newSummary";
//mail($email,$newSummary);
//echo "message sent <a href= 'update.php'></a>";

/**
 * HOMEWORK:
 * 1. Thiết kế HTML cho phép:
 * - Nhập danh sách email người tham dự
 * - Nhập Title của event
 * - Nhập Nội dung event
 * 
 * 2. Submit HTML và bỏ dữ liệu vào khung bên dưới
 * 
 * 3. Update Events dựa vào thông tin đã nhập
 */

 // DS email khách
$invitationList = [

];

// Content của event
$newContent = $_POST['content'];

// Tiêu đề mới
$newSummary = $_POST['newsummary'];

require_once './google-api-php-client-2.2.2/vendor/autoload.php';
session_start();


/**
 * Returns an authorized API client.
 * @return Google_Client the authorized client object
 */
function getClient()
{
    $client = new Google_Client();
    $client->setApplicationName('Google Calendar API PHP Quickstart');
    $client->setScopes(Google_Service_Calendar::CALENDAR);
    $client->setAuthConfig('client_id.json');
    $client->setAccessType('offline');
    $client->setIncludeGrantedScopes(true);   // incremental auth

    if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
        $client->setAccessToken($_SESSION['access_token']);
      } else {
        $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/oauth2callback.php';
        header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
      }
    return $client;
}


// Get the API client and construct the service object.
$client = getClient();
$service = new Google_Service_Calendar($client);

// Print the next 10 events on the user's calendar.
$calendarId = 'primary';
try {
  $event = $service->events->get('primary', $_POST["eventID"]);
} catch (Exception $e) {
    echo "CANNOT GET EVENT";
    die;
}

// $event->setContent($newcontent);
// $updatedEvent=

// Update Event and auto send updated invitation emails using google calendar API
$event->setSummary($newSummary);
$event->setDescription($newContent);
$updatedEvent = $service->events->update('primary', $event->getId(), $event, ['alwaysIncludeEmail' => true, 'sendUpdates' => 'all']);

// Print the updated date.
echo "UPDATED: ";
echo $updatedEvent->getUpdated();
?>