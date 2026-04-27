<?php
require_once(BASE_PATH.'classes/categories.php');

global $page,$smarty;

$cg = new Categories();
$article = $cg->getArticle(['order' => 'ar.created_at DESC','id' => $page->uriparams['id']]);


$page->title = $article['title'];
$page->metadescription = $article['description'];
$page->metakeywords = 'Блог,Чичиков';

$smarty->assign('page',$page);
$smarty->assign('article',$article);
$page->content = $smarty->fetch('templates/article.tpl');

$smarty->display(BASE_PATH.'templates/layout.tpl');

?>