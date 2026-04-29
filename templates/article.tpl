	<div class="blog-article-single">
		<div class="article-single-header">
			<h1>{$article['title']}</h1>

		</div>
		<div class="blog-article-nav">
			<button onclick="javascript:history.back(1);">Назад</button>
		</div>
		<div class="article-single-container">
			<div class="article-single-image" style="background-image:url('/images/{$article['picture']}')"></div>
			<div class="article-single-wrap">
				<div class="article-single-wrap-1">
					<div class="article-single-description">{$article['description']}</div>
					<div class="article-single-body">
					{$article['body']}
					</div>
				</div>
				<div class="article-single-info">
					<p><b>Просмотров:</b> {$article['hits']}</p>
					<p><b>Опубликовано:</b> {$article['created_at_a']}</p>
				</div>
			</div>
	
		</div>
		{if count($article["articles"]) > 0}
		<div class="article-articles-wrap">
			<h2>Похожие статьи</h2>
			<div class="article-articles-container">
				{foreach from=$article["articles"] item=art}
				<div class="blog-article">
					<div class="blog-article-wrap">
						<div class="blog-article-image" style="background-image:url('/images/{$art["picture"]}')"></div>
							<h3>{$art["title"]}</h3>
							<p>{$art["created_at_a"]}</p>
							<div class="blog-article-description">{$art["description"]}</div>
						</div>
					<button onclick="window.location.href = '/article/id/{$art["id"]}/'">Читать статью</button>
				</div>
				{/foreach}
			</div>
		</div>
		{/if}
	</div>

