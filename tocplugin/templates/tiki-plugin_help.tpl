{* $Id$ *}

{if $prefs.javascript_enabled eq 'y' && $area_id}
	<a href="javascript:void(0);" onclick="needToConfirm=false;$.closeModal();popup_plugin_form('{$area_id}','{$plugin_name|lower|@addslashes}');return false;">{icon name=$plugin.iconname|default:"plugin" _text="{tr}Insert{/tr}"}</a>
{/if}
<strong>{$plugin.name|escape}</strong>
<em>{$plugin_name|lower}</em>

{if $prefs.feature_help eq 'y'}
	{if !empty($plugin.documentation)}
		<a href="{$plugin.documentation|escape}" onclick="needToConfirm=false;" target="tikihelp" class="tikihelp">
			{icon name='help'}
		</a>
	{/if}
{/if}


<div class="plugin-desc" style="margin-left:30px">
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
