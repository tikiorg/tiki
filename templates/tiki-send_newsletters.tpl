{* $Id$ *}
{$showBoxCheck}
<h1><a class="pagetitle" href="tiki-send_newsletters.php">{tr}Send newsletters{/tr} {if $nlId ne '0'}{$nlName}{/if}</a></h1>
{if $tiki_p_admin_newsletters eq "y"}<div class="navbar"><span class="button2"><a class="linkbut" href="tiki-admin_newsletters.php{if $nlId}?nlId={$nlId}{/if}">{tr}Admin Newsletters{/tr}</a></span></div>{/if}
{assign var=area_name value="editnl"}
{if $emited eq 'y'}
{tr}The newsletter was sent to {$sent} email addresses{/tr}<br /><br />
{if $errors}
<span class="attention">{tr}Errors detected{/tr}<br /></span>
<table class="normal">
<tr class="formcolor"><td class="heading">{tr}User{/tr}</td><td class="heading">{tr}Email{/tr}</td></tr>
{cycle values="odd,even" print=false}
{section loop=$errors name=ix}
<tr class="formcolor"><td class="{cycle advance=false}">{$errors[ix].user}</td><td class="{cycle}">{$errors[ix].email}</td></tr>
{/section}
</table><br /><br />
{/if}
{/if}
{if $presend eq 'y'}
<br />
<b>{tr}This newsletter will be sent to {$subscribers} email addresses.{/tr}</b>
<form method="post" action="tiki-send_newsletters.php">
<input type="hidden" name="nlId" value="{$nlId|escape}" />
<input type="hidden" name="editionId" value="{$info.editionId}"/>
<input type="hidden" name="subject" value="{$subject|escape}" />
<input type="hidden" name="data" value="{$data|escape}" />
<input type="hidden" name="dataparsed" value="{$dataparsed|escape}" />
<input type="hidden" name="cookietab" value="3" />
<input type="hidden" name="datatxt" value="{$datatxt|escape}" />
<input type="submit" name="send" value="{tr}Send{/tr}" />
<input type="submit" name="preview" value="{tr}Cancel{/tr}" />
</form>
<h2>{tr}Preview{/tr}</h2>
<div class="wikitext">{$subject}</div>
<h3>{tr}HTML version{/tr}</h3>
<div class="wikitext">{$dataparsed}</div>
{if $allowTxt eq 'y' }
<h3>{tr}Text version{/tr}</h3>
<div class="wikitext">{$datatxt}</div>
{/if}
{else}
{if $preview eq 'y'}
<h2>{tr}Preview{/tr}</h2>
<div class="wikitext">{$info.subject}</div>
<h3>{tr}HTML version{/tr}</h3>
<div class="wikitext">{$info.dataparsed}</div>
{if $allowTxt eq 'y' }
<h3>{tr}Text version{/tr}</h3>
<div class="wikitext" >{$info.datatxt}</div>
{/if}
{if $txt}<div class="wikitext">{$txt}</div>{/if}
{/if}

<br />
{* --- tab headers --- *}
{if $prefs.feature_tabs eq 'y'}
{cycle name=tabs values="1,2,3,4" print=false advance=false reset=true}
<div class="tabs">
<span id="tab{cycle name=tabs advance=false}" class="tabmark"><a href="javascript:tikitabs({cycle name=tabs},4);">{tr}Edit{/tr}</a></span>
<span id="tab{cycle name=tabs advance=false}" class="tabmark"><a href="javascript:tikitabs({cycle name=tabs},4);">{tr}Drafts{/tr}&nbsp;({$cant_drafts})</a></span>
<span id="tab{cycle name=tabs advance=false}" class="tabmark"><a href="javascript:tikitabs({cycle name=tabs},4);">{tr}Sent editions{/tr}&nbsp;({$cant_editions})</a></span>
</div>
{/if}

{cycle name=content values="1,2,3,4" print=false advance=false reset=true}
{* --- tab with editior --- *}
<div id="content{cycle name=content assign=focustab}{$focustab}" class="tabcontent"{if $prefs.feature_tabs eq 'y'} style="display:{if $focustab eq $cookietab}block{else}none{/if};"{/if}>

<h2>{tr}Prepare a newsletter to be sent{/tr}</h2>
<form action="tiki-send_newsletters.php" method="post" id='editpageform'>
<input type="hidden" name="editionId" value="{$info.editionId}"/>
<table class="normal" id="newstable">
<tr class="formcolor"><td class="formcolor">{tr}Subject{/tr}:</td><td class="formcolor"><input type="text" maxlength="250" size="80" name="subject" value="{$info.subject|escape}" /></td></tr>
<tr class="formcolor"><td class="formcolor">{tr}Newsletter{/tr}:</td><td class="formcolor">
<select name="nlId" onchange="checkNewsletterTxtArea();">
{section loop=$newsletters name=ix}
<option value="{$newsletters[ix].nlId|escape}" {if $newsletters[ix].nlId eq $nlId}selected="selected"{/if}>{$newsletters[ix].name}</option>
{/section}
</select>
</td></tr>

