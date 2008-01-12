{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-admin_integrator.tpl,v 1.31.2.1 2008-01-12 13:07:43 pkdille Exp $ *}

<h1>
  <a class="pagetitle" href="tiki-admin_integrator.php">{tr}Integrator{/tr}</a>

  {if $prefs.feature_help eq 'y'}
    <a href="http://tikiwiki.org/tiki-index.php?page=TikiIntegrator" target="tikihelp" class="tikihelp" title="{tr}Tiki integrator{/tr}">
      <img src="img/icons/help.gif" border="0" height="16" width="16" alt='{tr}Help{/tr}' />
    </a>
  {/if}
</h1>

<div class="rbox" name="tip">
  <div class="rbox-title" name="tip">{tr}Tip{/tr}</div>  
  <div class="rbox-data" name="tip">
    {tr}An easier way to integrate content from another site into Tiki is via iframed links using Tiki's <a class="rbox-link" href="tiki-admin_links.php">featured links</a> feature.{/tr}
  </div>
</div>

<br />

{if $repID > 0}
    <h2>{tr}Edit Repository:{/tr} {$name}</h2>
{else}
    <h2>{tr}Create New Repository{/tr}</h2>
{/if}
<div id="page-bar">
  <table><tr>
    <td><div class="button2">
      <a href="tiki-list_integrator_repositories.php" class="linkbut">{tr}List Repositories{/tr}</a>
    </div></td>
    <td><div class="button2">
      <a href="tiki-admin_integrator.php" class="linkbut">{tr}New Repository{/tr}</a>
    </div></td>
    {if isset($repID) and $repID ne '0'}
    <td><div class="button2">
      <a href="tiki-integrator.php?repID={$repID|escape}" class="linkbut">{tr}View Repository{/tr}</a>
    </div></td>
    {/if}
  </tr></table>
</div>
<br />

{* Add form *}
<form action="tiki-admin_integrator.php" method="post">
<input type="hidden" name="repID" value="{$repID|escape}" />
<table class="normal">
  <tr><td class="formcolor"><span title="{tr}Human readable repository name{/tr}">{tr}Name{/tr}</span></td>
    <td class="formcolor"><input type="text" name="name" value="{$name|escape}" title="{tr}Human readable repository name{/tr}" /></td>
  </tr><tr>
    <td class="formcolor"><span title="{tr}Path to repository (local filesystem: relative/absolute web root, remote: prefixed with 'http://'){/tr}">{tr}Path{/tr}</span></td>
    <td class="formcolor"><input type="text" name="path" value="{$path|escape}" title="{tr}Path to repository (local filesystem: relative/absolute web root, remote: prefixed with 'http://'){/tr}" /></td>
  </tr><tr>
    <td class="formcolor"><span title="{tr}File name of start page{/tr}">{tr}Start page{/tr}</span></td>
    <td class="formcolor"><input type="text" name="start" value="{$start|escape}" title="{tr}File name of start page{/tr}" /></td>
  </tr><tr>
    <td class="formcolor"><span title="{tr}CSS file to load when browse this repository{/tr}">{tr}CSS file{/tr}</span></td>
    <td class="formcolor"><input type="text" name="cssfile" value="{$cssfile|escape}" title="{tr}CSS file to load when browse this repository{/tr}" /></td>
  </tr><tr>
    <td class="formcolor"><span title="{tr}Is repository visible to users{/tr}">{tr}Visible{/tr}</span></td>
    <td class="formcolor"><input type="checkbox" name="vis" {if $vis eq 'y'}checked="checked"{/if} title="{tr}Is repository visible to users{/tr}" /></td>
  </tr><tr>
    <td class="formcolor"><span title="{tr}Can files from repository be cached{/tr}">{tr}Cacheable{/tr}</span></td>
    <td class="formcolor">
      <input type="checkbox" name="cacheable" {if $cacheable eq 'y'}checked="checked"{/if} title="{tr}Are files from repository can be cached{/tr}" />
      {if isset($repID) and $repID ne '0'}
        &nbsp;&nbsp;
        <a href="tiki-admin_integrator.php?action=clear&amp;repID={$repID|escape}" title="{tr}Clear all cached pages of this repository{/tr}">
          {tr}Clear cache{/tr}
        </a>
      {/if}
    </td>
  </tr><tr>
    <td class="formcolor"><span title="{tr}Seconds count 'till cached page will be expired{/tr}">{tr}Cache expiration{/tr}</span></td>
    <td class="formcolor"><input type="text" maxlength="14" size="14" name="expiration" value="{$expiration|escape}" title="{tr}Seconds count 'till cached page will be expired{/tr}" /></td>
  </tr><tr>
    <td class="formcolor"><span title="{tr}Human readable text description of repository{/tr}">{tr}Description{/tr}</span></td>
    <td class="formcolor"><textarea name="description" rows="4" title="{tr}Human readable text description of repository{/tr}">{$description|escape}</textarea></td>
  </tr><tr>
    <td class="formcolor"></td>
    <td class="formcolor"><input type="submit" name="save" value="{tr}Save{/tr}" /></td>
  </tr>
</table>
</form>

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
        <a href="tiki-admin_integrator_rules.php?repID={$repositories[rep].repID|escape}" title="{tr}Edit rules{/tr}">
          {$repositories[rep].name}
        </a>
      </td>
      <td class="{cycle advance=false}">{$repositories[rep].path}</td>
      <td class="{cycle advance=false}">{$repositories[rep].start_page}</td>
      <td class="{cycle advance=false}">{$repositories[rep].css_file}</td>
      <td class="{if (strlen($repositories[rep].description) > 0)}{cycle advance=false}{else}{cycle}{/if}">
        <a href="tiki-admin_integrator.php?action=edit&amp;repID={$repositories[rep].repID|escape}" title="{tr}Edit{/tr}">
            <img src="pics/icons/wrench.png" alt="{tr}Edit{/tr}" border="0" width="16" height="16" />
        </a>
        &nbsp;&nbsp;<a href="tiki-admin_integrator.php?action=rm&amp;repID={$repositories[rep].repID|escape}"  
		title="{tr}Delete{/tr}"><img alt="{tr}Delete{/tr}" src="pics/icons/cross.png" border="0" width="16" height="16" /></a>&nbsp;&nbsp;
      </td>

    {* Show description as colspaned row if it is not an empty *}
    {if (strlen($repositories[rep].description) > 0)}
    </tr><tr>
      <td class="{cycle}" colspan="4">{$repositories[rep].description}</td>
    {/if}
    </tr>
  {/section}
</table>
<br /><br />
