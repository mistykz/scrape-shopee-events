<?php

require 'globals.php';

clearScreen();

//Read cookie from cookieAccount.txt
$cookie = file_get_contents("cookieAccount.txt");
$csrftoken = getCsrfToken($cookie);
if ($cookie == NULL){
    echo "Tidak ada file cookieAccount.txt!\n";
    exit(0);
}
//echo $cookie;
//echo $csrftoken;

$cookie_and_csrf = [$cookie, $csrftoken];
//echo $cookie_and_csrf[1];


//Get match id
$link = readline("Input link co1k (contoh : https://shopee.co.id/collections/994861) : ");
$match_id = getMatchID($link);
//echo $match_id; //Debug

//Ganti sesuai jumlah page
$page = (int)readline("Input jumlah page : ");

scrapeProductToJSON($match_id, $page, $cookie_and_csrf);

jsonToCSV($page);

$command = escapeshellcmd('python csv_to_xlsx.py');
$output = shell_exec($command);
echo $output;

?>
