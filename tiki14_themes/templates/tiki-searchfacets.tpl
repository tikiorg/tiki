{* $Id: tiki-searchindex.tpl 42331 2012-07-10 15:05:01Z jonnybradley $ *}
{extends 'layout_view.tpl'}

{block name=title}
	{title help="Search" admpage="search"}{tr}Search{/tr}{/title}
{/block}

{block name=content}
<div class="nohighlight">
	<form id="search-form" class="form-inline" method="get" action="tiki-searchindex.php">
		<div class="form-group">
			<label class="sr-only" for="filter~content">{tr}Search Query{/tr}</label>
			<input class="form-control" type="search" name="filter~content" value="{$filter.content|escape}"/>

			{foreach from=$facets item=facet}
				<input type="hidden" name="filter~{$facet|escape}" value="{$filter[$facet]|escape}"/>
			{/foreach}
		</div>
		<input type="submit" class="btn btn-primary" value="{tr}Search{/tr}"/>

		{if $prefs.storedsearch_enabled eq 'y' and $user}
			<input type="hidden" name="storeAs" value=""/>
			<a href="{service controller=search_stored action=select modal=true}" id="store-query" class="btn btn-default">{tr}Save Search{/tr}</a>
			<a href="{service controller=search_stored action=list}" class="btn btn-link">{tr}View Saved Searches{/tr}</a>
			{jq}
				$('#store-query').clickModal({
					success: function (data) {
						var form = $(this).closest('form')[0];

						$(form.storeAs).val(data.queryId);
						$(form).attr('method', 'post');
						$(form).submit();
					}
				});
			{/jq}
		{/if}
	</form>
</div><!--nohighlight-->
	{* do not change the comment above, since smarty 'highlight' outputfilter is hardcoded to find exactly this... instead you may experience white pages as results *}

{if isset($results)}
	{$results}
{/if}

<div class="clearfix"></div>
{/block}
