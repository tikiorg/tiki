{* $Id$ *}
{if !isset($tpl_module_title)}
	{capture assign=tpl_module_title}{tr}Switch Theme{/tr} {if !empty($tc_theme) or $group_theme or (($section eq 'admin' or !$section) and $prefs.theme_admin neq '')}{icon name="information" class="tips btn btn-sm btn-link" title="{tr}Not allowed here{/tr}:{tr}Displayed theme{/tr}: {$prefs.theme_active}{if !empty($prefs.theme_option_active)}/{$prefs.theme_option_active}{/if} ({if !empty($tc_theme)}{tr}Theme Control{/tr}{elseif ($section eq 'admin' or !$section) and $prefs.theme_admin neq ''}{tr}Admin Theme{/tr}{elseif $group_theme}{tr}Group Theme{/tr}{/if})"}{/if}{/capture}
{/if}
{tikimodule error=$module_params.error title=$tpl_module_title name="switch_theme" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
	<form method="get" action="tiki-switch_theme.php">
		<fieldset {if !empty($tc_theme) or $group_theme or (($section eq 'admin' or !$section) and $prefs.theme_admin neq '')}disabled{/if}>
			<div class="form-group">
				<select name="theme" size="1" onchange="this.form.submit();" class="form-control">
					{assign var="user_themeoption" value="{$prefs.user_theme}{if $prefs.user_theme_option}/{$prefs.user_theme_option}{/if}"}
					<option value="" class="text-muted bg-info">{tr}Site theme{/tr} ({$prefs.theme_site}{if !empty($prefs.theme_option_site)}/{$prefs.theme_option_site}{/if})</option>
					{foreach from=$available_themesandoptions key=theme item=theme_name}
						<option value="{$theme|escape}" {if $user_themeoption eq $theme}selected="selected"{/if}>{$theme_name|ucwords}</option>
					{/foreach}
				</select>
			</div>
			{if $prefs.themegenerator_feature eq "y"}
				<div class="form-group">
					<select name="theme-themegen" onchange="this.form.submit();" class="form-control">
						<option value="">{tr}None{/tr}</option>
						{section name=ix loop=$themegen_list}
							{if !empty($themegen_list[ix])}
								<option value="{$themegen_list[ix]|escape}"{if $prefs.themegenerator_theme eq $themegen_list[ix]} selected="selected"{/if}>{$themegen_list[ix]|truncate:15|ucwords}</option>
							{/if}
						{/section}
					</select>
				</div>
			{/if}
			<noscript>
				<button type="submit" class="btn btn-default btn-sm">{tr}Switch{/tr}</button>
			</noscript>
		</fieldset>
	</form>
{/tikimodule}