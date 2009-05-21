{* $Id$ *}

<b>{$plugin.name|escape}</b>

{if $prefs.javascript_enabled eq 'y' && $area_name}
	<a href="javascript:void(0);{if $prefs.feature_shadowbox eq 'y' and ($prefs.feature_jquery eq 'y' or $prefs.feature_mootools eq 'y')}javascript:Shadowbox.close();{/if}" onclick="needToConfirm=false;popup_plugin_form('{$area_name}','{$plugin_name|lower|@addslashes}')">{icon _id="plugin_add" text="{tr}Insert{/tr}"}</a>
{/if}

{if $prefs.feature_help eq 'y'}
	{if $plugin.documentation}
		<a href="{$plugin.documentation|escape}" onclick="needToConfirm=false;" target="tikihelp" class="tikihelp">{icon _id=help}</a>
	{/if}
{/if}


<div class="plugin-desc">
	{$plugin.description}
</div>

{if $prefs.javascript_enabled eq 'y'}
{else}
<div class="plugin-sample">
	{if $plugin.body}
		&#123;{$plugin_name}(
		{foreach key=name item=param from=$plugin.params}
			<div class="plugin-param">
				{if $param.required}
					{$name}=<em>"{$param.description|escape}"</em>
				{else}
					[ {$name}=<em>"{$param.description|escape}"</em> ]
				{/if}
			</div>
		{/foreach}
		)&#125;
		<div class="plugin-param">
			{$plugin.body}
		</div>
		&#123;{$plugin_name}&#125;
	{else}
		&#123;{$plugin_name|@lower}
		{foreach key=name item=param from=$plugin.params}
			<div class="plugin-param">
				{if $param.required}
					{$name}=<em>"{$param.description|escape}"</em>
				{else}
					[ {$name}=<em>"{$param.description|escape}"</em> ]
				{/if}
			</div>
		{/foreach}
		&#125;
	{/if}
</div>
{/if}
