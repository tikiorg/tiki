{extends 'layout_view.tpl'}

{block name="title"}
	<h1>{object_link type=$type id=$object backuptitle="{tr}Information{/tr}"}</h1>
{/block}

{block name="content"}
{if $plain}
{$content}
{else}
<div>{$content}</div>
{/if}
{/block}
