{foreach key=tid item=tlabel from=$field_value.links}
	<div style="clear:both">
		<div style="float:right;text-align:right">
			<a href="tiki-view_tracker_item.php?trackerId={$field_value.trackerId}&amp;itemId={$tid}" class="link" title="{tr}View Item{/tr}">{icon _id='magnifier' alt="{tr}View Item{/tr}"}</a>
		</div>
		<a href="tiki-view_tracker_item.php?trackerId={$field_value.trackerId}&amp;itemId={$tid}" class="link" title="{tr}View Item{/tr}">{if $tlabel}{$tlabel}{else}&nbsp;{/if}</a></div>
{/foreach}
{if $tiki_p_create_tracker_items eq 'y' and !(count($field_value.links) >= 1 and $field_value.tracker_options.oneUserItem eq 'y')}
	<div style="clear:both;text-align:right;">
		<a href="tiki-view_tracker.php?trackerId={$field_value.options_array[0]}&amp;vals%5B{$field_value.options_array[1]}%5D=
		{assign var="fieldopts" value="|"|explode:$field_value.options_array[2]}
		{section name=ox loop=$ins_fields}
			{if $ins_fields[ox].fieldId eq $fieldopts[0]}
				{$ins_fields[ox].value}
			{/if}
		{/section}
		">{tr}Insert New Item{/tr}
	</div>
{/if}



