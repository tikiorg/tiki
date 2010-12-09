{* $Id$ *}

{title help="Integrator"}{tr}Integrator{/tr}{/title}

{remarksbox type="tip" title="{tr}Tip{/tr}"}
    {tr}An easier way to integrate content from another site into Tiki is via iframed links using Tiki's <a class="rbox-link" href="tiki-admin_links.php">featured links</a> feature.{/tr}
{/remarksbox}

{if $repID > 0}
    <h2>{tr}Edit Repository:{/tr} {$name}</h2>
{else}
    <h2>{tr}Create New Repository{/tr}</h2>
{/if}

<div class="navbar">
	{button href="tiki-list_integrator_repositories.php" _text="{tr}List Repositories{/tr}"}
	{button href="tiki-admin_integrator.php" _text="{tr}New Repository{/tr}"}
	{if isset($repID) and $repID ne '0'}
		{assign var=thisrepID value=$repID|escape }
		{button href="tiki-integrator.php?repID=$thisrepID" _text="{tr}View Repository{/tr}"}
  {/if}
</div>


{* Add form *}
<form action="tiki-admin_integrator.php" method="post">
<input type="hidden" name="repID" value="{$repID|escape}" />
<table class="formcolor">
  <tr><td><span title="{tr}Human-readable repository name{/tr}">{tr}Name{/tr}</span></td>
    <td><input type="text" name="name" value="{$name|escape}" title="{tr}Human-readable repository name{/tr}" /></td>
  </tr><tr>
    <td><span title="{tr}Path to repository (local filesystem: relative/absolute web root, remote: prefixed with 'http://'){/tr}">{tr}Path{/tr}</span></td>
    <td><input type="text" name="path" value="{$path|escape}" title="{tr}Path to repository (local filesystem: relative/absolute web root, remote: prefixed with 'http://'){/tr}" /></td>
  </tr><tr>
    <td><span title="{tr}File name of start page{/tr}">{tr}Start page{/tr}</span></td>
    <td><input type="text" name="start" value="{$start|escape}" title="{tr}File name of start page{/tr}" /></td>
  </tr><tr>
    <td><span title="{tr}CSS file to load when browse this repository{/tr}">{tr}CSS File{/tr}</span></td>
    <td><input type="text" name="cssfile" value="{$cssfile|escape}" title="{tr}CSS file to load when browse this repository{/tr}" /></td>
  </tr><tr>
    <td><span title="{tr}Is repository visible to users{/tr}">{tr}Visible{/tr}</span></td>
    <td><input type="checkbox" name="vis" {if $vis eq 'y'}checked="checked"{/if} title="{tr}Is repository visible to users{/tr}" /></td>
  </tr><tr>
    <td><span title="{tr}Can files from repository be cached{/tr}">{tr}Cacheable{/tr}</span></td>
    <td>
      <input type="checkbox" name="cacheable" {if $cacheable eq 'y'}checked="checked"{/if} title="{tr}Are files from repository can be cached{/tr}" />
      {if isset($repID) and $repID ne '0'}
        &nbsp;&nbsp;
        <a href="tiki-admin_integrator.php?action=clear&amp;repID={$repID|escape}" title="{tr}Clear all cached pages of this repository{/tr}">
          {tr}Clear cache{/tr}
        </a>
      {/if}
    </td>
  </tr><tr>
    <td><span title="{tr}Seconds count 'till cached page will be expired{/tr}">{tr}Cache expiration{/tr}</span></td>
    <td><input type="text" maxlength="14" size="14" name="expiration" value="{$expiration|escape}" title="{tr}Seconds count 'till cached page will be expired{/tr}" /></td>
  </tr><tr>
    <td><span title="{tr}Human-readable text description of repository{/tr}">{tr}Description{/tr}</span></td>
    <td><textarea name="description" rows="4" title="{tr}Human-readable text description of repository{/tr}">{$description|escape}</textarea></td>
  </tr><tr>
    <td></td>
    <td><input type="submit" name="save" value="{tr}Save{/tr}" /></td>
  </tr>
</table>
</form>

<h2>{tr}Available Repositories{/tr}</h2>

{* Table with list of repositories *}
<table class="normal" id="integrator-repositories">
  <tr>
    <th rowspan="2">{tr}Name{/tr}</th>
    <th>{tr}Path{/tr}</th>
    <th>{tr}Start{/tr}</th>
    <th>{tr}CSS File{/tr}</th>
    <th>{tr}Actions{/tr}</th>
  </tr><tr>
    <th colspan="4">{tr}Description{/tr}</th>
  </tr>
  {cycle values="odd,even" print=false}
  {section name=rep loop=$repositories}
    <tr class="{cycle}">
      <td{if (strlen($repositories[rep].description) > 0)} rowspan="2"{/if}>
        <a href="tiki-admin_integrator_rules.php?repID={$repositories[rep].repID|escape}" title="{tr}Edit rules{/tr}">
          {$repositories[rep].name}
        </a>
      </td>
      <td>{$repositories[rep].path}</td>
      <td>{$repositories[rep].start_page}</td>
      <td>{$repositories[rep].css_file}</td>
      <td>
        <a href="tiki-admin_integrator.php?action=edit&amp;repID={$repositories[rep].repID|escape}" title="{tr}Edit{/tr}">
            {icon _id='wrench' alt="{tr}Edit{/tr}"}
        </a>
        &nbsp;&nbsp;<a href="tiki-admin_integrator.php?action=rm&amp;repID={$repositories[rep].repID|escape}"  
		title="{tr}Delete{/tr}">{icon _id='cross' alt="{tr}Delete{/tr}"}</a>&nbsp;&nbsp;
      </td>

    {* Show description as colspaned row if it is not an empty *}
    {if (strlen($repositories[rep].description) > 0)}
    </tr><tr class="{cycle}">
      <td colspan="4">{$repositories[rep].description}</td>
    {/if}
    </tr>
  {/section}
</table>
