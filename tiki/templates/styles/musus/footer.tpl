{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/musus/footer.tpl,v 1.3 2004-02-02 18:44:22 musus Exp $ *}

{*if $tiki_p_admin eq 'y' and $feature_debug_console eq 'y'*}
  {* Include debugging console. Note it shoudl be processed as near as possible to the end of file *}

  {php}  include_once("tiki-debug_console.php"); {/php}
  {include file="tiki-debug_console.tpl"}

{*/if*}
</body>
</html>  
