{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-comm_received_objects.tpl,v 1.2 2003-08-07 20:56:53 zaufi Exp $ *}

{if $feature_comm eq 'y'}
<div class="box">
<div class="box-title">
{include file="modules/module-title.tpl" module_title="{tr}Received objects{/tr}" module_name="comm_received_objects"}
</div>
<div class="box-data">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr><td valign="top" class="module">{tr}Pages:{/tr}</td><td class="module">&nbsp;{$modReceivedPages}</td></tr>
</table>
</div>
</div>
{/if}