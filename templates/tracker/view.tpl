<div class="navbar">
	{if $canModify}
		<a class="btn btn-default" href="{service controller=tracker action=update_item trackerId=$trackerId itemId=$itemId modal=1}" data-toggle="modal" data-target="#bootstrap-modal">{icon _id=pencil} {tr}Edit{/tr}</a>
	{/if}
	{include file="tracker_actions.tpl"}
</div>

{trackerfields mode=view trackerId=$trackerId fields=$fields}

