{strip}
{foreach key=tid item=tlabel from=$field.links}
	<div style="clear:both">
		{if $context.list_mode neq 'y'}
		<div style="float:right;text-align:right">
			<a href="tiki-view_tracker_item.php?trackerId={$field.trackerId}&amp;itemId={$tid}" class="link" title="{tr}View Item{/tr}">{icon _id='magnifier' alt="{tr}View Item{/tr}"}</a>
		</div>
		{/if}
		<a href="tiki-view_tracker_item.php?trackerId={$field.trackerId}&amp;itemId={$tid}" class="link" title="{tr}View Item{/tr}">{if $tlabel}{$tlabel}{else}&nbsp;{/if}</a></div>
{/foreach}
{if $context.list_mode neq 'y' and $tiki_p_create_tracker_items eq 'y' and !(count($field.links) >= 1 and $field.tracker_options.oneUserItem eq 'y')}
	<div style="clear:both;text-align:right;">
		<a href="tiki-view_tracker.php?trackerId={$field.options_array[0]}&amp;vals%5B{$field.options_array[1]}%5D=
		{assign var="fieldopts" value="|"|explode:$field.options_array[2]}
		{section name=ox loop=$ins_fields}
			{if $ins_fields[ox].fieldId eq $fieldopts[0]}
				{$ins_fields[ox].value}
			{/if}
		{/section}
		">{tr}Insert New Item{/tr}</a>
	</div>
{/if}
{/strip}