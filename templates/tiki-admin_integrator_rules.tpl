{* $Id$ *}

{title help="Integrator"}{tr}Edit Rules for Repository:{/tr} {$name}{/title}

<div class="navbar">
	{button href="tiki-admin_integrator.php" _text="{tr}Configure Repositories{/tr}"}
  {button href="tiki-list_integrator_repositories.php" _text="{tr}List Repositories{/tr}</a>"}
	{assign var=thisrepID value=$repID|escape } 
	{button href="tiki-admin_integrator.php?action=edit&amp;repID=$thisrepID" _text="{tr}Configure this Repository{/tr}"}
	{button href="tiki-integrator.php?repID=$thisrepID" _text="{tr}View this Repository{/tr}"}
	{button href="tiki-admin_integrator_rules.php?repID=$thisrepID" _text="{tr}New Rule{/tr}"}
	{if count($reps) gt 0}
		{button _onclick="javascript:flip('rules-copy-panel');" _text="{tr}Copy Rules{/tr}" _title="{tr}view/hide copy rules dialog{/tr}"}
  {/if}
</div>

{if count($reps) gt 0}
<div id="rules-copy-panel">
<form action="tiki-admin_integrator_rules.php?repID={$repID|escape}" method="post">
<table class="formcolor"><tr>
  <td>{tr}Source repository{/tr}</td>
  <td>
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

<table class="formcolor">
  <tr>
    <td><span title="{tr}According this order rules will be applied ('0' or empty = auto){/tr}">{tr}Rule order{/tr}</span></td>
    <td><input type="text" maxlength="2" size="2" name="ord" value="{$ord|escape}" title="{tr}According this order rules will be applied ('0' or empty = auto){/tr}" />
    </td>
  </tr><tr>
    <td><span title="{tr}Text to search for{/tr}">{tr}Search{/tr}</span></td>
    <td><input type="text" name="srch" value="{$srch|escape}" title="{tr}Text to search for{/tr}" /></td>
  </tr><tr>
    <td><span title="{tr}Text to replace{/tr}">{tr}Replace{/tr}</span></td>
    <td><input type="text" name="repl" value="{$repl|escape}" title="{tr}Text to replace{/tr}" /></td>
  </tr><tr>
    <td><span title="{tr}Is this regular expression or simple search/replacer{/tr}">{tr}Regex{/tr}</span></td>
    <td>
      <input type="checkbox" name="type" {if $type eq 'y'}checked="checked"{/if} title="{tr}Is this regular expression or simple search/replacer{/tr}" />
    </td>
  </tr><tr>
    <td><span title="{tr}Is case sensitive (for simple replacer){/tr}">{tr}Case sensitive{/tr}</td>
    <td>
      <input type="checkbox" name="casesense" {if $casesense eq 'y'}checked="checked"{/if} title="{tr}Is case sensitive (for simple replacer){/tr}" />
    </td>
  </tr><tr>
    <td><span title="{tr}subset of chars: imsxeADSXUu, which is regex modifiers{/tr}">{tr}Regex modifiers{/tr}</span></td>
    <td>
      <input type="text" maxlength="20" size="20" name="rxmod" value="{$rxmod|escape}" title="{tr}subset of chars: imsxeADSXUu, which is regex modifiers{/tr}" />
    </td>
  </tr><tr>
    <td><span title="{tr}Human-readable text description of rule{/tr}">{tr}Description{/tr}</td>
    <td><textarea name="description" rows="4" title="{tr}Human-readable text description of rule{/tr}">{$description|escape}</textarea></td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>
      <input type="submit" name="save" value="{tr}Save{/tr}" />&nbsp;&nbsp;
      <input type="checkbox" name="enabled" {if $enabled eq 'y'}checked="checked"{/if} title="{tr}Check to enable this rule{/tr}" />&nbsp;
      {tr}Enabled{/tr}
    </td>
  </tr><tr>
    <td colspan="2">{tr}Preview options{/tr}</td>
  </tr><tr>
    <td><span title="{tr}Apply all rules or just this to generate preview{/tr}">{tr}Apply all rules{/tr}</td>
    <td><input type="checkbox" name="all" {if $all eq 'y'}checked="checked"{/if} title="{tr}Apply all rules or just this to generate preview{/tr}" /></td>
  </tr><tr>
    <td><span title="{tr}View source code after rules applied{/tr}">{tr}Code preview{/tr}</td>
    <td><input type="checkbox" name="code" {if $code eq 'y'}checked="checked"{/if} title="{tr}View source code after rules applied{/tr}" /></td>
  </tr><tr>
    <td><span title="{tr}Generate HTML preview{/tr}">{tr}HTML preview{/tr}</td>
    <td><input type="checkbox" name="html" {if $html eq 'y'}checked="checked"{/if} title="{tr}Generate HTML preview{/tr}" /></td>
  </tr><tr>
    <td><span title="{tr}Test file from repository to generate preview for (empty = configured start page){/tr}">{tr}File{/tr}</td>
    <td>
      <input type="text" name="file" value="{$file|escape}" title="{tr}Test file from repository to generate preview for (empty = configured start page){/tr}" />
    </td>
  </tr><tr>
    <td>&nbsp;</td>
    <td><input type="submit" name="preview" value="{tr}Preview{/tr}" /></td>
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
    <th rowspan="2"><span title="{tr}Rule order{/tr}">#</span></th>
    <th>{tr}Search{/tr}</th>
    <th>{tr}Replace{/tr}</th>
    <th>{tr}Regex{/tr}</th>
    <th>{tr}Case{/tr}</th>
    <th>{tr}Actions{/tr}</th>
  </tr><tr>
    <th colspan="5">{tr}Description{/tr}</th>
  </tr>
  {cycle values="odd,even" print=false}
  {section name=rule loop=$rules}
    <tr class="{cycle}">
      <td{if (strlen($rules[rule].description) > 0)} rowspan="2"{/if}>
        {if $rules[rule].enabled ne 'y'}<s>{$rules[rule].ord|escape}</s>
        {else}{$rules[rule].ord|escape}
        {/if}
      </td>
      <td>{$rules[rule].srch|escape}</td>
      <td>{$rules[rule].repl|escape}</td>
      <td>{$rules[rule].type|escape}</td>
      <td>{$rules[rule].casesense|escape}</td>
      <td>
        <a href="tiki-admin_integrator_rules.php?action=edit&amp;repID={$repID|escape}&amp;ruleID={$rules[rule].ruleID|escape}" title="{tr}Edit{/tr}">{icon _id='wrench' alt="{tr}Configure/Options{/tr}"}</a>
        &nbsp;&nbsp;<a href="tiki-admin_integrator_rules.php?action=rm&amp;repID={$repID|escape}&amp;ruleID={$rules[rule].ruleID|escape}"
		title="{tr}Delete{/tr}">{icon _id='cross' alt="{tr}Delete{/tr}"}</a>&nbsp;&nbsp;
      </td>

    {* Show description as colspaned row if it is not an empty *}
    {if (strlen($rules[rule].description) > 0)}
    </tr><tr class="{cycle}">
      <td colspan="5">{$rules[rule].description|escape}</td>
    {/if}
    </tr>
  {/section}
</table>
<br /><br />
