<?php

include("../src/init.php");

require_once('../vendor/autoload.php');
use Postmark\PostmarkClient;
use Postmark\Models\PostmarkException;
use Postmark\Models\PostmarkAttachment;

if (!isset($_REQUEST['RecordingUrl'])) {
    echo "Must specify recording url";
    #die;
    $_REQUEST['RecordingUrl'] = 'http://api.twilio.com/2010-04-01/Accounts/ACf179df625b71b4cbcf69aab065367e02/Recordings/REe7d0835a70a72fa772ca02556203a028';
}
 
if (!isset($_REQUEST['TranscriptionStatus'])) {
    echo "Must specify transcription status";
    $_REQUEST['TranscriptionStatus'] = '[TranscriptionStatus]';
}
if (!isset($_REQUEST['Caller'])) {
    echo "Must specify Caller";
    $_REQUEST['Caller'] = '+447909547990';
}

$contact = search_accounts($ACCOUNTS, $google_api, $_REQUEST['Caller'], $error);


$postmark = new PostmarkClient(POSTMARK_KEY);

$message = array();
$From = 'vox@aqxs.net';
$FromName = 'Vox Ex Machina';

$message['From'] = sprintf('"%s" <%s>', $FromName, $From);

if($contact->found){
    $message['To'] = $contact->account_email;
    $message['ReplyTo'] = $contact->email;
} else {
    $message['To'] = DEFAULT_MAILTO;

    $contact->name  = $_REQUEST['Caller'];
    $contact->job   = '';
    $contact->email = $From;
    $contact->photo = false;
    $contact->account = 'no known';
    $contact->found = false;
}

$message['Subject'] = "Voicemail from ".$contact->name;

$tags = array(
    '[[NAME]]' => $contact->name,
    '[[JOB]]'  => $contact->job ? '('. $contact->job.')' : '',
    '[[TRANSCRIPTION]]' => isset($_REQUEST['TranscriptionText']) ? $_REQUEST['TranscriptionText'] : '',
    '[[ERROR]]' => $error ? $error : "Contact found in ".$contact->account.'  contact list'
);
 
if (strtolower($_REQUEST['TranscriptionStatus']) != "completed") {
    $body = "New have a new voicemail from ".$contact->name."\n\n";
    $template = "voicemail.html";

} else {
    $body = "New have a new voicemail from ".$contact->name."\n\n";
    $body .= "Text of the Twilio transcribed voicemail:\n";
    $body .= $_REQUEST['TranscriptionText']."\n\n";
    $template = "transcribed.html";
}


$template = file_get_contents("../src/templates/".$template);

$message['TextBody'] = $body;
$message['HtmlBody'] = str_replace(array_keys($tags), array_values($tags), $template);

$filename = "vox_".strtr($contact->name, " ","-")."_".date("Y-m-d").".mp3";

$attachment = PostmarkAttachment::fromFile($_REQUEST['RecordingUrl'], $filename, 'audio/mpeg');

$message['Attachments'] = array($attachment);


try{
	$resp= $postmark->sendEmailBatch(array($message));
	var_dump($resp);
}catch(PostmarkException $ex){
	var_dump($ex);
}catch(Exception $ex){
	var_dump($ex);
}
