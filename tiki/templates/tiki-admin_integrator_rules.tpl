{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-admin_integrator_rules.tpl,v 1.6 2003-10-17 16:10:17 zaufi Exp $ *}

<h2>{tr}Edit Rules for Repository:{/tr} {$name}</h2>
<div id="page-bar">
  <table><tr>
    <td><div class="button2">
      <a href="tiki-admin_integrator.php" class="linkbut">{tr}configure repositories{/tr}</a>
    </div></td>
    <td><div class="button2">
      <a href="tiki-list_integrator_repositories.php" class="linkbut">{tr}list repositories{/tr}</a>
    </div></td>
    <td><div class="button2">
      <a href="tiki-integrator.php?repID={$repID|escape}" class="linkbut">{tr}view repository{/tr}</a>
    </div></td>
    {if count($reps) gt 0}
    <td><div class="button2">
      <a title="{tr}view/hide copy rules dialog{/tr}" href="javascript:flip('rules-copy-panel');" class="linkbut">
        {tr}copy rules{/tr}
      </a>
    </div></td>
    {/if}
  </tr></table>
</div>
<br />

{if count($reps) gt 0}
<div id="rules-copy-panel">
<form action="tiki-admin_integrator_rules.php?repID={$repID|escape}" method="post">
<table class="normal"><tr>
  <td class="formcolor">{tr}Source repository{/tr}</td>
  <td class="formcolor">
    <select name="srcrep">{html_options options=$reps}</select> &nbsp; &nbsp;
    <input type="submit" name="copy" value="{tr}Copy{/tr}" />
  </td>
</tr> </table>
</form>
<br /><br />
</div>
{/if}

{* Add form *}
<form action="tiki-admin_integrator_rules.php?repID={$repID|escape}" method="post">
<input type="hidden" name="ruleID" value="{$ruleID|escape}" />
<input type="hidden" name="repID" value="{$repID|escape}" />

<table class="normal">
  <tr><td class="formcolor">{tr}Search{/tr}</td>
    <td class="formcolor"><input type="text" name="srch" value="{$srch|escape}" /></td>
  </tr><tr>
    <td class="formcolor">{tr}Replace{/tr}</td>
    <td class="formcolor"><input type="text" name="repl" value="{$repl|escape}" /></td>
  </tr><tr>
    <td class="formcolor">{tr}Regex{/tr}</td>
    <td class="formcolor">
      <input type="checkbox" name="type" {if $type eq 'y'}checked="checked"{/if} /> &nbsp;&nbsp;
      {tr}Use preg_replace or str_replace to filter text{/tr}
    </td>
  </tr><tr>
    <td class="formcolor">{tr}Case sensitive{/tr}</td>
    <td class="formcolor">
      <input type="checkbox" name="casesense" {if $casesense eq 'y'}checked="checked"{/if} /> &nbsp;&nbsp;
      {tr}Use case sensitive str_replace{/tr}
    </td>
  </tr><tr>
    <td class="formcolor">{tr}<span title="set of: imsxeADSXUu">Regex modifiers{/tr}</span></td>
    <td class="formcolor">
      <input type="text" name="rxmod" value="{$rxmod|escape}" /> &nbsp;&nbsp;
      {tr}Aux modifiers for preg_replace{/tr}
    </td>
  </tr><tr>
    <td class="formcolor">{tr}Description{/tr}</td>
    <td class="formcolor"><textarea name="description" rows="4">{$description|escape}</textarea></td>
  </tr><tr>
    <td class="formcolor">&nbsp;</td>
    <td class="formcolor"><input type="submit" name="save" value="{tr}Save{/tr}" /></td>
  </tr><tr>
    <td class="formcolor" colspan="2">{tr}Preview options{/tr}</td>
  </tr><tr>
    <td class="formcolor">{tr}Apply all rules{/tr}</td>
    <td class="formcolor"><input type="checkbox" name="all" {if $all eq 'y'}checked="checked"{/if} /></td>
  </tr><tr>
    <td class="formcolor">{tr}Code preview{/tr}</td>
    <td class="formcolor"><input type="checkbox" name="code" {if $code eq 'y'}checked="checked"{/if} /></td>
  </tr><tr>
    <td class="formcolor">{tr}HTLM preview{/tr}</td>
    <td class="formcolor"><input type="checkbox" name="html" {if $html eq 'y'}checked="checked"{/if} /></td>
  </tr><tr>
    <td class="formcolor">{tr}File{/tr}</td>
    <td class="formcolor">
      <input type="text" name="file" value="{$file|escape}" /> &nbsp;&nbsp;
      {tr}Test file from repository (empty = configured start page){/tr}
    </td>
  </tr><tr>
    <td class="formcolor">&nbsp;</td>
    <td class="formcolor"><input type="submit" name="preview" value="{tr}Preview{/tr}" /></td>
  </tr>
</table>
</form>

{if (($html eq 'y') or ($code eq 'y')) and (strlen($preview_data) gt 0)}
  <h2>{tr}Preview Results{/tr}</h2>
  {if strlen($css_file) > 0}
    <link rel="StyleSheet"  href="{$css_file}" type="text/css" />
  {/if}
  <div class="integration_preview">
  {if $code eq 'y'}
    <div class="codelisting"><pre>{$preview_data|escape:"html"|wordwrap:120:"\n"}</pre></div>
  {/if}
  {if $html eq 'y'}
      <div class="integrated-page">{$preview_data}</div>
  {/if}
  </div>
{/if}

<h2>{tr}Rules List{/tr}</h2>

{* Table with list of repositories *}
<table class="normal" id="integrator_rules">
  <tr>
    <td class="heading">{tr}Search{/tr}</td>
    <td class="heading">{tr}Replace{/tr}</td>
    <td class="heading">{tr}Regex{/tr}</td>
    <td class="heading">{tr}Case{/tr}</td>
    <td class="heading">{tr}Actions{/tr}</td>
  </tr><tr>
    <td class="heading" colspan="5">{tr}Description{/tr}</td>
  </tr>
  {cycle values="odd,even" print=false}
  {section name=rule loop=$rules}
    <tr>
      <td class="{cycle advance=false}">{$rules[rule].srch|escape}</td>
      <td class="{cycle advance=false}">{$rules[rule].repl|escape}</td>
      <td class="{cycle advance=false}">{$rules[rule].type|escape}</td>
      <td class="{cycle advance=false}">{$rules[rule].casesense|escape}</td>
      <td class="{if (strlen($rules[rule].description) > 0)}{cycle advance=false}{else}{cycle}{/if}">
        <a href="tiki-admin_integrator_rules.php?action=edit&repID={$repID|escape}&ruleID={$rules[rule].ruleID|escape}" title='{tr}edit{/tr}' >
            <img src='img/icons/config.gif' alt='{tr}edit{/tr}' border='0' />
        </a>
        <a href="tiki-admin_integrator_rules.php?action=rm&repID={$repID|escape}&ruleID={$rules[rule].ruleID|escape}" title='{tr}remove{/tr}' >
            <img src='img/icons2/delete.gif' alt='{tr}remove{/tr}' border='0' />
        </a>
      </td>

    {* Show description as colspaned row if it is not an empty *}
    {if (strlen($rules[rule].description) > 0)}
    </tr><tr>
      <td class="{cycle}" colspan="5">{$rules[rule].description|escape}</td>
    {/if}
    </tr>
  {/section}
</table>
<br /><br />
