<?php

require 'globals.php';

clearScreen();

//Get match id
$link = readline("Input link co1k (contoh : https://shopee.co.id/collections/994861) : ");
$match_id = getMatchID($link);
//echo $match_id; //Debug

scrapeProductToJSON($cookie, $csrftoken, $match_id);

jsonToCSV();

?>
