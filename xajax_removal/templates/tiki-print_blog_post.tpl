<html id="print">
	<head><title>{$post_info.title|escape}</title></head>
	<body class="tiki_blogs print">
		<div style="margin: 2%">
			{include file='blog_wrapper.tpl' blog_post_context='print'}
			<hr />
			<small>
				{tr}Permalink{/tr}: <a class="link" href="{$post_info.postId|sefurl:blogpost}">{$base_url}{$post_info.postId|sefurl:blogpost}</a>
			</small>
		</div>
	</body>
</html>
