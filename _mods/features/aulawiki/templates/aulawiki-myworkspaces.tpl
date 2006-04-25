<h1>{tr}My Workspaces{/tr}</h1>
</BR>
<table class="normal" width="100%">
    <tr> 
      <td class="heading" width="10%">{tr}Code{/tr}</td>
      <td class="heading" width="50%">{tr}Name{/tr}</td>
      <td class="heading" width="20%">{tr}Type{/tr}</td>
      <td class="heading" width="10%">{tr}Start{/tr}</td>
      <td class="heading" width="10%">{tr}End{/tr}</td>
    </tr>

{foreach from=$userWorkspaces item=userWorkspace}
    {cycle values="odd,even" assign="parImpar"}
    <tr> 
      <td class="{$parImpar}"><a class="link" href="aulawiki-homeasg.php?idAsignatura={$userWorkspace.code}">{$userWorkspace.code}</a></td>
      <td class="{$parImpar}"><a class="link" href="aulawiki-grupo.php?idGrupo={$asgGrp.grupo.nombreGrupo}">{$userWorkspace.name}</a></td>
      <td class="{$parImpar}">{$userWorkspace.typename}</td>
      <td class="{$parImpar}">{$userWorkspace.startDate|date_format:"%m/%e/%Y"}</td>
      <td class="{$parImpar}">{$userWorkspace.endDate|date_format:"%m/%e/%Y"}</td>
    </tr>
{/foreach}

</table>

<br/>