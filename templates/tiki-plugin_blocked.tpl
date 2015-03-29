<div class="panel panel-danger" id="{$plugin_fingerprint|escape}">
	<div class="panel-heading">
		{icon name='error' style="vertical-align:middle"}
		{if $plugin_status eq 'rejected'}
			{tr}Plugin execution was denied{/tr}
		{else}
			{tr}Plugin execution pending approval{/tr}
		{/if}
	</div>
	<div class="panel-body">
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
				{assign var=thisplugin_name value=$plugin_name|escape}
				{assign var=thisplugin_index value=$plugin_index|escape}
				{button href="javascript:void(0)" _onclick="toggle('sec-$thisplugin_name-$thisplugin_index')" _class="text-right" _text="{tr}View Details{/tr}"}
				<div id="sec-{$plugin_name|escape}-{$plugin_index|escape}" style="display:none">
					<div><h3>{tr}Details:{/tr} {$plugin_name|upper|escape}</h3></div>
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
						<div class="panel panel-warning">
							<div class="panel-heading">
								{tr}Body{/tr}
							</div>
							<div class="panel-body">
								<textarea rows="10" style="width: 99%">{$plugin_body}</textarea>
							</div>
						</div>
					{else}
						<p>{tr}This plugin's body is empty.{/tr}</p>
					{/if}
					<form method="post" action="{$smarty.server.REQUEST_URI|escape}">
						<p>
							<input type="hidden" name="plugin_fingerprint" value="{$plugin_fingerprint|escape}">
							{if $plugin_preview}
								<input type="submit" class="btn btn-default btn-sm" name="plugin_preview" value="{tr}Preview{/tr}">
							{/if}
							{if $plugin_approve}
								<input type="submit" class="btn btn-default btn-sm" name="plugin_accept" value="{tr}Approve{/tr}">
								<input type="submit" class="btn btn-warning btn-sm" name="plugin_reject" value="{tr}Reject{/tr}">
							{/if}
						</p>
					</form>
				</div>
			{/if}
		{/if}
	</div>
</div>
