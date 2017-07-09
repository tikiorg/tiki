{extends 'layout_view.tpl'}

{block name="title"}
	<h3>{$title}</h3>
{/block}

{block name="content"}
	{foreach from=$plugins key=plugin item=info}
		<fieldset class="margin-bottom-lg">
			<legend>
				{if $info.iconname}{icon name=$info.iconname}{else}{icon name='plugin'}{/if} {tr}{$info.title|escape}{/tr}
			</legend>
			<div class="adminoptionbox">
				<strong>{$plugin|escape}</strong>: {tr}{$info.description|default:''|escape}{/tr}
				{help url="Plugin$plugin"}
			</div>
			{preference name='wikiplugin_'|cat:$plugin label="{tr}Enable{/tr}"}
			{preference name='wikiplugininline_'|cat:$plugin label="{tr}Disable edit plugin icon (make plugin inline){/tr}"}
		</fieldset>
	{/foreach}
{/block}
