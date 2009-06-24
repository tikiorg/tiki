{title url="tiki-plugins.php" help="Wiki+Plugins"}{tr}Plugin Approval{/tr}{/title}

{remarksbox type="tip" title="{tr}Tip{/tr}"}
{tr}About WikiPlugins and security: Make sure to only grant the "tiki_p_plugin_approve" permission to trusted editors.{/tr} {tr}You can deactivate risky plugins at (<a href="tiki-admin.php?page=textarea">tiki-admin.php?page=textarea</a>).{/tr}
{/remarksbox}


{if $plugin_list}
	<form method="post" action="">
		<p>
			{tr}Plugins pending validation are added to this list the first time they are encountered. Only their <em>signature</em> is preserved. Some of the plugins listed here may no longer be in use in the page originally using them. In this case, it's safe to clear them from this list. They will be added back next time they are encountered. Plugins can be approved or rejected from the page containing them.{/tr}
		</p>
		<ul>
			{foreach from=$plugin_list item=plugin}
				<li>
					<input type="checkbox" name="clear[]" value="{$plugin.fingerprint|escape}" id="{$plugin.fingerprint|escape}"/>
					<label for="{$plugin.fingerprint|escape}">{$plugin.fingerprint|substring:0:20|escape}...</label>
					<p>
						{if $plugin.last_objectType eq 'wiki page'}
							{tr 0=$plugin.last_objectId|sefurl:'wiki page' 1=$plugin.last_objectId|escape }Last seen in wiki page <a href="%0">%1</a>{/tr}
						{else}
							{tr}Seen in unknown object{/tr}
						{/if}
					</p>
				</li>
			{/foreach}
		</ul>
		<p>
			<input type="submit" value="{tr}Clear checked items{/tr}"/>
		</p>
	</form>
{else}
	<p>{tr}No plugin pending approval.{/tr}</p>
{/if}
