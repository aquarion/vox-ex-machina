<?PHP


set_include_path(get_include_path() . 
	PATH_SEPARATOR . '../libraries/google-api-php-client/src' . 
	PATH_SEPARATOR . '../libraries/twilio' .
	 PATH_SEPARATOR . '../libraries/PHPMailer');

require_once '../etc/config.php';
require_once 'Google_Client.php';
require_once 'PHPMailerAutoload.php';

$google_api = new Google_Client();
$google_api->setApplicationName("Vox Ex Machina");
$google_api->setScopes(array(
    'https://apps-apis.google.com/a/feeds/groups/',
    'https://apps-apis.google.com/a/feeds/alias/',
    'https://apps-apis.google.com/a/feeds/user/',
	'https://www.google.com/m8/feeds/',
	'https://www.google.com/m8/feeds/user/',
));
 $google_api->setClientId(GOOGLE_CLIENT_ID);
 $google_api->setClientSecret(GOOGLE_SECRET);
 $google_api->setRedirectUri(SITE_URL.'/recieve_oauth.php');
 $google_api->setDeveloperKey(DEVELOPER_KEY);



 function find_contact(&$google_api, $access_key, $contact){

	$google_api->setAccessToken($access_key);
	$token = json_decode($access_key);
	$auth_pass = $token->access_token;

	$req = new Google_HttpRequest("https://www.google.com/m8/feeds/contacts/default/full?q=".urlencode($contact));
	$req->setRequestHeaders(array('GData-Version'=> '3.0','content-type'=>'application/atom+xml; charset=UTF-8; type=feed'));
	$val = $google_api->getIo()->authenticatedRequest($req);

	if($val->getResponseHttpCode() != 200){
		throw new Exception("API Access Failure");
	}

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
				$val = $google_api->getIo()->authenticatedRequest($req);
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


function search_accounts($ACCOUNTS, &$google_api, $search, &$error){
	$contact = false;
	$error = '';
	foreach($ACCOUNTS as $code => $account){
		$access_key = @file_get_contents("../cache/contacts_access.".$code);
		$google_api->setRedirectUri(SITE_URL.'/recieve_oauth.php?domain='.$code);
		$authUrl = $google_api->createAuthUrl();
		if(!$access_key){
			$error .= '&bull; '.$account['name'].' isn\'t connected, please visit this link to fix this: <a href="'.$authUrl.'">Reconnect '.$account['name'].'</a><br/>';
		} else {
			try {
				$contact = find_contact($google_api, $access_key, $search);
				file_put_contents("../cache/contacts_access.".$code, $google_api->getAccessToken());
			} catch (Exception $e){
			 	$error .= '&bull; '.$account['name'].'\'s connection is broken, please visit this link to fix this: <a href="'.$authUrl.'">Reconnect '.$account['name'].'</a><br/>';
			}
		}
		if($contact->found){
			$contact->account = $account['name'];
			$contact->account_email = $account['to'];
			return $contact;
		}
	}
	return $contact;
}