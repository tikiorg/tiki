{if $tabular_actions.canModify}
	<a href="{bootstrap_modal controller=tracker action=update_item trackerId=$tabular_actions.trackerId itemId=$tabular_actions.itemId}">{icon name=edit}<span class="sr-only">{tr}Edit{/tr}</span></a>
{/if}
{if $tabular_actions.canRemove}
	<a class="text-danger" href="{bootstrap_modal controller=tracker action=remove_item trackerId=$tabular_actions.trackerId itemId=$tabular_actions.itemId}">{icon name=delete}<span class="sr-only">{tr}Edit{/tr}</span></a>
{/if}
