{if $plugins_alias|@count}
	<div class="button2">
		<h2>{tr}Available Alias{/tr}</h2>
		<form method="post" action="tiki-admin.php?page=plugins">
			{foreach from=$plugins_alias item=name}
				{assign var=full value='wikiplugin_'|cat:$name}
				<input type="checkbox" name="enabled[]" value="{$name|escape}" {if $prefs[$full] eq 'y'}checked="checked"{/if}/>
				<a href="tiki-admin.php?page=plugins&amp;plugin={$name|escape}">{$name|escape}</a>
			{/foreach}
			<div>
				<input type="submit" name="enable" value="{tr}Enable Plugins{/tr}"/>
			</div>
		</form>
	</div>
{/if}

	{if $plugin}
		<div class="button2">
			<a href="tiki-admin.php?page=plugins">{tr}New{/tr}</a>
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
				<td rowspan="4"><input type="text" name="input[{$token|escape}][token]" value="{if $token neq '__NEW__'}{$token|escape}{/if}" size="10"/></td>
				<td>{tr}Name{/tr}</td>
				<td><input type="text" name="input[{$token|escape}][name]" value="{$detail.name|escape}"/></td>
			</tr>
			<tr>
				<td>{tr}Description{/tr}</td>
				<td><input type="text" name="input[{$token|escape}][description]" value="{$detail.description|escape}"/></td>
			</tr>
			<tr>
				<td>{tr}Required{/tr}</td>
				<td colspan="2"><input type="checkbox" name="input[{$token|escape}][required]" value="y"{if $detail.required} checked="checked"{/if}/></td>
			</tr>
			<tr>
				<td>{tr}Safe{/tr}</td>
				<td><input type="checkbox" name="input[{$token|escape}][safe]" value="y"{if $detail.safe} checked="checked"{/if}/></td>
			</tr>
		{/foreach}

		<tr><th colspan="3">{tr}Plugin Body{/tr}</th></tr>

		<tr>
			<td>{tr}Ignore User Input{/tr}</td>
			<td colspan="2"><input type="checkbox" name="ignorebody" value="y" {if $plugin.body.input eq 'ignore'}checked="checked"{/if}/></td>
		</tr>
		<tr>
			<td>{tr}Default Content{/tr}</td>
			<td colspan="2"><textarea name="defaultbody">{$plugin.body.default|escape}</textarea></td>
		</tr>

		<tr>
			<td></td>
			<th colspan="2">{tr}Parameters{/tr}</th>
		</tr>

		{foreach from=$plugin.body.params key=token item=detail}
			<tr>
				<td></td>
				<td colspan="2"><input type="text" name="bodyparam[{$token|escape}][token]" value="{if $token neq '__NEW__'}{$token|escape}{/if}" size="10"/></td>
			</tr>
			<tr>
				<td></td>
				<td>{tr}Encoding{/tr}</td>
				<td>
					<select name="bodyparam[{$token|escape}][encoding]">
						{foreach from=','|explode:'none,html,url' item=val}
							<option value="{$val|escape}" {if $detail.encoding eq $val}selected="selected"{/if}>{$val|escape}</option>
						{/foreach}
					</select>
				</td>
			</tr>
			<tr>
				<td></td>
				<td>{tr}Argument Source (if different){/tr}</td>
				<td><input type="text" name="bodyparam[{$token|escape}][input]" value="{$detail.input|escape}"/></td>
			</tr>
			<tr>
				<td></td>
				<td>{tr}Default Value{/tr}</td>
				<td><input type="text" name="bodyparam[{$token|escape}][default]" value="{$detail.default|escape}"/></td>
			</tr>
		{/foreach}

		<tr><th colspan="3">{tr}Simple Plugin Arguments{/tr}</th></tr>

		{foreach from=$plugin.params key=token item=value}
			{if ! $value|is_array}
			<tr>
				<td><input type="text" name="sparams[{$token|escape}][token]" value="{$token|escape}" size="10"/></td>
				<td>{tr}Default{/tr}</td>
				<td><input type="text" name="sparams[{$token|escape}][default]" value="{$value|escape}"/></td>
			</tr>
			{/if}
		{/foreach}
		<tr>
			<td><input type="text" name="sparams[__NEW__][token]" value="" size="10"/></td>
			<td>{tr}Default{/tr}</td>
			<td><input type="text" name="sparams[__NEW__][default]" value=""/></td>
		</tr>

		<tr><th colspan="3">{tr}Composed Plugin Arguments{/tr}</th></tr>

		{foreach from=$plugin.params key=token item=detail}
			{if $detail|is_array}
				<tr>
					<td><input type="text" name="cparams[{$token|escape}][token]" value="{if $token neq '__NEW__'}{$token|escape}{/if}" size="10"/></td>
					<td>{tr}Pattern{/tr}</td>
					<td><input type="text" name="cparams[{$token|escape}][pattern]" value="{$detail.pattern|escape}"/></td>
				</tr>
				<tr>
					<td></td>
					<th colspan="2">{tr}Parameters{/tr}</th>
				</tr>

				{foreach from=$detail.params key=t item=d}
					<tr>
						<td></td>
						<td colspan="2"><input type="text" name="cparams[{$token|escape}][params][{$t|escape}][token]" value="{if $t neq '__NEW__'}{$t|escape}{/if}" size="10"/></td>
					</tr>
					<tr>
						<td></td>
						<td>{tr}Encoding{/tr}</td>
						<td>
							<select name="cparams[{$token|escape}][params][{$t|escape}][encoding]">
								{foreach from=','|explode:'none,html,url' item=val}
									<option value="{$val|escape}" {if $d.encoding eq $val}selected="selected"{/if}>{$val|escape}</option>
								{/foreach}
							</select>
						</td>
					</tr>
					<tr>
						<td></td>
						<td>{tr}Argument Source (if different){/tr}</td>
						<td><input type="text" name="cparams[{$token|escape}][params][{$t|escape}][input]" value="{$d.input|escape}"/></td>
					</tr>
					<tr>
						<td></td>
						<td>{tr}Default Value{/tr}</td>
						<td><input type="text" name="cparams[{$token|escape}][params][{$t|escape}][default]" value="{$d.default|escape}"/></td>
					</tr>
				{/foreach}
			{/if}
		{/foreach}

		<tr>
			<td colspan="3"><input type="submit" name="save" value="{tr}Save{/tr}"/></td>
		</tr>
	</table>
</form>
