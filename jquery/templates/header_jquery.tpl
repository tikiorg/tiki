{* $Id: $ *}

<!--  start jquery-tiki -->
{if !isset($jqdebug)}{assign var=jqdebug value=false}{/if}
{if $jqdebug}<script type="text/javascript" src="lib/jquery/jquery.js"></script>{else}
<script type="text/javascript" src="lib/jquery/jquery.min.js"></script>{/if}
<script type="text/javascript" src="lib/jquery_tiki/tiki-jquery.js"></script>
<script type="text/javascript">
<!--//--><![CDATA[//><!--
// Save $ as it's used for moo
var $old = $; $ = $jq;
//--><!]]>
</script>
{if $prefs.feature_jquery_ui eq 'y'}{* TODO optimise so not including all - maybe *}
{if $jqdebug}<script type="text/javascript" src="lib/jquery/jquery.ui/ui/jquery.ui.all.js"></script>{else}
<script type="text/javascript" src="lib/jquery/jquery.ui/ui/minified/jquery.ui.all.min.js"></script>{/if}
{/if}
{if $prefs.feature_jquery_tooltips eq 'y'}
<script type="text/javascript" src="lib/jquery/cluetip/jquery.dimensions.js"></script>
<script type="text/javascript" src="lib/jquery/cluetip/jquery.hoverIntent.js"></script>
<script type="text/javascript" src="lib/jquery/cluetip/jquery.cluetip.js"></script>
<link rel="stylesheet" href="lib/jquery/cluetip/jquery.cluetip.css" type="text/css" /> 
{/if}
<script type="text/javascript">
<!--//--><![CDATA[//><!--
// Restore $
$jq = $; $ = $old; $old = false;
//--><!]]>
</script>

<script type="text/javascript">
<!--//--><![CDATA[//><!--

{* Object to hold prefs for jq *}
var jqueryTiki = new Object();
jqueryTiki.ui = {if $prefs.feature_jquery_ui eq 'y'}true{else}false{/if};		// included UI lib?
jqueryTiki.tooltips = {if $prefs.feature_jquery_tooltips eq 'y'}true{else}false{/if};	// included clueTip lib?
jqueryTiki.effect = "{$prefs.jquery_effect}";	// Default effect
jqueryTiki.effect_direction = "{$prefs.jquery_effect_direction}";	// 'horizontal' | 'vertical' etc
jqueryTiki.effect_speed = "{$prefs.jquery_effect_speed}";	// 'slow' | 'normal' | 'fast' | milliseconds (int) ]
jqueryTiki.effect_tabs = "{$prefs.jquery_effect_tabs}";	// Different effect for tabs
jqueryTiki.effect_tabs_direction = "{$prefs.jquery_effect_tabs_direction}";
jqueryTiki.effect_tabs_speed = "{$prefs.jquery_effect_tabs_speed}";


//--><!]]>
</script>

<!--  end jquery-tiki -->
