{extends 'layout_view.tpl'}

{block name="title"}
	<h3>{$title}</h3>
{/block}

{block name="content"}
	{foreach from=$plugins key=plugin item=info}
		<fieldset class="margin-bottom-lg">
			<legend>
				{if $info.iconname}{icon name=$info.iconname}{else}{icon name='plugin'}{/if} {$info.name|lower|escape}
			</legend>
			<div class="adminoptionbox">
				<strong>{$plugin|escape}</strong>: {$info.description|default:''|escape}
				{help url="Plugin$plugin"}
			</div>
			{assign var=pref value="wikiplugin_$plugin"}
			{if in_array( $pref, $info.prefs)}
				{assign var=pref_inline value="wikiplugininline_$plugin"}
				{preference name=$pref label="{tr}Enable{/tr}"}
				{preference name=$pref_inline label="{tr}Disable edit plugin icon (make plugin inline){/tr}"}
			{/if}
		</fieldset>
	{/foreach}
{/block}
