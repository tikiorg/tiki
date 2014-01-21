{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}
{if $plain}
{$content}
{else}
<h5>{object_link type=$type id=$object}</h5>
<div>{$content}</div>
{/if}
{/block}
