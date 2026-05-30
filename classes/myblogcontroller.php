<?php
namespace Classes;

use Classes\Categories;
use Classes\Pagination;

class myBlogController{

use SmartyInit;

private $categories;
private $page;

function __construct($page){

	$this->categories = new Categories;
	$this->page = $page;
	$this->smartyInit();
	
}

public function main(){

$this->page->title = 'Блог Чичикова';
$this->page->metadescription = 'Блог Чичикова';
$this->page->metakeywords = 'Блог,Чичиков';
$this->page->perpage = 4;

$categories = $this->categories->getCategories(['order' => 'created_at DESC']);
$count = $this->categories->getCountCategories();

$url = $this->page->cutParamFromUrl('page').'main/';
$pg = new Pagination($count,$this->page->npage,$url,$this->page->perpage,'pagination-item');
$paginationbar = $pg->getPaginationBar();

$this->smarty->assign('page',$this->page);
$this->smarty->assign('categories',$categories);
$this->smarty->assign('paginationbar',$paginationbar);
$this->page->content = $this->smarty->fetch('templates/main.tpl');

$this->smarty->display(BASE_PATH.'templates/layout.tpl');

}

public function category(){

$this->page->perpage = 9;
$sort = (!isset($this->page->uriparams['sort'])) ? 'ar.created_at DESC' : urldecode($this->page->uriparams['sort']);

$category = $this->categories->getCategory(['order' => $sort,'cat_id' => $this->page->uriparams['id']]);
$count = $this->categories->getCountArticles(['cat_id' => $this->page->uriparams['id']]);

$url = $this->page->cutParamFromUrl('page');
$pg = new Pagination($count,$this->page->npage,$url,$this->page->perpage,'pagination-item');
$paginationbar = $pg->getPaginationBar();

$this->page->title = $category['title'];
$this->page->metadescription = $category['description'];
$this->page->metakeywords = 'Блог,Чичиков';

$this->smarty->assign('page',$this->page);
$this->smarty->assign('category',$category);
$this->smarty->assign('sort',$sort);
$this->smarty->assign('paginationbar',$paginationbar);

$this->page->content = $this->smarty->fetch('templates/category.tpl');

$this->smarty->display(BASE_PATH.'templates/layout.tpl');

}

public function article(){

$article = $this->categories->getArticle(['order' => 'ar.created_at DESC','id' => $this->page->uriparams['id']]);


$this->page->title = $article['title'];
$this->page->metadescription = $article['description'];
$this->page->metakeywords = 'Блог,Чичиков';

$this->smarty->assign('page',$this->page);
$this->smarty->assign('article',$article);
$this->page->content = $this->smarty->fetch('templates/article.tpl');

$this->smarty->display(BASE_PATH.'templates/layout.tpl');

}


}

?>