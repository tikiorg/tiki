{extends "layout_view.tpl"}

{block name="title"}
	{title}{$title}{/title}
{/block}

{block name="content"}
	<form method="post" action="{service controller=wiki action=regenerate_slugs}">
		<p>{tr}Re-generating the URLs may cause 404 errors for links coming from external sources, such as search engines.{/tr}</p>

		<p>{tr}You will also need to rebuild your caches and search index.{/tr}</p>
		<p>{tr}Do you really want to proceed?{/tr}</p>
		<div class="submit">
			<input class="btn btn-warning" type="submit" value="{tr}Re-generate URLs{/tr}">
		</div>
	</form>
{/block}
