{* $Header: /cvsroot/tikiwiki/tiki/templates/footer.tpl,v 1.2 2003-08-01 10:31:08 redflo Exp $ *}

{*if $tiki_p_admin eq 'y' and $feature_debug_console eq 'y'*}
  {* Include debugging console. Note it shoudl be processed as near as possible to the end of file *}

  {php}  include_once("tiki-debug_console.php"); {/php}
  {include file="tiki-debug_console.tpl"}

{*/if*}
</body>
</html>  
