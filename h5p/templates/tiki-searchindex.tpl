{* $Id$ *}
{extends 'layout_view.tpl'}

{block name=title}
	{title help="Search" admpage="search"}{tr}Search{/tr}{/title}
{/block}

{block name=content}
{include file='tiki-searchindex_form.tpl'}
{/block}
