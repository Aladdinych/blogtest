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

	// Для исходной статьи формируем строку для сравнения	
	$text = $article['body'] . ' ' . $article['description'] . ' ' . $article['title'];
	// Разбиваем строку на слова
	$words = $this->parseIntoWords($text);
	$count = count($words); 
	$result = [];

	// Считываем все статьи из БД	
	$rows = $this->db->getRows('articles', NULL);
	$rowslist = [];
	foreach($rows as $row) {
		// Для каждой целевой статьи из БД получаем массив слов
		$verifiable = $this->parseIntoWords($row['body'] . ' ' . $row['description'] . ' ' . $row['title']);

		// Сравниваем каждое слово исходной статьи со словами из целевой статьи, полученной из БД
		// Подсчитываем количество совпавших слов
		$similar_counter = 0;
		foreach ($words as $word) {
			foreach ($verifiable as $verifiable_row){
				if($word == $verifiable_row) {
					$similar_counter++;
					break;
				}
			}
		}
		// Подсчитываем процент совпавших слов для каждой целевой статьи
		// Формируем массив "похожести" целевых статей 
		$result[$row['id']]['weight'] = $similar_counter * 100 / $count;
		
		$rowslist[$row['id']] = $row;
	}
	// сортируем массив "похожести" целевых статей по убыванию процента
	arsort($result);

	// из полученного массива берем первые ColsOnRow (3) исключая нулевую с процентом - 100%
	$result = array_slice($result, 1, self::ColsOnRow, true);

	// формируем массив данных из отобранных статей для вывода
	// исключая исходную статью и статьи с процентом "похожести" < Similarity (27%)
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

	$text = stripslashes($text); // Удаляем обратные слеши из строки $text
	$text = html_entity_decode($text, ENT_QUOTES);  // Преобразуем HTML сущности в символы
	$text = htmlspecialchars_decode($text, ENT_QUOTES); // Преобразовывает специальные HTML-сущности обратно в символы 
	$text = strip_tags($text);  // Удаляем HTML теги из текста
	$text = mb_strtolower($text);  // Преобразуем строку в нижний регистр
	$text = str_ireplace('ё', 'е', $text); // Заменяем ё на е
	$text = mb_eregi_replace("[^a-zа-яй0-9 ]", ' ', $text); // Заменяем на пробел все символы не являющиеся буквами, цифрами и пробелами
	$text = mb_ereg_replace('[ ]+', ' ', $text); // заменяем подстроку из любого количества пробелов на 1 пробел
	$words = explode(' ', $text); // Разбиваем текст на массив слов
	$words = array_unique($words); // Удаляем повторяющиеся слова из массива. Теперь массив состоит из уникальных слов

	// Массив из предлогов и союзов
	$unnecesary = [
		'без',  'близ',  'в',     'во',     'вместо', 'вне',   'для',    'до', 
		'за',   'и',     'из',    'изо',    'из',     'за',    'под',    'к',  
		'ко',   'кроме', 'между', 'на',     'над',    'о',     'об',     'обо',
		'от',   'ото',   'перед', 'передо', 'пред',   'предо', 'по',     'под',
		'подо', 'при',   'про',   'ради',   'с',      'со',    'сквозь', 'среди',
		'у',    'через', 'но',    'или',    'по',     'не'
	];
	// Удаляем из массива слов предлоги и союзы
	$words = array_diff($words, $unnecesary);
	// Удаляем из массива пустые слова
	$words = array_diff($words, array(''));	

	return $words;
}

}

?>