<?php
if(!defined("BASE_PATH"))
	define("BASE_PATH", realpath(dirname(realpath(__FILE__)) ) . '/../');
require_once(BASE_PATH.'classes/db.php');


$fname = BASE_PATH.'seeding/dead_souls.txt';

$blocks = [];
$blocks[] = [];
$cnt = 0;
$bl = 0; $sl = 0;

$fi = fopen($fname,'r');

while(!feof($fi)){
	$ss = fgets($fi);
	$len = mb_strlen($ss,'utf-8'); 

	if($cnt > 1 && $len > 80){
		$bl++; 
		$blocks[$bl] = [];
		$sl=0;
	}
	if($len > 80){
		$blocks[$bl][$sl] = str_replace(["\n","\r"],'',$ss);
		$sl++;
	}
	if($len < 7){
		$cnt++;
	}else{
		$cnt = 0;
	}
}

fclose($fi);

$db = new Db();
$db->query('TRUNCATE `categories`');
$db->query('TRUNCATE `articles`');
$db->query('TRUNCATE `article2cats`');

$pict = ['001.jpg','002.jpg','100.jpg','101.jpg','102.jpg','103.jpg',
         '201.jpg','202.jpg','203.jpg','301.jpg','302.jpg'];

$created_at = Date('Y-m-d H:i:s',time());
foreach($blocks as $key=>$block){
	$cnt1 = count($block);
	if($cnt1 <= 1) continue;
	$text = $block[0];
	$title = getSentence($text,80);
	$description = getSentence($text,250);
	$created_at = getNewDate($created_at);	
	$cat_id = $db->Insert('categories',[
		'title' => $title,
		'description' => $description,
		'created_at' => $created_at
	]);

	$created_at_a = $created_at;
        for($i=1;$i < ($cnt1 - 1); $i++){
		$text = $block[$i];
		$title_a = getSentence($text,80);
		$description_a = getSentence($text,250);
		$body_a = cutSentence($text, $description_a);
		$body_a = getSentence($body_a,2048);
		$picture = $pict[rand(1,10)];
		$hits = rand(0,60);
		$created_at_a = getNewDate($created_at_a);	
		$art_id = $db->Insert('articles',[
			'title' => $title_a,
			'description' => $description_a,
			'body' => $body_a,
			'picture' => $picture,
			'hits' => $hits,
			'created_at' => $created_at_a
		]);
		$db->Insert('article2cats',[
			'cat_id' => $cat_id,
			'art_id' => $art_id,
			'created_at' => $created_at_a
		]);
	}
}

function getSentence($text,$len){
$delims = ['.','?','!',';',':',',','…',' '];
	$str = mb_substr($text,0,$len,'utf-8');
	foreach($delims as $delim){
		$ostr = mb_strrchr($str,$delim,true,'utf-8');
		if(!is_bool($ostr)){
			break;
		}
	}
	return (!$ostr) ? '' : $ostr.'.';
}
function cutSentence($text,$sentence){
	$sent = mb_strstr($sentence,'.',true,'utf-8');
	$ostr = str_replace($sent,'',$text);
	$ostr = mb_strstr($ostr,' ',false,'utf-8');
	$ostr = ltrim($ostr);
	return (!$ostr) ? '' : $ostr;
}
function getNewDate($date){
	$days = rand(1,10);
	$seconds = rand(40, 23100);
	return DATE('Y-m-d H:i:s',strtotime('-'.$days.' days -'.$seconds.' seconds',strtotime($date)));
}

?>