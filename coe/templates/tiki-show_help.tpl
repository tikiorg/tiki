{* $Id$ *}
<div class="help" id="tikihelp">
	<div class="help_sections" id="help_sections" style="display:none">
	{foreach item=help from=$help_sections}
		{$help.content}
	{/foreach}
	{jq}
$jq(function() {
	$jq("#help_sections").accordion({ fillSpace: false, clearStyle: true, collapsible: true, autoHeight: false,  header: 'h3' });
});
	{/jq}
	</div>
</div>
