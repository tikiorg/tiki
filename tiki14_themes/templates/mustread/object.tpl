{extends "layout_view.tpl"}

{block name="title"}
	{title}{$title}{/title}
{/block}

{block name="content"}
	{if $current|count}
		<h4>{tr}On this document{/tr}</h4>
		<ul class="list-unstyled">
			{foreach $current as $item}
				<li><a href="{service controller=mustread action=list id=$item.object_id}">{$item.title|escape}</a></li>
			{/foreach}
		</ul>
	{/if}
	{if $canAdd}
		<a class="btn btn-default add-mustread-item" href="{service controller=tracker action=insert_item trackerId=$prefs.mustread_tracker forced=$fields}">{icon name="add"} {tr}Add Item{/tr}</a>
	{/if}
{/block}
