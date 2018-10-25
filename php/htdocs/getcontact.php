<?PHP

include("../src/init.php");

$search = 'INVALID';


$contact = search_accounts($ACCOUNTS, $google_api, $search, $error);

if(!$contact->found){
	$caller = $search;
	if($caller[0] == '0'){
		$number = '+44'.substr($search, 1);
	} else {
		$number = '0'.substr($search, 3);
	}
	echo "<h2>$number</h2>";
	$contact = search_accounts($ACCOUNTS, $google_api, $number, $error);
}

if($error){
	echo $error;
} else {
	echo "Everything is fine";
}
