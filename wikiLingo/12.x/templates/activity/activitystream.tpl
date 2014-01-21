{* note please do not remove ol and li as these are needed for "Show More" button to work *}
<ol>
	{foreach from=$results item=activity}
		<li>{activity info=$activity}</li>
	{foreachelse}
		<li class="invalid">{tr}No activity for you.{/tr}</li>
	{/foreach}
</ol>
