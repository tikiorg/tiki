{extends "layout_view.tpl"}
{block name=title}
	{title help="Friendship Network"}{tr}Friendship Network{/tr}{/title}
{/block}
{block name=content}
	{wikiplugin _name=activitystream}
		{literal}
		{filter personalize=follow}
		{group}
		{/literal}
	{/wikiplugin}
{/block}