{if $tiki_p_use_content_templates eq 'y'}
<tr class="formcolor"><td class="formcolor">{tr}Apply content template{/tr}</td><td class="formcolor">
<input type="hidden" name="previousTemplateId" value="{$templateId}" />
<select name="templateId" onchange="javascript:document.getElementById('editpageform').submit();">
<option value="0">{tr}none{/tr}</option>
{section name=ix loop=$templates}
<option value="{$templates[ix].templateId|escape}" {if $templateId eq $templates[ix].templateId}selected="selected"{/if}>{$templates[ix].name}</option>
{/section}
</select>
</td></tr>
{/if}
{if $tpls}
<tr class="formcolor"><td class="formcolor">{tr}Apply template{/tr}</td><td class="formcolor">
<select name="usedTpl">
<option value="">{tr}none{/tr}</option>
{section name=ix loop=$tpls}
<option value="{$tpls[ix]|escape}" {if $usedTpl eq $tpls[ix]}selected="selected"{/if}>{$tpls[ix]}</option>
{/section}
</select>
</td></tr>
{/if}

<tr class="formcolor">
  <td class="formcolor">{tr}Data HTML{/tr}:
    <br /><br />
    {include file="textareasize.tpl" area_name='editnl' formId='editpageform'}
    {if $prefs.quicktags_over_textarea neq 'y'}
      <br /><br />
      {include file=tiki-edit_help_tool.tpl area_name='data'}
    {/if}
  </td>
  <td class="formcolor">
    {if $prefs.quicktags_over_textarea eq 'y'}
      {include file=tiki-edit_help_tool.tpl area_name='data'}
    {/if}
    <textarea id='editnl' name="data" rows="{$rows}" cols="{$cols}">{$info.data|escape}</textarea>
    <input type="hidden" name="rows" value="{$rows}"/>
    <input type="hidden" name="cols" value="{$cols}"/>
  </td>
</tr>
  
<tr class="formcolor">
  <td class="formcolor" id="txtcol1">
    {tr}Data Txt{/tr}:<br /><br />{include file="textareasize.tpl" area_name='editnltxt' formId='editpageform'}
  </td>
  <td class="formcolor" id="txtcol2" >
    <textarea id='editnltxt' name="datatxt" rows="{$rows}" cols="{$cols}">{$info.datatxt|escape}</textarea>
  </td>
</tr>

<tr class="formcolor">
  <td class="formcolor">&nbsp;</td>
  <td class="formcolor">
    <input type="submit" name="preview" value="{tr}Preview{/tr}" />&nbsp;<input type="submit" name="save_only" value="{tr}Save as draft{/tr}" /></td>
</tr>

<tr>
  <td class="formcolor">&nbsp;</td>
  <td class="formcolor">&nbsp;<input type="submit" name="save" value="{tr}Send Newsletters{/tr}" /></td>
</tr>
</table>
</form>
</div>
{* --- tab with drafts --- *}
<div id="content{cycle name=content assign=focustab}{$focustab}" class="tabcontent"{if $prefs.feature_tabs eq 'y'} style="display:{if $focustab eq $cookietab}block{else}none{/if};"{/if}>
{** need to be finished }
{assign var=channels value=$drafts}
{assign var=view_editions value='n'}
{assign var=offset value=$dr_offset}
{assign var=next_offset value=$dr_next_offset}
{assign var=prev_offset value=$dr_prev_offset}
{assign var=actual_page value=$dr_actual_page}
{assign var=cant_pages value=$dr_cant_pages}
{assign var=cur value='dr'}
{assign var=bak value='ed'}
{assign var=sort_mode value=$dr_sort_mode}
{assign var=sort_mode_bak value=$ed_sort_mode}
{assign var=offset value=$dr_offset}
{assign var=offset_bak value=$ed_offset}
{assign var=find value=$dr_find}
{assign var=find_bak value=$ed_find}
{assign var=tab value=2}
<h2>{tr}Drafts{/tr}&nbsp;({$cant_drafts})</h2>
{include file=sent_newsletters.tpl }
{ **}
</div>

{* --- tab with editions --- *}
<div id="content{cycle name=content assign=focustab}{$focustab}" class="tabcontent"{if $prefs.feature_tabs eq 'y'} style="display:{if $focustab eq $cookietab}block{else}none{/if};"{/if}>
{assign var=channels value=$editions}
{assign var=view_editions value='y'}
{assign var=offset value=$ed_offset}
{assign var=next_offset value=$ed_next_offset}
{assign var=prev_offset value=$ed_prev_offset}
{assign var=actual_page value=$ed_actual_page}
{assign var=cant_pages value=$ed_cant_pages}
{assign var=cur value='ed'}
{assign var=bak value='dr'}
{assign var=sort_mode value=$ed_sort_mode}
{assign var=sort_mode_bak value=$dr_sort_mode}
{assign var=offset value=$ed_offset}
{assign var=offset_bak value=$dr_offset}
{assign var=find value=$ed_find}
{assign var=find_bak value=$dr_find}
{assign var=tab value=3}
<h2>{tr}Sent editions{/tr}&nbsp;({$cant_editions})</h2>
{include file=sent_newsletters.tpl }
</div>
{/if}
<script type='text/javascript'>
<!--
{if $allowTxt eq 'n'}
document.getElementById('txtcol1').style.display='none';
document.getElementById('txtcol2').style.display='none';
{/if}
-->
</script>
