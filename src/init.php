<?PHP


set_include_path(get_include_path() . PATH_SEPARATOR . '../libraries/google-api-php-client/src' . PATH_SEPARATOR . '../libraries/twilio');

require_once '../etc/config.php';
require_once 'Google_Client.php';

$client = new Google_Client();
$client->setApplicationName("Vox Ex Machina");
$client->setScopes(array(
    'https://apps-apis.google.com/a/feeds/groups/',
    'https://apps-apis.google.com/a/feeds/alias/',
    'https://apps-apis.google.com/a/feeds/user/',
	'https://www.google.com/m8/feeds/',
	'https://www.google.com/m8/feeds/user/',
));
 $client->setClientId(GOOGLE_CLIENT_ID);
 $client->setClientSecret(GOOGLE_SECRET);
 $client->setRedirectUri(SITE_URL.'/recieve_oauth.php');
 $client->setDeveloperKey(DEVELOPER_KEY);



 function find_contact(&$client, $access_key, $contact){

	$client->setAccessToken($access_key);
	$token = json_decode($access_key);
	$auth_pass = $token->access_token;

	$req = new Google_HttpRequest("https://www.google.com/m8/feeds/contacts/default/full?q=".urlencode($contact));
	$req->setRequestHeaders(array('GData-Version'=> '3.0','content-type'=>'application/atom+xml; charset=UTF-8; type=feed'));
	$val = $client->getIo()->authenticatedRequest($req);
	$response =$val->getResponseBody();
	$xml = simplexml_load_string($response);

	$result = new StdClass();

	$result->name  = $contact;
	$result->job   = '';
	$result->email = 'no-reply@istic.net';
	$result->photo = false;
	$result->found = false;

	if(sizeof($xml->entry) > 0){
		$result->found = true;
		$item =$xml->entry[0];
		$ns_gd = $item->children('http://schemas.google.com/g/2005');
		$ns_Contact = $item->children('http://schemas.google.com/contact/2008');

		if(isset($ns_gd->name->fullName)) {
			$result->name = (string)$ns_gd->name->fullName;
		}

		if(isset($ns_gd->organization)){
			$job = array();
			if(isset($ns_gd->organization->orgName)){
				$job[] = $ns_gd->organization->orgName;
			}
			
			if(isset($ns_gd->organization->orgTitle)){
				$job[] = $ns_gd->organization->orgTitle;
			}
			$result->job = join(" - ", $job);
		}

		foreach($ns_gd->email as $email){
			$attributes = $email->attributes();
			if($attributes->primary == 'true'){
				$result->email = (string)$attributes->address;
				break;
			}
		}

		foreach($item->link as $link){
			if($link->attributes()->rel == "http://schemas.google.com/contacts/2008/rel#photo"){
				$req = new Google_HttpRequest($link->attributes()->href);
				$val = $client->getIo()->authenticatedRequest($req);
				$response =$val->getResponseBody();
				$result->photo = 'data: image/jpeg;base64,'.base64_encode($response);
				break;
			}
		}

	}

	$group = (string)$xml->id;
 	error_log("Search for $contact in ".$group.": ".( $result->found ? 'Found' : 'Not Found'));


	return $result;
 }
