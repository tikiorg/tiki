{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}
{tr}New tracker created.{/tr} <a href="tiki-list_trackers.php?find={$name|escape:'url'}">{tr}Return to Trackers{/tr}</a>
{/block}
