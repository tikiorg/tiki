{*$Id: include_anchors.tpl 36730 2011-08-31 14:54:09Z jonnybradley $*}

{foreach from=$icons key=page item=info}
	{if ! $info.disabled and $info.icon}
		{self_link page=$page _class="icon tips" _title="`$info.title`|`$info.description`"}{icon _id=$info.icon alt=$info.title class="reflect" style="vertical-align: middle" width="32" height="32"}{/self_link}
	{/if}
{/foreach}

<br class="clear" />
