{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-admin_integrator.tpl,v 1.1 2003-10-13 17:17:22 zaufi Exp $ *}

{if $repID > 0}
    <h2>{tr}Edit this Repository:{/tr} {$name}</h2>
{else}
    <h2>{tr}Create New Repository:{/tr}</h2>
{/if}
<div id="page-bar">
  <table><tr>
    <td><div class="button2">
      <a href="tiki-list_integrator_repositories.php" class="linkbut">{tr}list repositories{/tr}</a>
    </div></td>
  </tr></table>
</div>
<br />

{* Add form *}
<form action="tiki-admin_integrator.php" method="post">
<input type="hidden" name="repID" value="{$repID|escape}" />
<table class="normal">
  <tr><td class="formcolor">{tr}Name{/tr}</td>
    <td class="formcolor"><input type="text" name="name" value="{$name|escape}" /></td>
  </tr><tr>
    <td class="formcolor">{tr}Path{/tr}</td>
    <td class="formcolor"><input type="text" name="path" value="{$path|escape}" /></td>
  </tr><tr>
    <td class="formcolor">{tr}Start page{/tr}</td>
    <td class="formcolor"><input type="text" name="start" value="{$start|escape}" /></td>
  </tr><tr>
    <td class="formcolor">{tr}CSS file{/tr}</td>
    <td class="formcolor"><input type="text" name="cssfile" value="{$cssfile|escape}" /></td>
  </tr><tr>
    <td class="formcolor">{tr}Description{/tr}</td>
    <td class="formcolor"><textarea name="description" rows="4">{$description|escape}</textarea></td>
  </tr><tr>
    <td class="formcolor"></td>
    <td class="formcolor"><input type="submit" name="save" value="{tr}Save{/tr}" /></td>
  </tr>
</table>

<h2>{tr}Available Repositories{/tr}</h2>

{* Table with list of repositories *}
<table class="normal" id="integrator-repositories">
  <tr>
    <td class="heading" rowspan="2">{tr}Name{/tr}</td>
    <td class="heading">{tr}Path{/tr}</td>
    <td class="heading">{tr}Start{/tr}</td>
    <td class="heading">{tr}CSS File{/tr}</td>
    <td class="heading">{tr}Actions{/tr}</td>
  </tr><tr>
    <td class="heading" colspan="4">{tr}Description{/tr}</td>
  </tr>
  {cycle values="odd,even" print=false}
  {section name=rep loop=$repositories}
    <tr>
      <td class="{cycle advance=false}"{if (strlen($repositories[rep].description) > 0)} rowspan="2"{/if}>
        <a href="tiki-admin_integrator_rules.php?repID={$repositories[rep].repID|escape}" title={tr}Edit rules{/tr}>
          {$repositories[rep].name}
        </a>
      </td>
      <td class="{cycle advance=false}">{$repositories[rep].path}</td>
      <td class="{cycle advance=false}">{$repositories[rep].start_page}</td>
      <td class="{cycle advance=false}">{$repositories[rep].css_file}</td>
      <td class="{if (strlen($repositories[rep].description) > 0)}{cycle advance=false}{else}{cycle}{/if}">
        <a href="tiki-admin_integrator.php?action=edit&repID={$repositories[rep].repID|escape}" title='{tr}edit{/tr}' >
            <img src='img/icons/config.gif' alt='{tr}edit{/tr}' border='0' />
        </a>
        <a href="tiki-admin_integrator.php?action=rm&repID={$repositories[rep].repID|escape}" title='{tr}remove{/tr}' >
            <img src='img/icons2/delete.gif' alt='{tr}remove{/tr}' border='0' />
        </a>
      </td>

    {* Show description as colspaned row if it is not an empty *}
    {if (strlen($repositories[rep].description) > 0)}
    </tr><tr>
      <td class="{cycle}" colspan="4">{$repositories[rep].description}</td>
    {/if}
    </tr>
  {/section}
</table>
