{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-live_support.tpl,v 1.6 2003-08-28 20:22:14 sylvieg Exp $ *}

{if $feature_live_support eq 'y'}
<div class="box">
<div class="box-title">
{include file="modules/module-title.tpl" module_title="{tr}Live support{/tr}" module_name="live_support"}
</div>
<div class="box-data" align="center" style="margin:0px;padding:0px;">
{if $modsupport > 0}
<a href='javascript:window.open("tiki-live_support_client.php","","menubar=,scrollbars=yes,resizable=yes,height=450,width=300");'><img border="0" src="tiki-live_support_server.php?operators_online=1" alt="image" /></a>
{else}
<img border="0" src="tiki-live_support_server.php?operators_online=0" alt="image" />
{/if}
{if $tiki_p_live_support_admin eq 'y' or $user_is_operator eq 'y'}
<br /><a class="linkmodule" {jspopup href="tiki-live_support_console.php"}>{tr}Open operator console{/tr}</a>
{/if}
</div>
</div>
{/if}

