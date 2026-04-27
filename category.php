<?php
require_once(BASE_PATH.'classes/categories.php');
require_once(BASE_PATH.'classes/pagination.php');

global $page,$smarty;

$page->perpage = 9;
$sort = (!isset($page->uriparams['sort'])) ? 'ar.created_at DESC' : urldecode($page->uriparams['sort']);

$cg = new Categories();
$category = $cg->getCategory(['order' => $sort,'cat_id' => $page->uriparams['id']]);
$count = $cg->getCountArticles(['cat_id' => $page->uriparams['id']]);

$url = $page->cutParamFromUrl('page');
$pg = new Pagination($count,$page->npage,$url,$page->perpage,'pagination-item');
$paginationbar = $pg->getPaginationBar();

$page->title = $category['title'];
$page->metadescription = $category['description'];
$page->metakeywords = 'Блог,Чичиков';

$smarty->assign('page',$page);
$smarty->assign('category',$category);
$smarty->assign('sort',$sort);
$smarty->assign('paginationbar',$paginationbar);

$page->content = $smarty->fetch('templates/category.tpl');

$smarty->display(BASE_PATH.'templates/layout.tpl');


?>