{* $Id$ *}
{title help="mods"}{tr}Tiki Mods Configuration{/tr}{/title}

<div class="navbar">
	{button href="tiki-mods.php" _text="{tr}Mods Install/uninstall{/tr}"}
</div>

{section name=n loop=$tikifeedback}<div class="simplebox{if $tikifeedback[n].num > 0} highlight{/if}">{$tikifeedback[n].mes}</div>{/section}

<form method="post" action="tiki-mods_admin.php">
	<table class="formcolor">
		<tr>
			<td>{tr}Enable Mods providing{/tr}</td>
			<td>
				<input type="checkbox" name="feature_mods_provider" value="on"{if $prefs.feature_mods_provider eq 'y'} checked="checked"{/if} />
			</td>
		</tr>
		<tr>
			<td>{tr}Mods local directory{/tr}</td>
			<td><input type="text" name="mods_dir" value="{$prefs.mods_dir}" size="42" /></td>
		</tr>
		<tr>
			<td>{tr}Mods remote server{/tr}</td>
			<td><input type="text" name="mods_server" value="{$prefs.mods_server}" size="42" /></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type="submit" name="save" value="{tr}Save{/tr}" /></td>
		</tr>
	</table>
</form>

