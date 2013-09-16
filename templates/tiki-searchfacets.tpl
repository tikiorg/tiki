{* $Id: tiki-searchindex.tpl 42331 2012-07-10 15:05:01Z jonnybradley $ *}
{extends 'layout_view.tpl'}

{block name=title}
	{title help="Search" admpage="search"}{tr}Search{/tr}{/title}
{/block}

{block name=content}
<div class="nohighlight">
	<form id="search-form" method="get" action="tiki-searchindex.php">
		<p>
			<input type="search" name="filter~content" value="{$filter.content|escape}"/>
			<input type="submit" class="btn btn-default" value="{tr}Search{/tr}"/>

			{foreach from=$facets item=facet}
				<input type="hidden" name="filter~{$facet|escape}" value="{$filter[$facet]|escape}"/>
			{/foreach}
		</p>
	</form>
</div><!--nohighlight-->
	{* do not change the comment above, since smarty 'highlight' outputfilter is hardcoded to find exactly this... instead you may experience white pages as results *}

{if isset($results)}
	{$results}
{/if}

<div class="clearfix"></div>
{/block}
