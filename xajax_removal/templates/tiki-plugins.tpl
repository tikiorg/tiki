{title url="tiki-plugins.php" help="Wiki+Plugins"}{tr}Plugin Approval{/tr}{/title}

{remarksbox type="tip" title="{tr}Tip{/tr}"}
{tr}For security, grant the <strong>tiki_p_plugin_approve</strong> permission only to trusted user groups{/tr}. {tr}Use the <a href="tiki-admin.php?page=textarea">Admin: Text Area page</a> to deactivate potentially risky plugins{/tr}.
{/remarksbox}
		<p>
		{tr}This page lists the plugins that require validation, the first time they are encountered{/tr}. {tr}Each plugin contains a unique <em>signature</em> that is preserved{/tr}.</p>
{if $plugin_list}
		<p>{tr}If a plugin is no longer in use (for example, it has been removed from the wiki page), use <strong>Clear</strong> to remove it from this list{/tr}. {tr}The plugin will automatically be added if it is encountered{/tr}.
		</p>
		<p>{tr}Plugins can be individually previewed, approved, or rejected from the particular location that contains the plugin{/tr}. {tr}For security, you should review each plugin to ensure it is safe to approve{/tr}.</p>
	<form method="post" action="">
{cycle values="even,odd" print=false}
		
		{listfilter selectors='#plugins_list tr.odd,#plugins_list tr.even'} 
		<table class="normal" id="plugins_list">
			<tr>
				<th>{select_all checkbox_names='clear[]'}</th>
				<th>{tr}Plugin{/tr} </th>
				<th>{tr}Location{/tr} </th>
				<th>{tr}Added By{/tr} </th>
				<th>{tr}Actions{/tr} </th>
			</tr>
{foreach name=foo from=$plugin_list item=plugin}
			<tr class="{cycle}">
				<td style="text-align:center"><input type="checkbox" name="clear[]" value="{$plugin.fingerprint|escape}" id="{$plugin.fingerprint|escape}"/></td>
				<td><label for="{$plugin.fingerprint|escape}"><strong>{$plugin.fingerprint|substring:0:20|escape|replace:"-":"</strong> <br />{tr}Signature:{/tr} "}...</label>
				<td>{if $plugin.last_objectType eq 'wiki page'}
					{tr 0=$plugin.last_objectId|sefurl:'wiki page' 1=$plugin.last_objectId|escape }Wiki page: <a href="%0#{$plugin.fingerprint}" title="{tr}View this page{/tr}.">%1</a>{/tr}
					{else}
					{tr}Unknown{/tr}
					{/if}
				</td>
				<td>{if $plugin.added_by}{$plugin.added_by|userlink}{else}{tr}Unknown{/tr}{/if}
				</td>
				<td>
					<a href="tiki-plugins.php?approveone={$plugin.fingerprint}">{icon _id='accept' alt="{tr}Approve{/tr}"}</a>
					<a href="tiki-plugins.php?clearone={$plugin.fingerprint}">{icon _id='delete' alt="{tr}Clear{/tr}"}</a>
{if $plugin.last_objectType eq 'wiki page'}
{tr 0=$plugin.last_objectId|sefurl:'wiki page' 1=$plugin.last_objectId|escape }<a href="%0#{$plugin.fingerprint}" title="{tr}View this page{/tr}.">{icon _id='page'}</a>{/tr}	
{/if}	
{/foreach}
			</tr>
		</table>
		<p>
		<label for="submit_mult">{tr}Perform action with checked:{/tr}</label>
		<select name="submit_mult" id="submit_mult" onchange="this.form.submit();">
			<option value="" selected="selected">...</option>
			<option value="clear" >Clear</option>
			<option value="approve">Approve</option>
		</select> {tr}or{/tr}
		<input type="submit" name="approveall" value="{tr}Approve all pending plugins{/tr}"/>
		</p>
{remarksbox type="warning" title="{tr}Warning{/tr}"}
{tr}Using <strong>Approve</strong> or <strong>Approve All</strong> will approve and activate the pending plugins{/tr}. {tr}Use this feature <strong>only</strong> if you have verified that all the pending plugins are safe{/tr}.
{/remarksbox}
		
	<script type='text/javascript'>
		<!--
		// Fake js to allow the use of the <noscript> tag (so non-js-users can still submit)
		//-->
	</script>
	<noscript>
		<input type="submit" value="{tr}OK{/tr}" />
	</noscript>

{else}
	<p>{tr}No plugins pending approval.{/tr}</p>
{/if}
