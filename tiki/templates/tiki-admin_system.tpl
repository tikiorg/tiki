<h1><a class="pagetitle" href="tiki-admin_system.php">{tr}System Admin{/tr}</a>
{if $feature_help eq 'y'}
<a href="http://tikiwiki.org/tiki-index.php?page=SystemAdmin" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}system admin{/tr}">
<img border='0' src='img/icons/help.gif' alt='help' /></a>{/if}
{if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=templates/tiki-admin_system.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}system admin tpl{/tr}">
<img border='0' src='img/icons/info.gif' alt='edit tpl' /></a>{/if}
</h1>
<br/><br/>

<div class="simplebox">
<form action="tiki-admin_system.php" method="post">
<strong>{tr}Empty templates_c{/tr}</strong>
<input type="hidden" name="do" value="templates_c" />
<input type="submit" name="action" value="Kill !"/>
{$templates_c_size|kbsize}
</form>
</div>
