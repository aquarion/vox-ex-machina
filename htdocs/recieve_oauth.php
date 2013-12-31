<?PHP

include("../src/init.php");

if(isset($_REQUEST['code'])){
	
	if($_REQUEST['domain'] == "ist"){
		$client->setRedirectUri(SITE_URL.'/recieve_oauth.php?domain=ist');
		$client->authenticate();
		file_put_contents("../cache/contacts_access.ist", $client->getAccessToken());
	} elseif($_REQUEST['domain'] == "naq"){
		$client->setRedirectUri(SITE_URL.'/recieve_oauth.php?domain=naq');
		$client->authenticate();
		file_put_contents("../cache/contacts_access.naq", $client->getAccessToken());
	} 
	
} else {
	$client->authenticate();
}

header('location: getcontact.php');