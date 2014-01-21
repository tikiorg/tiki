{if $yaml}
	<div class="navbar">
		{include file="tracker_actions.tpl"}
	</div>
	{remarksbox type="note" title="{tr}YAML Export{/tr}"}
		<p>{tr _0=$trackerId}Profile for trackerId %0{/tr}</p>
	{/remarksbox}

	{$yaml}
	
{/if}
