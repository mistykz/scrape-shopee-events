<?php

function clearScreen(){
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $clr = system('cls');
    }
    else{
        $clr = system('clear');
    }
    return $clr;
}

function curlGet($url, $head){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_TIMEOUT, 3);
	$result = curl_exec($ch);
	return $result;
}

function getMatchID($url){
	$match_id_split = explode("collections/", $url);
	$match_id = $match_id_split[1];
	//echo $match_id_split;
	return $match_id;
}

function getCsrfToken($cookie){
	if($cookie != NULL){
		$csrf_token1 = explode("csrftoken=", $cookie);
		$csrf_token2 = $csrf_token1[1];
		$csrf_token3 = explode(";", $csrf_token2);
		$csrftoken = $csrf_token3[0];
		return $csrftoken;
	}
	else{
		echo "File cookie.txt tidak tersedia!";
		return;
	}
}

function scrapeProductToJSON($cookie, $csrftoken, $match_id){
	//Var count untuk menambahkan jumlah products pada "newest" di url tujuan dibawah ini
	$count = 0;

	//Ganti sesuai jumlah page
	$page = 33;

	//Jumlah produk per page (dapat diganti manual)
	//$productsCount = 60;
	
	//Make folder for json
	$dir = "json_file";
	if(is_dir($dir) === false){
		mkdir($dir);
	}	

	for($a = 0; $a<$page; $a++){
		$url = "https://shopee.co.id/api/v4/search/search_items?by=price&limit=60&match_id=$match_id&newest=$count&order=desc&page_type=collection&scenario=PAGE_COLLECTION_SEARCH&version=2";
		$count = $count + 60;

		$header = array(
	    	'Host: shopee.co.id',
	    	'user-agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.81 Safari/537.36',
	    	'accept: application/json',
	    	'x-shopee-language: id',
	    	'x-requested-with: XMLHttpRequest',
	    	'if-none-match-: ',
	    	'content-type: application/json',
	    	"x-csrftoken: $csrftoken",
	    	'origin: https://shopee.co.id',
	    	'referer: https://shopee.co.id/collections/994861?order=desc&page=8&sortBy=price',
	    	"cookie: $cookie"
	    );

		$data = json_decode(curlGet($url, $header), true);
		//print_r($data);
		//$a = count($data["items"]);
		//echo $a;
		
		$data_json = array(array());

		for($i = 0; $i < count($data["items"]); $i++){
			$data_json[$i] = $data["items"][$i]["item_basic"];
		}

		$json = json_encode(array('data' => $data_json), JSON_PRETTY_PRINT);

		file_put_contents("$dir/data$a.json", $json);

		echo "Sukses membuat file json pada page ke $a\n";
	}
}

function jsonToCSV(){
	//Ganti sesuai jumlah page
	$page = 33;

	//Jumlah produk per page (dapat diganti manual)
	//$productsCount = 60;

	//Migrasi isi file json ke csv
	for($b = 0; $b<$page; $b++){
		$location_json = "json_file/data$b.json";
		$content_json = file_get_contents($location_json);
		$data_json = json_decode($content_json, true);
		$file = fopen("data.csv", "a+");
		for($j = 0; $j < count($data_json["data"]); $j++){
			$name = $data_json["data"][$j]["name"];
			$price = $data_json["data"][$j]["price"]/100000;
			$shopid = $data_json["data"][$j]["shopid"];
			$itemid = $data_json["data"][$j]["itemid"];
			$link = "https://shopee.co.id/product/$shopid/$itemid";
			//printf("\n[%s]\nNama : %s\nHarga : %s\n\n", $j+1, $name, $price);
			//$contents = "\n\rNama : $name \r\nPrice : $price\r\nLink product : $link\n\r\n\r";
			$content_csv = array($name, $price, $link);
			fputcsv($file, $content_csv);
		}
		//fclose($file);
		//unlink($location_json); //Delete file json ketika sudah selesai input ke csv
		echo "Sukses input file dari json $b ke csv\n";
	}
}

?>
