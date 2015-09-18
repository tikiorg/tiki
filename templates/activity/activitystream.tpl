{* note please do not remove ol and li as these are needed for "Show More" button to work *}
<ol>
	{foreach from=$results item=activity}
		<li>{activity info=$activity}</li>
	{foreachelse}
		<li class="invalid">{tr}There is no activity to display in this stream.{/tr}</li>
	{/foreach}
</ol>
