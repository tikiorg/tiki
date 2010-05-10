 
{* $Id$ *}
{if $prefs.feature_use_minified_scripts == 'y'}{assign var=minified value='.min'}{assign var=minidir value='minified'}{else}{assign var=minified value=''}{assign var=minidir value=''}{/if}
<!--  start jquery-tiki -->
<script type="text/javascript" src="lib/jquery/jquery{$minified}.js"></script>
<script type="text/javascript" src="lib/jquery_tiki/tiki-jquery.js"></script>{* add {$minified} later if $minify_scripts_on_the_fly *}

{if $prefs.feature_jquery_ui eq 'y' or $prefs.feature_jquery_tooltips eq 'y' or $prefs.feature_jquery_autocomplete eq 'y' or $prefs.feature_jquery_superfish eq 'y' or $prefs.feature_jquery_reflection eq 'y' or $prefs.feature_jquery_cycle eq 'y' or $prefs.feature_shadowbox eq 'y'}
<script type="text/javascript">
<!--//--><![CDATA[//><!--
// Save $ if it's used for moo {* note: add plugins between this block and the Restore $ *}
{literal}var $old; if ($) {$old = $;} $ = $jq;{/literal}
//--><!]]>
</script>
{if $prefs.feature_jquery_ui eq 'y'}{* TODO optimise so not including all - maybe - one day *}
<script type="text/javascript" src="lib/jquery/jquery-ui/ui/{$minidir}/jquery-ui{$minified}.js"></script>
<link rel="stylesheet" href="lib/jquery/jquery-ui/themes/{$prefs.feature_jquery_ui_theme}/jquery-ui.css" />
{/if}
<script type="text/javascript" src="lib/filegals/file_gallery.js"></script>
{if $prefs.feature_jquery_tooltips eq 'y'}
<script type="text/javascript" src="lib/jquery/cluetip/lib/jquery.hoverIntent.js"></script>
<script type="text/javascript" src="lib/jquery/cluetip/lib/jquery.bgiframe.min.js"></script>
<script type="text/javascript" src="lib/jquery/cluetip/jquery.cluetip.js"></script>
<link rel="stylesheet" href="lib/jquery/cluetip/jquery.cluetip.css" type="text/css" /> 
{/if}
{if $prefs.feature_jquery_autocomplete eq 'y'}
<script type="text/javascript" src="lib/jquery/jquery-autocomplete/lib/jquery.ajaxQueue.js"></script>
{if $prefs.feature_jquery_tooltips neq 'y'}
<script type="text/javascript" src="lib/jquery/jquery-autocomplete/lib/jquery.bgiframe.min.js"></script>
{/if}
<script type="text/javascript" src="lib/jquery/jquery-autocomplete/jquery.autocomplete{$minified}.js"></script>
<link rel="stylesheet" href="lib/jquery/jquery-autocomplete/jquery.autocomplete.css" type="text/css" /> 
{/if}
{if $prefs.feature_jquery_superfish eq 'y'}
<script type="text/javascript" src="lib/jquery/superfish/js/superfish.js"></script>
<script type="text/javascript" src="lib/jquery/superfish/js/supersubs.js"></script> 
{/if}
{if $prefs.feature_jquery_reflection eq 'y'}
<script type="text/javascript" src="lib/jquery/reflection-jquery/js/reflection.js"></script> 
{/if}
{if $prefs.feature_jquery_sheet eq 'y'}
<link rel="stylesheet" href="lib/jquery/jquery.sheet/jquery.sheet.base.css" type="text/css" /> 
<script type="text/javascript" src="lib/jquery/jquery.sheet/jquery.sheet{$minified}.js"></script> 
{/if}
{if $prefs.feature_jquery_tablesorter eq 'y'}
<link rel="stylesheet" href="lib/jquery_tiki/tablesorter/themes/tiki/style.css" type="text/css" /> 
<script type="text/javascript" src="lib/jquery/tablesorter/jquery.tablesorter{$minified}.js"></script>
<script type="text/javascript" src="lib/jquery/tablesorter/addons/pager/jquery.tablesorter.pager.js"></script>
{/if}
{if $prefs.feature_jquery_cycle eq 'y'}
<script type="text/javascript" src="lib/jquery/malsup-cycle/jquery.cycle.all{$minified}.js"></script>
{/if}
{if $prefs.feature_shadowbox eq "y"}
	<!-- Includes for Colorbox script -->
	{if $prefs.feature_use_minified_scripts == 'y'}
		<script type="text/javascript" src="lib/jquery/colorbox/jquery.colorbox-min.js" charset="utf-8"></script>
	{else}
		<script type="text/javascript" src="lib/jquery/colorbox/jquery.colorbox.js" charset="utf-8"></script>
	{/if}
	<link type="text/css" media="screen" rel="stylesheet" href="lib/jquery/colorbox/styles/colorbox.css" />
{/if}
{* small libs on by default *}
<script type="text/javascript" src="lib/jquery/jquery.cookie.js"></script>
<script type="text/javascript" src="lib/jquery/jquery.async.js"></script>
<script type="text/javascript" src="lib/jquery/jquery.columnmanager/jquery.columnmanager{$minified}.js"></script>
<script type="text/javascript" src="lib/jquery/treeTable/src/javascripts/jquery.treeTable{$minified}.js"></script>
<link rel="stylesheet" href="lib/jquery/treeTable/src/stylesheets/jquery.treeTable.css" type="text/css" /> 
<script type="text/javascript">
<!--//--><![CDATA[//><!--
// Restore $
{literal}$jq = $; if ($old) { $ = $old; $old = $jq.undefined };{/literal}
//--><!]]>
</script>
{/if}{* end if $prefs.feature_jquery_ui eq 'y' or $prefs.feature_jquery_tooltips etc *}
<script type="text/javascript">
<!--//--><![CDATA[//><!--

