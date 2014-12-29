{* $Id$ *}
{strip}
	{tikimodule error=$module_params.error title=$tpl_module_title name="logo" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
		<div {if $module_params.bgcolor ne ''} style="background-color: {$module_params.bgcolor};" {/if} class="pull-left {$module_params.class_image|escape}">
			{if $module_params.src}
				<a href="{$module_params.link}" title="{$module_params.title_attr|escape}"{if $prefs.mobile_mode eq "y"} rel="external"{/if}>
					<img src="{$module_params.src}" alt="{$module_params.alt_attr|escape}" style="border: none">
				</a>
			{/if}
		</div>
		{if !empty($module_params.sitetitle) or !empty($module_params.sitesubtitle)}
			<div class="pull-left {$module_params.class_titles|escape}">
				<h1 class="sitetitle">
					{if !empty($module_params.sitetitle)}
						<a href="{$module_params.link}"{if $prefs.mobile_mode eq "y"} rel="external"{/if}>
							{tr}{$module_params.sitetitle|escape}{/tr}
						</a>
					{/if}
				</h1>
				<h2 class="sitesubtitle">{if !empty($module_params.sitesubtitle)}{tr}{$module_params.sitesubtitle|escape}{/tr}{/if}</h2>
			</div>
		{/if}
	{/tikimodule}
{/strip}
