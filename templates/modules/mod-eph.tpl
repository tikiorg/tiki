{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-eph.tpl,v 1.4 2003-11-20 23:49:04 mose Exp $ *}

<div class="box">
<div class="box-title">
{include file="module-title.tpl" module_title="<a class=\"cboxtlink\" href=\"tiki-eph.php\">{tr}Ephemerides{/tr}</a>" module_name="eph"}
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

