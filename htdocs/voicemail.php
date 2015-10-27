<?php
    require "../src/init.php";
    require "../libraries/twilio/Services/Twilio.php";


	$contact = search_accounts($ACCOUNTS, $google_api, $_REQUEST['From'], $error);

    $response = new Services_Twilio_Twiml();
    if($contact->found && file_exists("outgoing/".$contact->account.".mp3")){
	$response->play("http://voicemail.aqxs.net/outgoing/".$contact->account.".mp3");
    } elseif (file_exists("outgoing/Default.mp3")){
	$response->play("http://voicemail.aqxs.net/outgoing/Default.mp3");
    } else {
    	$response->say('You\'ve reached the voicemail for Nicholas Ay-ven-ell, please leave a message');
    }
    $response->record(
        array(
            'action' => "handle_message.php",
            'maxLength' => '120',
            'transcribe' => 'true',
            'transcribeCallback' => 'transcribed.php'
        )
    );

     // otherwise it falls through to the next verb
    $response->gather()
        ->say("A message was not received, press any key to try again");
 
    header("content-type: text/xml");
    print $response;
