{remarksbox type="tip" title="{tr}Tip{/tr}"}
	{tr}Text area (that apply throughout many features){/tr}
{/remarksbox}

<div class="cbox">
	<div class="cbox-title">
		{tr}{$crumbs[$crumb]->description}{/tr}
		{help crumb=$crumbs[$crumb]}
	</div>


	<form action="tiki-admin.php?page=textarea" method="post">
		<table class="admin">
			<tr>
				<td class="form"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Smiley" target="tikihelp" class="tikihelp" title="{tr}Allow Smileys{/tr}">{/if} {tr}Allow Smileys{/tr} {if $prefs.feature_help eq 'y'}</a>{/if} </td>
				<td><input type="checkbox" name="feature_smileys" {if $prefs.feature_smileys eq 'y'}checked="checked"{/if}/></td>
			</tr>
			<tr>
				<td class="form"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}AutoLinks" target="tikihelp" class="tikihelp" title="{tr}AutoLinks{/tr}">{/if} {tr}AutoLinks{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
				<td><input type="checkbox" name="feature_autolinks" {if $prefs.feature_autolinks eq 'y'}checked="checked"{/if}/></td>
			</tr>
			<tr>
				<td class="form"> <label for="general-ext_links">{tr}Open external links in new window{/tr}:</label></td>
				<td><input type="checkbox" name="popupLinks" id="general-ext_links" {if $prefs.popupLinks eq 'y'}checked="checked"{/if}/></td>
			</tr>
			<tr>
				<td class="form"> <label for="quicktags_over_textarea">{tr}Show quicktags over textareas (instead on left side){/tr}:</label></td>
				<td><input type="checkbox" name="quicktags_over_textarea" id="quicktags_over_textarea" {if $prefs.quicktags_over_textarea eq 'y'}checked="checked"{/if}/></td>
			</tr>
			<tr>
				<td class="form"><label for="feature_filegals_manager">{tr}Use File Galleries for images inclusion{/tr}</label></td>
				<td><input type="checkbox" name="feature_filegals_manager" id="feature_filegals_manager" {if $prefs.feature_filegals_manager eq 'y'}checked="checked"{/if}/></td>
			</tr>
			<tr>
				<td class="form">{if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Dynamic+Content" target="tikihelp" class="tikihelp" title="{tr}Dynamic Content System{/tr}">{/if} {tr}Dynamic Content System{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
				<td><input type="checkbox" name="feature_dynamic_content" id="feature_dynamic_content" {if $prefs.feature_dynamic_content eq 'y'}checked="checked"{/if}/></td>
			</tr>
			<tr>
				<td class="form"><label for="feature_comments_post_as_anonymous">{if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Post+Comments+as+Anonymous" target="tikihelp" class="tikihelp" title="{tr}Allow to post comments as Anonymous{/tr}">{/if} {tr}Allow to post comments as Anonymous{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</label></td>
				<td><input type="checkbox" name="feature_comments_post_as_anonymous" id="feature_comments_post_as_anonymous"{if $prefs.feature_comments_post_as_anonymous eq 'y'}checked="checked"{/if}/></td>
			</tr>
			<tr>
				<td class="form"><label for="feature_hotwords"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Hotwords" target="tikihelp" class="tikihelp" title="{tr}Hotwords{/tr}">{/if} {tr}Hotwords{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</label></td>
				<td><input type="checkbox" name="feature_hotwords" id="feature_hotwords" {if $prefs.feature_hotwords eq 'y'}checked="checked"{/if}/></td>
			</tr>
			<tr>
				<td class="form"><label for="feature_hotwords_nw"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Hotwords" target="tikihelp" class="tikihelp" title="{tr}Hotwords in New Windows{/tr}">{/if} {tr}Hotwords in New Windows{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</label></td>
				<td><input type="checkbox" name="feature_hotwords_nw" id="feature_hotwords_nw" {if $prefs.feature_hotwords_nw eq 'y'}checked="checked"{/if}/></td>
			</tr>
			<tr>
				<td class="form"><label for="feature_use_quoteplugin"> {tr}Use Quote plugin rather than &ldquo;&gt;&rdquo; for quoting{/tr}</label></td>
				<td><input type="checkbox" name="feature_use_quoteplugin" id="feature_use_quoteplugin"{if $prefs.feature_use_quoteplugin eq 'y'}checked="checked"{/if}/></td>
			</tr>
			<tr>
				<td class="form"> <label for="default_rows_textarea_wiki">{tr}Default number of rows (wiki){/tr}:</label></td>
				<td><input type="text" name="default_rows_textarea_wiki" id="default_rows_textarea_wiki" value="{$prefs.default_rows_textarea_wiki}" size="4" /></td>
			</tr>
			<tr>
				<td class="form"> <label for="default_rows_textarea_comment">{tr}Default number of rows (comments){/tr}:</label></td>
				<td><input type="text" name="default_rows_textarea_comment" id="default_rows_textarea_comment" value="{$prefs.default_rows_textarea_comment}" size="4" /></td>
			</tr>
			<tr>
				<td class="form"> <label for="default_rows_textarea_forum">{tr}Default number of rows (forum){/tr}:</label></td>
				<td><input type="text" name="default_rows_textarea_forum" id="default_rows_textarea_forum" value="{$prefs.default_rows_textarea_forum}" size="4" /></td>
			</tr>
			<tr>
				<td class="form"> <label for="default_rows_textarea_forumthread">{tr}Default number of rows (forum replies){/tr}:</label></td>
				<td><input type="text" name="default_rows_textarea_forumthread" id="default_rows_textarea_forumthread" value="{$prefs.default_rows_textarea_forumthread}" size="4" /></td>
			</tr>
			<tr>
				<td colspan="2" class="button"><input type="submit" name="textareasetup" value="{tr}Save{/tr}" /></td>
			</tr>
		</table>

		<div class="cbox-title">
			{tr}Plugins{/tr}
		</div>

		<table class="admin">

			{foreach from=$plugins key=plugin item=info}
				<tr>
					<td>
						{assign var=pref value=wikiplugin_$plugin}
						{if in_array( $pref, $info.prefs)}
							<input type="checkbox" name="wikiplugin_{$plugin|escape}" {if $prefs[$pref] eq 'y'}checked="checked"{/if}/>
						{/if}
					</td>
					<td class="form">
						{$info.name|escape}
						<p><small><strong>{$plugin|escape}</strong>: {$info.description|escape}</small></p>
					</td>
				</tr>
			{/foreach}

			<tr>
				<td colspan="2" class="button"><input type="submit" name="textareasetup" value="{tr}Save{/tr}" /></td>
			</tr>
		</table>
	</form>
</div>

