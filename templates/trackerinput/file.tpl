{if $tiki_p_attach_trackers ne 'y'}
	{tr}Permission denied{/tr}
{else}
	<input type="file" name="{$field.ins_id}"{if isset($input_err)} value="{$field.value}"{/if} />
{/if}
{if $field.value ne ''}
	<br />
	{$field.info.filename}&nbsp;
	<a href="tiki-download_item_attachment.php?attId={$field.value}" title="{tr}Download{/tr}">{icon _id='disk' alt="{tr}Download{/tr}"}</a>
	{if ($tiki_p_admin_trackers eq 'y' or $field.info.user eq $user) and $field.isMandatory ne 'y'}
		<a href="{$smarty.server.PHP_SELF}?{query removeattach=$field.value}">{icon _id='cross' alt="{tr}Remove{/tr}"}</a>
	{/if}
{/if}
