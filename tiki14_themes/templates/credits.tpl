{* $Id$ *}
{tr}Theme:{/tr} {if $prefs.theme_active}{$prefs.theme_active|ucwords}{else}{$prefs.style|replace:'.css':''|replace:'None':''|ucwords}{if $prefs.style_option} - {$prefs.style_option|replace:'.css':''|replace:'None':''|ucwords}{/if}{/if}
