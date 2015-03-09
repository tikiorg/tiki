{* $Id$ *}
{strip}
	{tikimodule error=$module_params.error title=$tpl_module_title name="logo" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
		<div {if $module_params.bgcolor ne ''} style="background-color: {$module_params.bgcolor};" {/if} class="pull-left {$module_params.class_image|escape}">
			{if $module_params.src}
                <a href="{$module_params.link}" title="{$module_params.title_attr|escape}">
					<img src="{$module_params.src}" alt="{$module_params.alt_attr|escape}" style="width: 100%; height: auto">
				</a>
			{/if}
		</div>
		{if !empty($module_params.sitetitle) or !empty($module_params.sitesubtitle)}
			<div class="pull-left {$module_params.class_titles|escape}">
				{if !empty($module_params.sitetitle)}
                    <h1 class="sitetitle">
						<a href="{$module_params.link}">
							{tr}{$module_params.sitetitle|escape}{/tr}
						</a>
                    </h1>
				{/if}
                {if !empty($module_params.sitesubtitle)}
    				<h2 class="sitesubtitle">{tr}{$module_params.sitesubtitle|escape}{/tr}</h2>
                {/if}
			</div>
		{/if}
	{/tikimodule}
{/strip}
