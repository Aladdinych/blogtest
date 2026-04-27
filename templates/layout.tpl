<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>{$page->title}</title>
<meta name="title" content="{$page->title}">
<meta name="keywords" content="{$page->metakeywords}">
<meta name="description" content="{$page->metadescription}">

<link href="/css/style.css?ver=1.21042026001" rel="stylesheet" media="all">
<meta name="viewport" content="width=device-width, height=device-height, initial-scale=1, maximum-scale=1">
<meta http-equiv="cleartype" content="on">


<link rel="icon" href="/favicon.ico" type="image/x-icon">
<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">

</head>
<body>
	<div class="header-blog">
		<div class="header-blog-overlay"></div>
		<div class="header-blog-container">
			<div class="blog-logo"><a href="/"> </a></div>
			<h1>Блог Чичикова</h1>
			<div class="header-block-right"></div>
		</div>
	</div>
	<div class="content-blog">
		<div class="content-blog-container">
		{$page->content}
		</div>
	</div>

	<div class="footer-blog">
		<div class="footer-blog-container">
			<div class="footer-blog-copyright">Copyright ©2025 Чичиков & Co. Все права защищены.</div>
		</div>
	</div>

{literal}
<script type="text/javascript">

</script>
<script src="/js/script.js?ver=1.21042026001"></script>
{/literal}

																
</body>
</html>
