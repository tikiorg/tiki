{tikimodule title="{tr}Top users{/tr}" name="users_rank"}

<table>
{section loop=$users_rank name=u}
  <tr>
    <td>{$users_rank[u].position})&nbsp;</td>
    <td>{$users_rank[u].score}</td>
    <td>&nbsp;{$users_rank[u].login|userlink}</td>
  </tr>
{/section}
</table>

{/tikimodule}
