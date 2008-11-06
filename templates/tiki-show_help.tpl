<div class="help" id="tikihelp">
	<div class="help_icon">
		<img onclick="javascript:flip('help_sections');" src="pics/help_icon.png">
	</div>
	<div class="help_sections" id="help_sections" style="display:none">
	{foreach item=help from=$help_sections}
		<div class="help_section_select">
		<img onclick="javascript:flip('{$help.id}');" src="pics/icons/add.png">
		{$help.title}
		</div>
		<div class="help_section" id="{$help.id}" style="display:none">
		{$help.content}
		</div>
	{/foreach}
	</div>
</div>