{* Object to hold prefs for jq *}
var jqueryTiki = new Object();
jqueryTiki.ui = {if $prefs.feature_jquery_ui eq 'y'}true{else}false{/if};
jqueryTiki.tooltips = {if $prefs.feature_jquery_tooltips eq 'y'}true{else}false{/if};
jqueryTiki.autocomplete = {if $prefs.feature_jquery_autocomplete eq 'y'}true{else}false{/if};
jqueryTiki.superfish = {if $prefs.feature_jquery_superfish eq 'y'}true{else}false{/if};
jqueryTiki.replection = {if $prefs.feature_jquery_reflection eq 'y'}true{else}false{/if};
jqueryTiki.tablesorter = {if $prefs.feature_jquery_tablesorter eq 'y'}true{else}false{/if};
jqueryTiki.cycle = {if $prefs.feature_jquery_cycle eq 'y'}true{else}false{/if};
jqueryTiki.colorbox = {if $prefs.feature_shadowbox eq 'y'}true{else}false{/if};
jqueryTiki.cboxCurrent = "{literal}{{/literal}current{literal}} / {{/literal}total{literal}}{/literal}";

jqueryTiki.effect = "{$prefs.jquery_effect}";				// Default effect
jqueryTiki.effect_direction = "{$prefs.jquery_effect_direction}";	// 'horizontal' | 'vertical' etc
jqueryTiki.effect_speed = "{$prefs.jquery_effect_speed}";	// 'slow' | 'normal' | 'fast' | milliseconds (int) ]
jqueryTiki.effect_tabs = "{$prefs.jquery_effect_tabs}";		// Different effect for tabs
jqueryTiki.effect_tabs_direction = "{$prefs.jquery_effect_tabs_direction}";
jqueryTiki.effect_tabs_speed = "{$prefs.jquery_effect_tabs_speed}";

//--><!]]>
</script>
<!--  end jquery-tiki -->
