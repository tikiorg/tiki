{* $Header: /cvsroot/tikiwiki/tiki/templates/footer.tpl,v 1.3 2004-06-11 05:20:41 mose Exp $ *}

{*if $tiki_p_admin eq 'y' and $feature_debug_console eq 'y'*}
  {* Include debugging console. Note it shoudl be processed as near as possible to the end of file *}

  {php}  include_once("tiki-debug_console.php"); {/php}
  {include file="tiki-debug_console.tpl"}

{*/if*}
{if $lastup}
<div style="font-size:x-small;text-align:center;">{tr}Last update from CVS{/tr}: {$lastup|tiki_long_datetime}</div>
{/if}
</body>
</html>  
