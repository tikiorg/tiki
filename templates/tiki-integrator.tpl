{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-integrator.tpl,v 1.4 2003-11-10 03:24:07 zaufi Exp $ *}

<div class="integrated-page">
  {$data}
</div>

<hr />
<div id="page-bar">
  <table><tr>
    {if $cached eq 'y'}
    <td><div class="button2">
      <a href="tiki-integrator.php?repID={$repID|escape}{if strlen($file) gt 0}&file={$file}{/if}&clear_cache" class="linkbut" title="{tr}Clear cached version and refresh cache{/tr}">
        {tr}refresh{/tr}
      </a>
    </div></td>
    {/if}

    <td><div class="button2">
      <a href="tiki-list_integrator_repositories.php" class="linkbut">{tr}list repositories{/tr}</a>
    </div></td>

    {* Show config buttons only for admins *}
    {if $tiki_p_admin eq 'y' or $tiki_p_admin_integrator eq 'y'}
    <td><div class="button2">
       <a href="tiki-admin_integrator_rules.php?repID={$repID|escape}&amp;file={$file|escape}" class="linkbut">{tr}configure rules{/tr}</a>
     </div></td>
     <td><div class="button2">
       <a href="tiki-admin_integrator.php?action=edit&repID={$repID|escape}" class="linkbut">{tr}edit repository{/tr}</a>
     </div></td>
    {/if}

  </tr></table>
</div>
<br />
