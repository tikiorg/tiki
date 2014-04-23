{*$Id$*}

{foreach from=$icons key=page item=info}
	{if ! $info.disabled and $info.icon}
		{self_link _icon=$info.icon _icon_class="reflect" _width="32" _height="32" _alt=$info.title page=$page _class="icon tips bottom" _title="`$info.title`|`$info.description`"}{/self_link}
	{/if}
{/foreach}
