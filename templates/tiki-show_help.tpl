{* $Id$ *}
<div class="help" id="tikihelp">
	<div class="help_sections" id="help_sections" style="display:none">
	{foreach item=help from=$help_sections}
		{$help.content}
	{/foreach}
	{if $prefs.feature_jquery_ui eq "y"}{jq}
$jq(function() {
	$jq("#help_sections").accordion({ fillSpace: false, clearStyle: true, collapsible: true, autoHeight: false,  header: 'h3' });
});
	{/jq}{/if}
	</div>
</div>
