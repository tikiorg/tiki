{if $tiki_p_attach_trackers ne 'y'}
	{tr}Permission denied{/tr}
{else}
	<input type="file" name="{$field_value.ins_id}"{if isset($input_err)} value="{$field_value.value}"{/if} />
{/if}
{if $field_value.value ne ''}
	<br />
	{$field_value.info.filename}&nbsp;
	<a href="tiki-download_item_attachment.php?attId={$field_value.value}" title="{tr}Download{/tr}">{icon _id='disk' alt="{tr}Download{/tr}"}</a>
	{if ($tiki_p_admin_trackers eq 'y' or $field_value.info.user eq $user) and $field_value.isMandatory ne 'y'}
		<a href="{$smarty.server.PHP_SELF}?{query removeattach=$field_value.value}">{icon _id='cross' alt="{tr}Remove{/tr}"}</a>
	{/if}
{/if}
