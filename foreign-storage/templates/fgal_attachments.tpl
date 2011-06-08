{* $Id$ *}

<a name="attachments"></a>
{if $tiki_p_wiki_view_attachments == 'y' || $tiki_p_wiki_admin_attachments == 'y' || $tiki_p_wiki_attach_files == 'y'}

	<div
		{if $pagemd5}
			{assign var=cookie_key value="show_attzone$pagemd5"}
			id="attzone{$pagemd5}"
		{else}
			{assign var=cookie_key value="show_attzone"}
			id="attzone"
		{/if}
		{if (isset($smarty.session.tiki_cookie_jar.$cookie_key) and $smarty.session.tiki_cookie_jar.$cookie_key eq 'y')
			or (!isset($smarty.session.tiki_cookie_jar.$cookie_key) and $prefs.w_displayed_default eq 'y')}
			style="display:block;"
		{else}
			style="display:none;"
		{/if}
	>

	{* Generate table if view permissions granted and if count of attached files > 0 *}
	{if ($tiki_p_wiki_view_attachments == 'y' || $tiki_p_wiki_admin_attachments == 'y') && count($files) > 0}
		<fieldset>
			<legend>{tr}Attached files{/tr}</legend>
			{include file='list_file_gallery_content.tpl'}
		</fieldset>
	{/if}

	{if ($tiki_p_wiki_attach_files eq 'y' or $tiki_p_wiki_admin_attachments eq 'y') and $attach_box ne 'n'}
		<form enctype="multipart/form-data" action="tiki-index.php?page={$page|escape:"url"}" method="post">
			{if $page_ref_id}
				<input type="hidden" name="page_ref_id" value="{$page_ref_id}" />
			{/if}
			<table class="formcolor">
				<tr>
					<td>
						<label for="attach-upload">{tr}Upload file:{/tr}</label>
						<input size="16" name="userfile[0]" type="file" id="attach-upload" />
						<label for="attach-comment">{tr}Comment:{/tr}</label><input type="text" name="s_f_attachments-comment" maxlength="250" id="attach-comment" />
						<input type="submit" name="s_f_attachments-upload" value="{tr}Attach{/tr}"/>
						{query _type='form_input' page=$page}
					</td>
				</tr>
			</table>
		</form>
	{/if}
</div>
{/if}
