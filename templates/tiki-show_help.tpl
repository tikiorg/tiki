{* $Id$ *}
{if $prefs.javascript_enabled eq 'y'}
<div class="help" id="tikihelp">
	<div class="help_icon">
		<img onclick="javascript:flip('help_sections');" src="pics/help_icon.png" alt="{tr}Help{/tr}" title="{tr}Help{/tr}" />
	</div>
	<div class="help_sections" id="help_sections" style="display:none">
{/if}
	{foreach item=help from=$help_sections}
		<div class="help_section_select">
			<img {if $prefs.javascript_enabled eq 'y'}onclick="javascript:flip('{$help.id}');"{/if} src="pics/icons/add.png" />
		{$help.title}
		</div>
		<div {if $prefs.javascript_enabled eq 'y'}class="help_section" id="{$help.id}" style="display:all"{/if}>
		{$help.content}
		</div>
	{/foreach}
{if $prefs.javascript_enabled eq 'y'}
	</div>
</div>
{/if}
