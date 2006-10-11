{*
@author: Javier Reyes Gomez (jreyes@escire.com)
@date: 27/01/2006
@copyright (C) 2006 Javier Reyes Gomez (eScire.com)
@license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*}
{if $zones}
	<div class="zoneTabs">
	{foreach key=key item=zone from=$zones}
		{if $activeZone.zoneId == $key}
			<div class="activetabbut"><img src='images/workspaces/redarrow.gif' align="bottom" border='0'/><a href="tiki-workspaces_desktop.php?workspaceId={$workspace.workspaceId}&zoneId={$key}" class="activetablink"> {tr}{$zone.name}{/tr} </a></div>
		{else}
			<div class="noactivetabbut"><img src='images/workspaces/grayarrow.gif' align="bottom" border='0'/><a href="tiki-workspaces_desktop.php?workspaceId={$workspace.workspaceId}&zoneId={$key}" class="noactivetablink"> {tr}{$zone.name}{/tr} </a></div>
		{/if}
	{/foreach}
	</div>
{/if}
<div class="workspace_desktop">
<div class="desktopTitle">({$workspace.code}) {$workspace.name}</div>
<div class="desktopDesc">
{$workspace.description}
<br/><br/>
<b>{tr}Workspace path:{/tr}</b>
{section name=i loop=$path}
<a href="./tiki-workspaces_desktop.php?workspaceId={$path[i].workspaceId}">{$path[i].code}</a>/{/section}
<br/>
</div>
{foreach key=keygroups item=modules from=$modulegroups}
  <table id="workspace_desk_table"  >
    <tr>
    {foreach key=key item=column from=$modules}
      {if $column}
	      <td id="workspace_column" valign="top">
	      {section name=homeix loop=$column}
	      {$column[homeix].data}
	      {/section}
	      </td>
	  {/if}
     {/foreach}
    </tr>
{/foreach}
</table>
</div>