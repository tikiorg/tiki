{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-list_integrator_repositories.tpl,v 1.1 2003-10-13 17:17:22 zaufi Exp $ *}

<h2>{tr}Available Repositories{/tr}</h2>

<div id="page-bar">
  <table><tr>
    <td><div class="button2">
      <a href="tiki-admin_integrator.php" class="linkbut">{tr}configure repositories{/tr}</a>
    </div></td>
  </tr></table>
</div>
<br />

{* Table with list of repositories *}
<table class="normal" id="integrator-repositories">
  <tr>
    <td class="heading">{tr}Name{/tr}</td>
    <td class="heading">{tr}Description{/tr}</td>
  </tr>
  {cycle values="odd,even" print=false}
  {section name=rep loop=$repositories}
    <tr>
      <td class="{cycle advance=false}">
        <a href="tiki-integrator.php?repID={$repositories[rep].repID|escape}">
          {$repositories[rep].name}
        </a>
      </td>
      <td class="{cycle}">{$repositories[rep].description}</td>
    </tr>
  {/section}
</table>
