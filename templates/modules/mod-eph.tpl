{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-eph.tpl,v 1.3 2003-08-07 20:56:53 zaufi Exp $ *}

<div class="box">
<div class="box-title">
{include file="modules/module-title.tpl" module_title="<a class=\"cboxtlink\" href=\"tiki-eph.php\">{tr}Ephemerides{/tr}</a>" module_name="eph"}
</div>
<div class="box-data">
{if $modephdata}
<table>
{if $modephdata.filesize}
<tr>
<td text-align="center" class="module"><img alt="image" src="tiki-view_eph.php?ephId={$modephdata.ephId}" /></td>
</tr>
{/if}
<tr>
<td class="module">{$modephdata.textdata}</td>
</tr>
</table>
{/if}
</div>
</div>

