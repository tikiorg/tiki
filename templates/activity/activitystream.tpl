<ol>
	{foreach from=$results item=activity}
		<li>{activity info=$activity}</li>
	{foreachelse}
		<li>{tr}No activity for you.{/tr}</li>
	{/foreach}
</ol>
