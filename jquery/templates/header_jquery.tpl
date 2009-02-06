{* $Id: $ *}

<!--  start jquery-tiki -->
<script type="text/javascript" src="lib/jquery/jquery.min.js"></script>
<script type="text/javascript" src="lib/jquery_tiki/tiki-jquery.js"></script>
{if isset($prefs.feature_jquery_ui) and $prefs.feature_jquery_ui eq 'y'}{* TODO optimise so not including all *}
<script type="text/javascript" src="lib/jquery/jquery.ui/ui/jquery.ui.all.js"></script>{/if}

<script type="text/javascript">
<!--//--><![CDATA[//><!--

{* Object to hold prefs for jq *}
var jqueryTiki = new Object();
jqueryTiki.ui = {if isset($prefs.feature_jquery_ui) and $prefs.feature_jquery_ui eq 'y'}true{else}false{/if}; // included UI lib?
jqueryTiki.effect = "{$prefs.jquery_effect}";						// Default effect
jqueryTiki.effect_tabs = "{$prefs.jquery_effect_tabs}";			// Different effect for tabs
jqueryTiki.effect_direction = "{$jquery_effect_direction}";	// 'horizontal' | 'vertical' etc
jqueryTiki.effect_speed = "{$prefs.jquery_effect_speed}";			// 'slow' | 'normal' | 'fast' | milliseconds (int) ]


//--><!]]>
</script>

<!--  end jquery-tiki -->
