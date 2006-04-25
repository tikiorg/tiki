<div class="desktopTitle">({$workspace.code}) {$workspace.name}</div>
<div class="desktopDesc">
{$workspace.description}
<br/><br/>
<b>{tr}Workspace path:{/tr}</b>
{section name=i loop=$path}
<a href="./aulawiki-workspace_desktop.php?workspaceId={$path[i].workspaceId}">{$path[i].code}</a>/{/section}
<br/>
</div>
  <table id="workspace_desk_table"  >
    <tr>
      <td id="workspace_leftcolumn" valign="top">
      {section name=homeix loop=$workspace_left_modules}
      {$workspace_left_modules[homeix].data}
      {/section}
      </td>
      <td id="workspace_rightcolumn" valign="top">
      {section name=homeix loop=$workspace_right_modules}
      {$workspace_right_modules[homeix].data}
      {/section}
      </td>
    </tr>
    </table>