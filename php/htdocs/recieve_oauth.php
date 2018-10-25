<?PHP

include("../src/init.php");

if(isset($_REQUEST['code'])){
	$code = $_REQUEST['domain'];
	$google_api->setRedirectUri(SITE_URL.'/recieve_oauth.php?domain='.$code);
	$google_api->authenticate();
	file_put_contents("../cache/contacts_access.".$code, $google_api->getAccessToken());
} else {
	$google_api->authenticate();
}

header('location: getcontact.php');