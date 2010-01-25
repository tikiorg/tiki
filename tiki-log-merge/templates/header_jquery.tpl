{* $Id$ *}
<script type="text/javascript">
<!--//--><![CDATA[//><!--

{* Object to hold prefs for jq *}
var jqueryTiki = new Object();
jqueryTiki.ui = {if $prefs.feature_jquery_ui eq 'y'}true{else}false{/if};
jqueryTiki.ui_theme = "{$prefs.feature_jquery_ui_theme}";
jqueryTiki.tooltips = {if $prefs.feature_jquery_tooltips eq 'y'}true{else}false{/if};
jqueryTiki.autocomplete = {if $prefs.feature_jquery_autocomplete eq 'y'}true{else}false{/if};
jqueryTiki.superfish = {if $prefs.feature_jquery_superfish eq 'y'}true{else}false{/if};
jqueryTiki.replection = {if $prefs.feature_jquery_reflection eq 'y'}true{else}false{/if};
jqueryTiki.tablesorter = {if $prefs.feature_jquery_tablesorter eq 'y'}true{else}false{/if};
jqueryTiki.colorbox = {if $prefs.feature_shadowbox eq 'y'}true{else}false{/if};
jqueryTiki.cboxCurrent = "{literal}{{/literal}current{literal}} / {{/literal}total{literal}}{/literal}";
jqueryTiki.sheet = {if $prefs.feature_jquery_sheet eq 'y'}true{else}false{/if};
jqueryTiki.carousel = {if $prefs.feature_jquery_carousel eq 'y'}true{else}false{/if};

jqueryTiki.effect = "{$prefs.jquery_effect}";				// Default effect
jqueryTiki.effect_direction = "{$prefs.jquery_effect_direction}";	// 'horizontal' | 'vertical' etc
jqueryTiki.effect_speed = "{$prefs.jquery_effect_speed}";	// 'slow' | 'normal' | 'fast' | milliseconds (int) ]
jqueryTiki.effect_tabs = "{$prefs.jquery_effect_tabs}";		// Different effect for tabs
jqueryTiki.effect_tabs_direction = "{$prefs.jquery_effect_tabs_direction}";
jqueryTiki.effect_tabs_speed = "{$prefs.jquery_effect_tabs_speed}";

//--><!]]>
</script>
<!--  end jquery-tiki -->
