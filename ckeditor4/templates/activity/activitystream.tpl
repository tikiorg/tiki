<ol>
	{foreach from=$results item=activity}
		<li>{include file="`$activity.event_type`.tpl"}</li>
	{foreachelse}
		<li>{tr}No activity for you.{/tr}</li>
	{/foreach}
</ol>
