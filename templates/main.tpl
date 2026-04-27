	<div class="blog-category-pagination">
		{$paginationbar}
	</div>
{foreach from=$categories item=category}
	<div class="blog-category">
		<div class="blog-category-header">
			<h2>{$category["title"]}</h2>
			<button onclick="window.location.href = '/category/id/{$category["id"]}/'">Показать все статьи</button>
		</div>
		<div class="blog-articles-container">
			{foreach from=$category["articles"] item=article}
			<div class="blog-article">
					<div class="blog-article-wrap">
					<div class="blog-article-image" style="background-image:url('/images/{$article["picture"]}')"></div>
					<h3>{$article["title"]}</h3>
					<p>{$article["created_at_a"]}</p>
					<div class="blog-article-description">{$article["description"]}</div>
					</div>
					<button onclick="window.location.href = '/article/id/{$article["id"]}/'">Читать статью</button>
			</div>
			{/foreach}
		</div>
	</div>
{/foreach}
	<div class="blog-category-pagination">
		{$paginationbar}
	</div>

