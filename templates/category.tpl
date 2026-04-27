
	<div class="blog-category-single">
		<div class="blog-category-header">
			<h1>{$category["title"]}</h1>
		</div>
		<div class="blog-category-description">
			<p>{$category["description"]}</p>
		</div>
		<div class="blog-category-nav">
			<button onclick="window.location.href = '/'">На главную</button>
			<select id="category-sort" class="blog-category-sort" onchange="resort();">
				<option value="ar.created_at DESC" {if $sort == "ar.created_at DESC"} selected {/if}>По дате публикации ↓</option>
				<option value="ar.created_at ASC" {if $sort == "ar.created_at ASC"} selected {/if}>По дате публикации ↑</option>
				<option value="ar.hits DESC" {if $sort == "ar.hits DESC"} selected {/if}>По числу просмотров ↓</option>
				<option value="ar.hits ASC" {if $sort == "ar.hits ASC"} selected {/if}>По числу просмотров ↑</option>
			</select>
		</div>
		<div class="blog-category-pagination">
			{$paginationbar}
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
		<div class="blog-category-pagination">
			{$paginationbar}
		</div>
	</div>
