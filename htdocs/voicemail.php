<?php
    require "../libraries/twilio/Services/Twilio.php";

    $response = new Services_Twilio_Twiml();
    $response->say('Leave a message for me at the beep');
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