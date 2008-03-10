{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-admin_system.tpl,v 1.29.2.4 2008-03-10 16:26:29 sylvieg Exp $ *}
<h1><a class="pagetitle" href="tiki-admin_system.php">{tr}System Admin{/tr}</a>

{if $prefs.feature_help eq 'y'}
<a href="{$prefs.helpurl}System+Admin" target="tikihelp" class="tikihelp" title="{tr}system admin{/tr}">
{icon _id='help'}</a>{/if}
{if $prefs.feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-admin_system.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}system admin tpl{/tr}">
{icon _id='shape_square_edit' alt='{tr}Edit template{/tr}'}</a>{/if}</h1>

<div class="rbox" name="tip">
<div class="rbox-title" name="tip">{tr}Tip{/tr}</div>  
<div class="rbox-data" name="tip">{tr}If your Tiki is acting weird, first thing to try is to clear your cache below. Also very important is to clear your cache after an upgrade (by FTP/SSH when needed).{/tr}

</div>
</div>
<br />

<table class="normal">
<tr><td colspan="3" class="heading">{tr}Exterminator{/tr}</td></tr>
<tr class="form">
<td><b>./templates_c/</b></td>
<td><a href="tiki-admin_system.php?do=templates_c" class="link">{tr}Empty{/tr}</a></td>
<td>({$templates_c.cant} {tr}Files{/tr} / {$templates_c.total|kbsize})</td>
</tr>
<tr class="form">
<td><b>./modules/cache/</b></td>
<td><a href="tiki-admin_system.php?do=modules_cache" class="link">{tr}Empty{/tr}</a></td>
<td>({$modules.cant} {tr}Files{/tr} / {$modules.total|kbsize})</td>
</tr>
<tr class="form">
<td><b>./temp/cache/</b></td>
<td><a href="tiki-admin_system.php?do=temp_cache" class="link">{tr}Empty{/tr}</a></td>
<td>({$tempcache.cant} {tr}Files{/tr} / {$tempcache.total|kbsize})</td>
</tr>
<tr class="form">
<td><b>{tr}All user prefs sessions{/tr}</b></td>
<td><a href="tiki-admin_system.php?do=prefs" class="link">{tr}Empty{/tr}</a></td>
<td></td>
</tr>
</table>
{if count($templates)}
<br />
{tr}Templates compiler{/tr}
<table class="sortable" id="templatecompiler" width="100%">
<tr>
<th class="heading">{tr}Language{/tr}</th>
<th class="heading">{tr}Compile{/tr}</th>
<th class="heading">{tr}Pages{/tr}/{tr}Size{/tr}</th>
</tr>
{cycle values="even,odd" print=false}
{foreach key=key item=item from=$templates}
<tr class="form">
<td class="{cycle advance=false}"><b>{$key}</b></td>
<td class="{cycle advance=false}"><a href="tiki-admin_system.php?compiletemplates={$key}" class="link">{tr}Compile{/tr}</a></td>
<td class="{cycle}">({$item.cant} {tr}Files{/tr} / {$item.total|kbsize})</td>
</tr>
{/foreach}
</table>
{/if}
<br />

{if $tiki_p_admin eq 'y'}
<div class="advanced">{tr}Advanced feature{/tr}: {tr}Fix UTF-8 Errors in Tables{/tr}:
<a href="javascript:toggle('fixutf8')">{tr}Show{/tr}/{tr}Hide{/tr}</a>
<br /><br />
<div id="fixutf8" {if $advanced_features ne 'y'}style="display:none;"{else}style="display:block;"{/if}>
<h2>{tr}Fix UTF-8 Errors in Tables{/tr}</h2>
<table class="normal">
<tr><td class="form" colspan="4">{tr}Warning: Make a backup of your Database before using this function!{/tr}</td></tr>
<tr><td class="form" colspan="4">{tr}Warning: If you try to convert large tables, raise the maximum execution time in your php.ini!{/tr}</td></tr>
<tr><td class="form" colspan="4">{tr}This function converts ISO-8859-1 encoded strings in your tables to UTF-8{/tr}</td></tr>
<tr><td class="form" colspan="4">{tr}This may be necessary if you created content with tiki &lt; 1.8.4 and Default Charset settings in apache set to ISO-8859-1{/tr}</td></tr>
<tr><td colspan="4">&nbsp;</td></tr>
{if isset($utf8it)}
<tr><td>{$utf8it}</td><td>{$utf8if}</td><td colspan="2">{$investigate_utf8}</td></tr>
{/if}
{if isset($utf8ft)}
<tr><td>{$utf8ft}</td><td>{$utf8ff}</td><td colspan="2">{$errc} {tr}UTF-8 Errors fixed{/tr}</td></tr>
{/if}
</table>
<table class="sortable" id="tablefix" width="100%">
<tr><th class="heading">{tr}Table{/tr}</th><th class="heading">{tr}Field{/tr}</th><th class="heading">{tr}Investigate{/tr}</th><th class="heading">{tr}Fix it{/tr}</th></tr>
{cycle values="even,odd" print=false}
{foreach key=key item=item from=$tabfields}
<tr><td class="{cycle advance=false}">{$item.table}</td><td class="{cycle advance=false}">{$item.field}</td>
<td class="{cycle advance=false}"><a href="tiki-admin_system.php?utf8it={$item.table}&amp;utf8if={$item.field}" class="link">{tr}Investigate{/tr}</a></td>
<td class="{cycle}"><a href="tiki-admin_system.php?utf8ft={$item.table}&amp;utf8ff={$item.field}" class="link">{tr}Fix it{/tr}</a></td>
</tr>
{/foreach}
</table>
</div>
</div>
{/if}