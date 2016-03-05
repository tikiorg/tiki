{title url="tiki-plugins.php" help="Wiki+Plugins"}{tr}Plugin Approval{/tr}{/title}

{remarksbox type="tip" title="{tr}Tip{/tr}"}
	{tr}For security, grant the <strong>tiki_p_plugin_approve</strong> permission only to trusted user groups.{/tr} {tr}Use the <a href="tiki-admin.php?page=textarea">Admin: Text Area page</a> to deactivate potentially risky plugins.{/tr}
{/remarksbox}

<p>
{tr}This page lists the plugins that require validation, the first time they are encountered.{/tr} {tr}Each plugin contains a unique <em>signature</em> that is preserved.{/tr}</p>
<p>{tr}When you upgrade from an old version, you may need to reparse all the pages.{/tr} {button href="tiki-plugins.php?refresh=y" _text="{tr}Refresh{/tr}"}</p>

{if $plugin_list}
	<p>{tr}If a plugin is no longer in use (for example, it has been removed from the wiki page), use <strong>Clear</strong> to remove it from this list.{/tr} {tr}The plugin will automatically be added if it is encountered.{/tr}</p>
	<p>{tr}Plugins can be individually previewed, approved, or rejected from the particular location that contains the plugin.{/tr} {tr}For security, you should review each plugin to ensure it is safe to approve.{/tr}</p>

	{* Use css menus as fallback for item dropdown action menu if javascript is not being used *}
	{if $prefs.javascript_enabled !== 'y'}
		{$js = 'n'}
		{$libeg = '<li>'}
		{$liend = '</li>'}
	{else}
		{$js = 'y'}
		{$libeg = ''}
		{$liend = ''}
	{/if}
	<form method="post" action="#">

		{listfilter selectors='#plugins_list tr.odd,#plugins_list tr.even'}
		<div {if $js === 'y'}class="table-responsive"{/if}>
			<table class="table table-hover table-striped" id="plugins_list">
				<tr>
					<th>{select_all checkbox_names='clear[]'}</th>
					<th>{tr}Plugin{/tr} </th>
					<th>{tr}Location{/tr} </th>
					<th>{tr}Added By{/tr} </th>
					<th></th>
				</tr>
				{foreach name=foo from=$plugin_list item=plugin}
					<tr>
						<td class="checkbox-cell"><input type="checkbox" name="clear[]" value="{$plugin.fingerprint|escape}" id="{$plugin.fingerprint|escape}"></td>
						<td class="text"><label for="{$plugin.fingerprint|escape}"><strong>{$plugin.fingerprint|substring:0:20|escape|replace:"-":"</strong> <br>{tr}Signature:{/tr} "}...</label></td>
						<td class="text">
							{if $plugin.last_objectType eq 'wiki page'}
								{tr _0=$plugin.last_objectId|sefurl:'wiki page' _1=$plugin.last_objectId|escape _2=$plugin.fingerprint}Wiki page: <a href="%0#%2" title="View this page.">%1</a>{/tr}
							{else}
								{tr}Unknown{/tr}
							{/if}
						</td>
						<td class="text">{if $plugin.added_by}{$plugin.added_by|userlink}{else}{tr}Unknown{/tr}{/if}</td>
						<td class="action">
							{capture name='plugin_actions'}
								{strip}
									{$libeg}<a href="tiki-plugins.php?approveone={$plugin.fingerprint}" title="{tr}Approve{/tr}">
										{icon name='ok' _menu_text='y' _menu_icon='y' alt="{tr}Approve{/tr}"}
									</a>{$liend}
									{$libeg}<a href="tiki-plugins.php?clearone={$plugin.fingerprint}" title="{tr}Clear{/tr}">
										{icon name='trash' _menu_text='y' _menu_icon='y' alt="{tr}Clear{/tr}"}
									</a>{$liend}
									{if $plugin.last_objectType eq 'wiki page'}
										{$libeg}<a href="{$plugin.last_objectId|sefurl:'wiki page'}#{$plugin.fingerprint}" title="{tr}View this page{/tr}">{icon name='textfile'  _menu_text='y' _menu_icon='y' alt="{tr}View this page{/tr}"}</a>{$liend}
									{/if}
								{/strip}
							{/capture}
							{if $js === 'n'}<ul class="cssmenu_horiz"><li>{/if}
								<a
									class="tips"
									title="{tr}Actions{/tr}" href="#"
									{if $js === 'y'}{popup fullhtml="1" center=true text=$smarty.capture.plugin_actions|escape:"javascript"|escape:"html"}{/if}
									style="padding:0; margin:0; border:0"
								>
									{icon name='settings'}
								</a>
								{if $js === 'n'}
									<ul class="dropdown-menu" role="menu">{$smarty.capture.plugin_actions}</ul></li></ul>
							{/if}
						</td>
					</tr>
				{/foreach}
			</table>
		</div>

		<p>
			<label for="submit_mult">{tr}Perform action with checked:{/tr}</label>
			<select name="submit_mult" id="submit_mult" onchange="this.form.submit();">
				<option value="" selected="selected">...</option>
				<option value="clear" >Clear</option>
				<option value="approve">Approve</option>
			</select>
			{tr}or{/tr}
			<input type="submit" class="btn btn-default btn-sm" name="approveall" value="{tr}Approve all pending plugins{/tr}">
		</p>
		{remarksbox type="warning" title="{tr}Warning{/tr}"}
			{tr}Using <strong>Approve</strong> or <strong>Approve All</strong> will approve and activate the pending plugins.{/tr} {tr}Use this feature <strong>only</strong> if you have verified that all the pending plugins are safe.{/tr}
		{/remarksbox}

		<script type='text/javascript'>
			<!--
			// Fake js to allow the use of the <noscript> tag (so non-js-users can still submit)
			//-->
		</script>
		<noscript>
			<input type="submit" class="btn btn-default btn-sm" value="{tr}OK{/tr}">
		</noscript>
	</form>
{else}
	<p>{tr}No plugins pending approval.{/tr}</p>
{/if}
