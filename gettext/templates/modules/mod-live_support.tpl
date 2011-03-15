{tikimodule error=$module_params.error title=$tpl_module_title name="live_support" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{if $modsupport > 0}
<a href="#" onclick='javascript:window.open("tiki-live_support_client.php","","menubar=,scrollbars=yes,resizable=yes,height=450,width=300");'><img src="tiki-live_support_server.php?operators_online=1" alt="image" /></a>
{else}
<img src="tiki-live_support_server.php?operators_online=0" width="120" height="45" alt="image" />
{/if}
{if $tiki_p_live_support_admin eq 'y' or $user_is_operator eq 'y'}
<br /><a class="linkmodule" {jspopup href="tiki-live_support_console.php"}>{tr}Open operator console{/tr}</a>
{/if}
{/tikimodule}
