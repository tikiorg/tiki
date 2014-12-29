{foreach $results as $result}
		{if $result@iteration is odd}
			<div class="row">
		{/if}
		<div class="col-md-6 panel panel-default">
			{assign var=grpname value="tikiorg_organicgrp_`$result.object_id`"}
			{include file="tikiorg-groupsbox.tpl" private="{if $result.status == 'o'}n{elseif $result.status == 'p'}y{/if}"}
		</div>
		{if $result@iteration is even}
			</div>
		{/if}
{/foreach}
