<h1><a class="pagetitle" href="tiki-admin_system.php">{tr}System Admin{/tr}</a>
{if $feature_help eq 'y'}
<a href="http://tikiwiki.org/tiki-index.php?page=SystemAdmin" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}system admin{/tr}">
<img border='0' src='img/icons/help.gif' alt='help' /></a>{/if}
{if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=templates/tiki-admin_system.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}system admin tpl{/tr}">
<img border='0' src='img/icons/info.gif' alt='edit tpl' /></a>{/if}
</h1>
<br/><br/>

<table class="normal">
<tr><td colspan="3" class="heading">{tr}Exterminator{/tr}</td></tr>
<tr class="formcolor">
<td class="form"><b>./templates_c/</b></td>
<td class="form"><a href="tiki-admin_system.php?do=templates_c" class="link">{tr}Empty{/tr}</a></td>
<td class="form">({$templates_c.cant} {tr}files{/tr} / {$templates_c.total|kbsize})</td>
</tr>
<tr class="formcolor">
<td class="form"><b>./modules/cache/</b></td>
<td class="form"><a href="tiki-admin_system.php?do=modules_cache" class="link">{tr}Empty{/tr}</a></td>
<td class="form">({$modules.cant} {tr}files{/tr} / {$modules.total|kbsize})</td>
</tr>
</table>
