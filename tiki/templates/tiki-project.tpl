{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-project.tpl,v 1.2 2005-01-22 22:56:24 mose Exp $ *}
{* Tiki Project Template *}

<h3>{$project.projectName}</h3>
{include file=tiki-project_header.tpl}
<br />
<table class="normal">
<tr>
<td valign="top"><div class="cboxdata">{$project.projectDescription}</div><br/>
{tr}Project Created:{/tr} {$project.Created|tiki_long_datetime}</td>
<td valign="top" width="180px"><div class="box">
<div class="box-title">{tr}Project Info{/tr}</div>
<div class="box-data">{tr}Project Admins:{/tr}<br />

{section name=changes loop=$projectadmins}
{if $user and $feature_messages eq 'y' and $tiki_p_messages eq 'y'}
<a class="linkmodule" href="messu-compose.php?to={$projectadmins[changes]}" title="{tr}Send a message to{/tr} {$projectadmins[changes]}"><img src="img/icons/icon_ultima.gif" width="18" height="9" hspace="2" vspace="0" border="0" alt="{tr}Send message{/tr}" /></a>
{/if}
{$projectadmins[changes]|userlink:"linkmodule"}<br />
{/section}
<br />
{tr}Project Members:{/tr} 
<br/>
<a href="tiki-project_members.php?projectId={$projectId}">{tr}View Details{/tr}</a>
</div>
</div>
</td>
</tr>
</table>

