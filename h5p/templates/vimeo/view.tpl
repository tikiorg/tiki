{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}
{wikiplugin _name="vimeo" fileId={$file_id} width="100%"}{/wikiplugin}
{/block}
