<div class="cbox">
<div class="cbox-title">
{icon _id=error.png style="vertical-align:middle"}
{if $plugin_status eq 'rejected'}
	{tr}Plugin execution was denied{/tr}
{else}
	{tr}Plugin execution pending approval{/tr}
{/if}
</div>
<div class="cbox-data">
{if $plugin_status eq 'rejected'}
	<p>{tr}After argument validation by an editor, the execution of this plugin was denied. This plugin will eventually be removed or corrected.{/tr}</p>
{else}
	<p>{tr}This plugin was recently added or modified. Until an editor of the site validates the parameters, execution will not be possible.{/tr} {if $plugin_details}{tr}You are allowed to:{/tr}{/if}</p>
	{if $plugin_details}
	<ul>
		<li>{tr}View arguments{/tr}</li>
		{if $plugin_preview}<li>{tr}Execute the plugin in preview mode (may be dangerous){/tr}</li>{/if}
		{if $plugin_approve}<li>{tr}Approve the plugin for public execution{/tr}</li>{/if}
	</ul>
	{/if}
	{if $plugin_details}
	<div class="right button2">
		<a href="javascript:toggle('sec-{$plugin_name|escape}-{$plugin_index|escape}')" class="linkbut">{tr}View Details{/tr}</a>
	</div>
	<div id="sec-{$plugin_name|escape}-{$plugin_index|escape}" style="display:none">
		<div><h3>Details</h3></div>
		{if $plugin_args|@count > 0}
			<table>
				{foreach from=$plugin_args key=arg item=val}
				<tr>
					<th>{$arg|escape}</th>
					<td>{$val|escape}</td>
				</tr>
				{/foreach}
			</table>
		{else}
			<p>{tr}This plugin does not contain any arguments.{/tr}</p>
		{/if}

		{if $plugin_body}
			<div class="cbox">
			<div class="cbox-title">
				{tr}Body{/tr}
			</div>
			<div class="cbox-data">
			<textarea rows="10">{$plugin_body|escape}</textarea>
			</div>
		{else}
			<p>{tr}This plugin's body is empty.{/tr}</p>
		{/if}
		<form method="post" action="{$smarty.server.REQUEST_URI|escape}">
			<p>
				<input type="hidden" name="plugin_fingerprint" value="{$plugin_fingerprint|escape}"/>
				<input type="submit" name="plugin_accept" value="{tr}Approve{/tr}"/>
				<input type="submit" name="plugin_reject" value="{tr}Reject{/tr}"/>
			</p>
		</form>
	</div>
	{/if}
{/if}
</div>
</div>
</div>
