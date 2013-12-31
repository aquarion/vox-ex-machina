<?php

    include("../src/init.php");

    if (!isset($_REQUEST['RecordingUrl'])) {
        echo "Must specify recording url";
        #die;
        $_REQUEST['RecordingUrl'] = '[Recording URL]';
    }
     
    if (!isset($_REQUEST['TranscriptionStatus'])) {
        echo "Must specify transcription status";
        $_REQUEST['TranscriptionStatus'] = '[TranscriptionStatus]';
    }

    $search = $_REQUEST['Caller'];

    if(!NAQ_ACCESS_KEY){
        $client->setRedirectUri(SITE_URL.'/recieve_oauth.php?domain=ist');
        $client->authenticate();
    }
    if(!IST_ACCESS_KEY){
        $client->setRedirectUri(SITE_URL.'/recieve_oauth.php?domain=ist');
        $client->authenticate();
    }

    $contact = find_contact($client, IST_ACCESS_KEY, $search);
    file_put_contents("../cache/contacts_access.ist", $client->getAccessToken());

    if(!$contact->found){
        $contact = find_contact($client, NAQ_ACCESS_KEY, $search);
        file_put_contents("../cache/contacts_access.naq", $client->getAccessToken());
        $to = "nicholas@aquarionics.com";
    } else {
        $to = "nicholas@istic.net";
    }
     
    if (strtolower($_REQUEST['TranscriptionStatus']) != "completed") {
        $subject = "Voicemail from ".$contact->name;
        $body = "New have a new voicemail from ".$contact->name."\n\n";
        $body .= "Click this link to listen to the message:\n";
        $body .= $_REQUEST['RecordingUrl'];
    } else {
        $subject = "Voicemail from ".$contact->name;
        $body = "New have a new voicemail from ".$contact->name."\n\n";
        $body .= "Text of the Twilio transcribed voicemail:\n";
        $body .= $_REQUEST['TranscriptionText']."\n\n";
        $body .= "Click this link to listen to the message:\n";
        $body .= $_REQUEST['RecordingUrl'];
    }
     
    $headers = 'From: '.$contact->name.' <'.$contact->email. ">\r\n" .
        'X-Mailer: Twilio';
        mail($to, $subject, $body, $headers);
?>