<h1><a class="pagetitle" href="tiki-send_newsletters.php">{tr}Send newsletters{/tr}</a></h1>
{if $tiki_p_admin_newsletters eq "y"}<a class="linkbut" href="tiki-admin_newsletters.php">{tr}admin newsletters{/tr}</a>{/if}<br />
{assign var=area_name value="editnl"}
{if $emited eq 'y'}
{tr}The newsletter was sent to {$sent} email addresses{/tr}<br /><br />
{if $errors}
<span class="attention">{tr}Errors detected{/tr}<br /></span>
<table class="normal">
<tr><td class="heading">{tr}User{/tr}</td><td class="heading">{tr}Email{/tr}</td></tr>
{cycle values="odd,even" print=false}
{section loop=$errors name=ix}
<tr><td class="{cycle advance=false}">{$errors[ix].user}</td><td class="{cycle}">{$errors[ix].email}</td></tr>
{/section}
</table><br /><br />
{/if}
{/if}
{if $presend eq 'y'}
<br />
<b>{tr}This newsletter will be sent to {$subscribers} email addresses.{/tr}</b>
<form method="post" action="tiki-send_newsletters.php">
<input type="hidden" name="nlId" value="{$nlId|escape}" />
<input type="hidden" name="subject" value="{$subject|escape}" />
<input type="hidden" name="data" value="{$data|escape}" />
<input type="hidden" name="dataparsed" value="{$dataparsed|escape}" />
<input type="submit" name="send" value="{tr}send{/tr}" />
<input type="submit" name="preview" value="{tr}cancel{/tr}" />
</form>
<h2>{tr}Preview{/tr}</h2>
<div class="wikitext">{$subject}</div>
<div class="wikitext">{$dataparsed}</div>
{else}
{if $preview eq 'y'}
<h2>{tr}Preview{/tr}</h2>
<div class="wikitext">{$info.subject}</div>
<div class="wikitext">{$info.dataparsed}</div>
{if $txt}<div class="wikitext">{$txt}</div>{/if}
{/if}
<h2>{tr}Prepare a newsletter to be sent{/tr}</h2>
<form action="tiki-send_newsletters.php" method="post" id='editpageform'>
<table class="normal">
<tr><td class="formcolor">{tr}Subject{/tr}:</td><td class="formcolor"><input type="text" maxlength="250" size="40" name="subject" value="{$info.subject|escape}" /></td></tr>
<tr><td class="formcolor">{tr}Newsletter{/tr}:</td><td class="formcolor">
<select name="nlId">
{section loop=$newsletters name=ix}
<option value="{$newsletters[ix].nlId|escape}" {if $newsletters[ix].nlId eq $nlId}selected="selected"{/if}>{$newsletters[ix].name}</option>
{/section}
</select>
</td></tr>

{if $tiki_p_use_content_templates eq 'y'}
<tr><td class="formcolor">{tr}Apply content template{/tr}</td><td class="formcolor">
<input type="hidden" name="previousTemplateId" value="{$templateId}">
<select name="templateId" onchange="javascript:document.getElementById('editpageform').submit();">
<option value="0">{tr}none{/tr}</option>
{section name=ix loop=$templates}
<option value="{$templates[ix].templateId|escape}" {if $templateId eq $templates[ix].templateId}selected="selected"{/if}>{$templates[ix].name}</option>
{/section}
</select>
</td></tr>
{/if}
{if $tpls}
<tr><td class="formcolor">{tr}Apply template{/tr}</td><td class="formcolor">
<select name="usedTpl">
<option value="">{tr}none{/tr}</option>
{section name=ix loop=$tpls}
<option value="{$tpls[ix]|escape}" {if $usedTpl eq $tpls[ix]}selected="selected"{/if}>{$tpls[ix]}</option>
{/section}
</select>
</td></tr>
{/if}
<tr><td class="formcolor">{tr}Data{/tr}:<br /><br />{include file="textareasize.tpl" area_name='editnl' formId='editpageform'}<br /><br />{include file=tiki-edit_help_tool.tpl}</td>
<td class="formcolor"><textarea id='editnl' name="data" rows="{$rows}" cols="{$cols}">{$info.data|escape}</textarea>
<input type="hidden" name="rows" value="{$rows}"/>
<input type="hidden" name="cols" value="{$cols}"/>
</td></tr>
<tr><td  class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="preview" value="{tr}Preview{/tr}" /></td></tr>
<tr><td  class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="save" value="{tr}Send Newsletters{/tr}" /></td></tr>
</table>
</form>
{/if}

{if $presend ne 'y'}
{include file=sent_newsletters.tpl }
{/if}
