{extends "layout_view.tpl"}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}
	{foreach $result as $activity}
		{activity info=$activity format="summary"}
	{foreachelse}
		<div class="alert alert-success">
			{tr}Nothing left!{/tr}
		</div>
	{/foreach}
	<div class="submit">
		<a class="btn btn-primary" href="{$more_link|escape}">{tr}Show More{/tr}</a>
	</div>
{/block}
