<h1><a class="pagetitle" href="tiki-admin_system.php">{tr}Admin System{/tr}</a>
{if $feature_help eq 'y'}
<a href="http://tikiwiki.org/tiki-index.php?page=Backups" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}admin backups{/tr}">
<img border='0' src='img/icons/help.gif' alt='help' /></a>{/if}
{if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=templates/tiki-backup.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}admin admin tpl{/tr}">
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
