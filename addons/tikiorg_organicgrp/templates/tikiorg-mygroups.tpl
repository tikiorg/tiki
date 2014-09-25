{foreach item=result from=$results}
	{assign var=grpname value="tikiorg_organicgrp_`$result.object_id`"}
	{if $grpname|in_group}
		{if $result.tracker_status == 'o'}
			{include file="tikiorg-groupsbox.tpl" private="n"}
		{elseif $result.tracker_status == 'p'}
			{include file="tikiorg-groupsbox.tpl" private="y"}
		{/if}
	{/if}
{/foreach}
