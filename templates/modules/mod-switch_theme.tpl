{* $Id$ *}
{if !isset($tpl_module_title)}
	{capture assign=tpl_module_title}{tr}Switch Theme{/tr}
		{if not $switchtheme_enabled}
			{icon name="information" class="tips btn btn-sm btn-link" title=$info_title}
		{/if}
	{/capture}
{/if}
{tikimodule error=$module_params.error title=$tpl_module_title name="switch_theme" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
	<form method="get" action="tiki-switch_theme.php">
		<fieldset {if not $switchtheme_enabled}disabled{/if}>
			<div class="form-group">
				<select name="theme" onchange="this.form.submit();" class="form-control">
					<option value="" class="text-muted bg-info">{tr}Site theme{/tr} ({$prefs.site_theme}{if !empty($prefs.site_theme_option)}/{$prefs.site_theme_option}{/if})</option>
					{foreach from=$available_themes key=value item=label}
						<option value="{$value|escape}" {if $prefs.theme eq $value}selected="selected"{/if}>{$label|ucwords}</option>
					{/foreach}
				</select>
			</div>
			{if count($available_options)}
				<div class="form-group">
					<select name="theme_option" onchange="this.form.submit();" class="form-control">
						<option value="" class="text-muted bg-info">{tr}None{/tr}</option>
						{foreach from=$available_options key=value item=label}
							<option value="{$value|escape}" {if $prefs.theme_option eq $value}selected="selected"{/if}>{$label|ucwords}</option>
						{/foreach}
					</select>
				</div>
			{else}
				<input type="hidden" name="theme_option" value="">
			{/if}
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