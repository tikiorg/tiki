{* $Id$ *}
<!-- *** footer start *** -->
{if (! isset($display) or $display eq '')}
{if count($phpErrors)}
<span class="button2"><a href="#" onclick="flip('errors');return false;" class="linkbut">{tr}Show php error messages{/tr}</a></span><br />
<div id="errors" style="display:{if isset($smarty.session.tiki_cookie_jar.show_errors) and $smarty.session.tiki_cookie_jar.show_errors eq 'y'}block{else}none{/if};">
{foreach item=err from=$phpErrors}{$err}{/foreach}
</div>
{/if}

{if $tiki_p_admin eq 'y' and $prefs.feature_debug_console eq 'y'}
  {* Include debugging console. Note it shoudl be processed as near as possible to the end of file *}

  {php}  include_once("tiki-debug_console.php"); {/php}
  {include file="tiki-debug_console.tpl"}

{/if}
{if $prefs.feature_phplayers eq 'y'}
{php}global $LayersMenu; if (isset($LayersMenu)) {$LayersMenu->printHeader();$LayersMenu->printFooter();}{/php}
{/if}

{if $lastup}
<div class="cvsup" style="font-size:x-small;text-align:center;color:#999;">{tr}Last update from CVS{/tr}({$prefs.tiki_version}): {$lastup|tiki_long_datetime}</div>
{/if}
{/if}
<!-- *** footer end *** -->