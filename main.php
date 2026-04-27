<?php
require_once(BASE_PATH.'classes/categories.php');
require_once(BASE_PATH.'classes/pagination.php');

global $page,$smarty;

$page->title = 'Блог Чичикова';
$page->metadescription = 'Блог Чичикова';
$page->metakeywords = 'Блог,Чичиков';
$page->perpage = 4;

$cg = new Categories();
$categories = $cg->getCategories(['order' => 'created_at DESC']);
$count = $cg->getCountCategories();

$url = $page->cutParamFromUrl('page').'main/';
$pg = new Pagination($count,$page->npage,$url,$page->perpage,'pagination-item');
$paginationbar = $pg->getPaginationBar();

$smarty->assign('page',$page);
$smarty->assign('categories',$categories);
$smarty->assign('paginationbar',$paginationbar);
$page->content = $smarty->fetch('templates/main.tpl');

$smarty->display(BASE_PATH.'templates/layout.tpl');
?>