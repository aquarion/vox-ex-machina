<?PHP

include("../src/init.php");

$search = '+447909547990';


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
}


	echo "<dl>";

	echo "<dt>Name</dt><dd>".$contact->name."</dd>";
	echo "<dt>Email</dt><dd>".$contact->email."</dd>";
	echo "<dt>Job</dt><dd>".$contact->job."</dd>";

	echo "</dl>";

echo '<img src="'.$contact->photo.'">';
