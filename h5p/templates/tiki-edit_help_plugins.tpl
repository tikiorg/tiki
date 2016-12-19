{* $Id$ *}
{* \brief Show plugins help
 * included by tiki-show_help.tpl via smarty_block_add_help() *}

{if count($plugins) ne 0}

	<h3>{tr}Plugins{/tr}</h3>
	<div class="help_section">
		<p>
			{tr}Note that plugin arguments can be enclosed with double quotes (&quot;); this allows them to contain , or = or &gt;{/tr}.
			<br>
			{if $prefs.feature_help eq 'y'}{tr}More help here{/tr}
				<a href="{$prefs.helpurl}Plugins" target="tikihelp" class="tikihelp" title="{tr}Plugins:{/tr}{tr}Wiki plugins extend the function of wiki syntax with more specialized commands.{/tr}">
					{icon name='help' style="vertical-align:middle"}
				</a>
			{/if}
		</p>

		{if $tiki_p_admin eq 'y'}
			<a href="tiki-admin.php?page=textarea&amp;cookietab=2" target="tikihelp" class="tikihelp">
				{tr}Activate/deactivate plugins{/tr}
			</a>
		{/if}

		{if isset($editorId)}
			{listfilter selectors='#plugins_help_table tr' editorId=$editorId parentTabId="plugin_help"}
		{else}
			{listfilter selectors='#plugins_help_table tr' parentTabId="plugin_help"}
		{/if}
		<table id="plugins_help_table" width="95%" class="formcolor">
			<tr><th>{tr}Description{/tr}</th></tr>

			{section name=i loop=$plugins} {* To modify the template of below: tiki-plugin_help.tpl *}
				<tr>
					<td>
						{if $plugins[i].help eq ''}
							{tr}No description available{/tr}
						{else}
							{$plugins[i].help}
						{/if}
					</td>
				</tr>
			{/section}
		</table>
	</div>
{/if}
