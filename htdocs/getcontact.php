<?PHP

include("../src/init.php");

$search = 'INVALID';


$contact = search_accounts($ACCOUNTS, $google_api, $search, $error);

if($error){
	echo $error;
} else {
	echo "Everything is fine";
}