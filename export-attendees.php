<?php
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
    $client->setScopes(Google_Service_Calendar::CALENDAR_READONLY);
    $client->setAuthConfig('credentials.json');
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

/**
 * Export Excel Process
 */
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set Header
$sheet->setCellValue('A1', 'Email');
$sheet->setCellValue('B1', 'Status');


$attendeeList = $event->getAttendees();


$row = 2;
// prepare row for Excel Export
foreach ($attendeeList as $key => $attendee) {
  $sheet->setCellValue('A' . $row , $attendee->getEmail());
  $sheet->setCellValue('B' . $row, $attendee->getResponseStatus());
  $row++;
}

$writer = new Xlsx($spreadsheet);

$fileName = "AttendeeList_" . date("dmY", time()) . ".xlsx";
$filePath = './export/' . $fileName;
$writer->save($filePath);

header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
header("Cache-Control: public"); // needed for internet explorer
header("Content-Type: application/xlsx");
header("Content-Transfer-Encoding: Binary");
header("Content-Length:".filesize($filePath));
header("Content-Disposition: attachment; filename=" . $fileName);
readfile($filePath);

