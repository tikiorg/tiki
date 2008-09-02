{if $plugins_alias|@count}
	<div class="button2">
		<h2>{tr}Available Alias{/tr}</h2>
		{foreach from=$plugins_alias item=name}
		<a href="tiki-admin.php?page=plugins&amp;plugin={$name|escape}" class="linkbut">{$name|escape}</a>
		{/foreach}
	</div>

	{if $plugin}
		<div class="button2">
			<a href="tiki-admin.php?page=plugins" class="linkbut">{tr}New{/tr}</a>
		</div>
	{/if}
	
	<form method="post" action="tiki-admin.php?page=plugins">
		<table class="normal">
			<tr><th colspan="3">{tr}General Information{/tr}</th></tr>
			<tr>
				<td>{tr}Plugin Name{/tr}</td>
				<td colspan="2">
					{if $plugin}
						<input type="hidden" name="plugin" value="{$plugin.plugin_name|escape}"/>
						{$plugin.plugin_name|escape}
					{else}
						<input type="text" name="plugin"/>
					{/if}
				</td>
			</tr>
			<tr>
				<td>{tr}Base Plugin{/tr}</td>
				<td colspan="2">
					<select name="implementation">
					{foreach from=$plugins_real item=base}
						<option value="{$base|escape}" {if $plugin.implementation eq $base}selected="selected"{/if}>{$base|escape}</option>
					{/foreach}
					</select>
				</td>
			</tr>
			<tr>
				<td>{tr}Name{/tr}</td>
				<td colspan="2"><input type="text" name="name" value="{$plugin.description.name|escape}"/></td>
			</tr>
			<tr>
				<td>{tr}Description{/tr}</td>
				<td colspan="2"><input type="text" name="description" value="{$plugin.description.description|escape}"/></td>
			</tr>
			<tr>
				<td>{tr}Dependencies{/tr}</td>
				<td colspan="2"><input type="text" name="prefs" value="{','|implode:$plugin.description.prefs}"/></td>
			</tr>
			<tr>
				<td>{tr}Validation{/tr}</td>
				<td colspan="2">
					<select name="validate">
					{foreach from=','|explode:'none,all,body,arguments' item=val}
						<option value="{$val|escape}" {if $plugin.description.validate eq $val}selected="selected"{/if}>{$val|escape}</option>
					{/foreach}
					</select>
				</td>
			</tr>

			<tr><th colspan="3">{tr}Plugin Parameter Documentation{/tr}</th></tr>

			{foreach from=$plugin.description.params key=token item=detail}
				<tr>
					<td rowspan="4"><input type="text" name="input[{$token|escape}][token]" value="{$token|escape}"/></td>
					<td>{tr}Name{/tr}</td>
					<td colspan="2"><input type="text" name="input[{$token|escape}][name]" value="{$detail.name|escape}"/></td>
				</tr>
				<tr>
					<td>{tr}Description{/tr}</td>
					<td colspan="2"><input type="text" name="input[{$token|escape}][description]" value="{$detail.description|escape}"/></td>
				</tr>
				<tr>
					<td>{tr}Required{/tr}</td>
					<td colspan="2"><input type="checkbox" name="input[{$token|escape}][required]" value="y"{if $detail.required} checked="checked"{/if}/></td>
				</tr>
				<tr>
					<td>{tr}Safe{/tr}</td>
					<td colspan="2"><input type="checkbox" name="input[{$token|escape}][safe]" value="y"{if $detail.safe} checked="checked"{/if}/></td>
				</tr>
			{/foreach}

			<tr>
				<td colspan="3"><input type="submit" name="save" value="{tr}Save{/tr}"/></td>
			</tr>
		</table>
	</form>

{/if}
