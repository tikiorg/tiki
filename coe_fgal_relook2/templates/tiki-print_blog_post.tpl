<html id="print">
	<head></head>
	<body>
		{include file='tiki-view_blog_post_content.tpl'}
		<hr>
		<small>
			{tr}Permalink{/tr}: <a class="link" href="{$postId|sefurl:blogpost}">{$base_url}{$postId|sefurl:blogpost}</a>
		</small>
	</body>
</html>
