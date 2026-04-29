<?php

require_once('db.php');


class Categories {

const Similarity = 27;
const ColsOnRow = 3;

public $title;
public $description;
public $published;
public $articles;
private $db;

function __construct() {
	$this->db = new Db();
}


public function getCategories($data) {
global $page;

	$offset = ($page->npage - 1) * $page->perpage;
	$limit = $page->perpage;

	$categories = [];

	$sql = "SELECT ct.id as ct_id, ct.created_at as ct_created_at, ct.title as ct_title, ct.description as ct_description, ar.* 
		FROM (SELECT id, created_at,title,description FROM `categories` ORDER BY {$data['order']} LIMIT {$offset} , {$limit}) ct 
		JOIN `article2cats` ac ON ac.cat_id = ct.id
		JOIN `articles` ar ON ar.id = ac.art_id
		WHERE ac.art_id > 0
		ORDER BY ct.created_at DESC, ar.created_at DESC";


/*	$sql = "SELECT ct.id as ct_id,ct.title as ct_title,
		ct.description as ct_description,ct.created_at as published, aa.*
		FROM `categories` ct
		JOIN LATERAL (SELECT ac.cat_id as cat_id, ar.* FROM `article2cats` ac 
		JOIN `articles` ar ON ar.id = ac.art_id WHERE ac.cat_id = ct.id LIMIT 3) aa ON aa.cat_id = ct.id
		ORDER BY :order, aa.id ASC";
*/

	$fvlist = [];
	$cat = $this->db->getBySQL($sql,$fvlist);

	foreach($cat as $ct) {
		if(!isset($categories[$ct['ct_id']])){
			$categories[$ct['ct_id']] = [
				'id' => $ct['ct_id'],
				'title' => $ct['ct_title'],
				'description' => $ct['ct_description'],
				'created_at' => $ct['ct_created_at'],
				'created_at_a' => $page->dateFormatWithMonthStr($ct['ct_created_at']),
			];
		}

		if(!isset($categories[$ct['ct_id']]['articles']) || count($categories[$ct['ct_id']]['articles']) < self::ColsOnRow){
			$categories[$ct['ct_id']]['articles'][$ct['id']] = [
				'id' => $ct['id'],
				'title' => $ct['title'],
				'description' => $ct['description'],
				'body' => $ct['body'],
				'picture' => $ct['picture'],
				'hits' => $ct['hits'],
				'created_at' => $ct['created_at'],
				'created_at_a' => $page->dateFormatWithMonthStr($ct['created_at'])

			];	
		}
	}
	return $categories;

}
public function getCountCategories() {

	$sql = "SELECT COUNT(*)	as count FROM `categories` ct";

	$fvlist = [];
	$cat = $this->db->getBySQL($sql,$fvlist);

	return $cat[0]['count'];
}
public function getCategory($data) {
global $page;

	$offset = ($page->npage - 1) * $page->perpage;
	$limit = $page->perpage;

	$sql = "SELECT ct.id as ct_id,ct.title as ct_title,
		ct.description as ct_description,ct.created_at as ct_created_at, ar.* 
		FROM `categories` ct 
		JOIN `article2cats` ac ON ac.cat_id = ct.id
		JOIN `articles` ar ON ar.id = ac.art_id
		WHERE ct.id = :cat_id
		ORDER BY {$data['order']}
		LIMIT {$offset} , {$limit}";

	$fvlist = ['cat_id' => $data['cat_id']];
	$cat = $this->db->getBySQL($sql,$fvlist);

	$category = [];
	foreach($cat as $ct) {
		if(!isset($category) || count($category) == 0){
			$category = [
				'id' => $ct['ct_id'],
				'title' => $ct['ct_title'],
				'description' => $ct['ct_description'],
				'created_at' => $ct['ct_created_at'],
				'created_at_a' => $page->dateFormatWithMonthStr($ct['ct_created_at']),
				'articles' =>[]
			];
		}
			$category['articles'][$ct['id']] = [
				'id' => $ct['id'],
				'title' => $ct['title'],
				'description' => $ct['description'],
				'body' => $ct['body'],
				'picture' => $ct['picture'],
				'hits' => $ct['hits'],
				'created_at' => $ct['created_at'],
				'created_at_a' => $page->dateFormatWithMonthStr($ct['created_at'])

			];	
	}

	return $category;

}
public function getCountArticles($data) {

	$sql = "SELECT COUNT(*) as count
		FROM `categories` ct 
		JOIN `article2cats` ac ON ac.cat_id = ct.id
		JOIN `articles` ar ON ar.id = ac.art_id
		WHERE ct.id = :cat_id";

	$fvlist = ['cat_id' => $data['cat_id']];
	$cat = $this->db->getBySQL($sql,$fvlist);

	return $cat[0]['count'];
}

public function getArticle($data) {
global $page;
	$sql = "SELECT ar.* 
		FROM `articles` ar
		WHERE ar.id = :art_id
		ORDER BY {$data['order']}";

	$fvlist = ['art_id' => $data['id']];
	$cat = $this->db->getBySQL($sql,$fvlist);

	$article = [];
	$ct = $cat[0];

	$article = [
		'id' => $ct['id'],
		'title' => $ct['title'],
		'description' => $ct['description'],
		'body' => $ct['body'],
		'picture' => $ct['picture'],
		'hits' => $ct['hits'],
		'created_at' => $ct['created_at'],
		'created_at_a' => $page->dateFormatWithMonthStr($ct['created_at'])
	];

	$article['articles'] = $this->getSimilarArticles($article);

	return $article;
}

public function getSimilarArticles($article){
global $page;

	$text = $article['body'] . ' ' . $article['description'] . ' ' . $article['title'];
	$words = $this->parseIntoWords($text);
	$count = count($words); 
	$result = [];
	
	$rows = $this->db->getRows('articles', NULL);
	$rowslist = [];
	foreach($rows as $row) {
		$verifiable = $this->parseIntoWords($row['body'] . ' ' . $row['description'] . ' ' . $row['title']);
		$similar_counter = 0;
		foreach ($words as $word) {
			foreach ($verifiable as $verifiable_row){
				if($word == $verifiable_row) {
					$similar_counter++;
					break;
				}
			}
		}
	$result[$row['id']]['weight'] = $similar_counter * 100 / $count;

	$rowslist[$row['id']] = $row;
	}

	arsort($result);
	$result = array_slice($result, 1, self::ColsOnRow, true);
	$articles = [];
	foreach($result as $key=>$item){
		if($item['weight'] > self::Similarity && $item['weight'] <> 100){
			$articles[$key] = [
				'id' => $rowslist[$key]['id'],
				'title' => $rowslist[$key]['title'],
				'description' => $rowslist[$key]['description'],
				'body' => $rowslist[$key]['body'],
				'picture' => $rowslist[$key]['picture'],
				'hits' => $rowslist[$key]['hits'],
				'created_at' => $rowslist[$key]['created_at'],
				'created_at_a' => $page->dateFormatWithMonthStr($rowslist[$key]['created_at'])
			];
		}
	}

	return $articles;
}
private function parseIntoWords($text){

	$text = stripslashes($text);
	$text = html_entity_decode($text);
	$text = htmlspecialchars_decode($text, ENT_QUOTES);
	$text = strip_tags($text);
	$text = mb_strtolower($text);
	$text = str_ireplace('ё', 'е', $text);
	$text = mb_eregi_replace("[^a-zа-яй0-9 ]", ' ', $text);
	$text = mb_ereg_replace('[ ]+', ' ', $text);
	$words = explode(' ', $text);
	$words = array_unique($words);

	$unnecesary = [
		'без',  'близ',  'в',     'во',     'вместо', 'вне',   'для',    'до', 
		'за',   'и',     'из',    'изо',    'из',     'за',    'под',    'к',  
		'ко',   'кроме', 'между', 'на',     'над',    'о',     'об',     'обо',
		'от',   'ото',   'перед', 'передо', 'пред',   'предо', 'по',     'под',
		'подо', 'при',   'про',   'ради',   'с',      'со',    'сквозь', 'среди',
		'у',    'через', 'но',    'или',    'по',     'не'
	];

	$words = array_diff($words, $unnecesary);
	$words = array_diff($words, array(''));	

	return $words;
}

}

?>