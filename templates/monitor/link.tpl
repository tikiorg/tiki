{if !empty($monitor_link.tag)}
	<{$monitor_link.tag}>
{/if}
<a class="{if isset($monitor_link.class)}{$monitor_link.class}{else}btn btn-link{/if}"
	href="{bootstrap_modal controller=monitor action=object type=$monitor_link.type object=$monitor_link.object}"
	title="{if !empty($monitor_link.title)}{$monitor_link.title}{else}{tr}Notification{/tr}{/if}">
	{icon name="notification"} {if !empty($monitor_link.linktext)}{$monitor_link.linktext}{/if}
</a>
{if !empty($monitor_link.tag)}
	</{$monitor_link.tag}>
{/if}
