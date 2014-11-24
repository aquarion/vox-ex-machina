<?php

include("../src/init.php");

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

if(!$contact->found){
	$caller = $_REQUEST['Caller'];
	if($caller[0] == '0'){
		$number = '+44'.substr($caller, 1);
	} else {
		$number = '0'.substr($caller, 3);
	}
	$contact = search_accounts($ACCOUNTS, $google_api, $number, $error);
}

$mail = new PHPMailer;
$mail->From = 'vox@voicemail.aqxs.net';
$mail->FromName = 'Vox Ex Machina';

$mail->isHTML(true);     

if($contact->found){
    $mail->addAddress($contact->account_email);
    $mail->addReplyTo($contact->email, $contact->name);
} else {
    $mail->addAddress(DEFAULT_MAILTO);

    $contact->name  = $_REQUEST['Caller'];
    $contact->job   = '';
    $contact->email = 'no-reply@istic.net';
    $contact->photo = false;
    $contact->account = 'no known';
    $contact->found = false;
}

$mail->Subject = "Voicemail from ".$contact->name;

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

$mail->AltBody = $body;

$template = file_get_contents("../src/templates/".$template);

$mail->Body = str_replace(array_keys($tags), array_values($tags), $template);

$string   = file_get_contents($_REQUEST['RecordingUrl']);
$filename = "vox_".strtr($contact->name, " ","-")."_".date("Y-m-d").".mp3";
$mail->AddStringAttachment($string, $filename, $encoding = 'base64', $type = 'application/octet-stream');

$mail->send();
