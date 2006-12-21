{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-editpage.tpl,v 1.96 2006-12-21 14:57:59 mose Exp $ *}

{popup_init src="lib/overlib.js"}

{* Check to see if there is an editing conflict *}
{if $editpageconflict == 'y'}
<script type='text/javascript'>
<!-- //Hide Script
	alert("{tr}This page is being edited by{/tr} {$semUser}. {tr}Proceed at your own peril{/tr}.")
//End Hide Script -->
</script>
{/if}

{if $feature_ajax == 'y'}
  <script language="JavaScript" src="lib/wiki/wiki-ajax.js"></script>
{/if}

<h1>{tr}Edit{/tr}: {$page|escape}{if $pageAlias ne ''}&nbsp;({$pageAlias|escape}){/if}</h1>
{if isset($data.draft)}
  {tr}Draft written on{/tr} {$data.draft.lastModif|tiki_long_time}<br/>
  {if $data.draft.lastModif < $data.lastModif}
    <b>{tr}Warning: new versions of this page have been made after this draft{/tr}</b>
  {/if}
{/if}
{if $page|lower eq 'sandbox'}
<div class="wikitext">
{tr}The SandBox is a page where you can practice your editing skills, use the preview feature to preview the appearance of the page, no versions are stored for this page.{/tr}
</div>
{/if}
{if $category_needed eq 'y'}
<div class="simplebox highlight">{tr}A category is mandatory{/tr}</div>
{/if}
{if $contribution_needed eq 'y'}
<div class="simplebox highlight">{tr}A contribution is mandatory{/tr}</div>
{/if}

{if $likepages}
<div>
{tr}Perhaps you are looking for:{/tr}
{if $likepages|@count < 0}
<ul>
{section name=back loop=$likepages}
<li><a  href="tiki-index.php?page={$likepages[back]|escape:"url"}" class="wiki">{$likepages[back]}</a></li>
{/section}
</ul>
{else}
<table class="normal"><tr>
{cycle name=table values=',,,,</tr><tr>' print=false advance=false}
{section name=back loop=$likepages}
<td><a  href="tiki-index.php?page={$likepages[back]|escape:"url"}" class="wiki">{$likepages[back]}</a></td>{cycle name=table}
{/section}
</tr></table>
{/if}
</div>
<br />
{/if}

{if $preview}
{include file="tiki-preview.tpl"}
{/if}
<form  enctype="multipart/form-data" method="post" action="tiki-editpage.php" id='editpageform' name='editpageform'>
{if $preview}
<input type="submit" class="wikiaction" name="preview" value="{tr}preview{/tr}" />
{if $page|lower neq 'sandbox'}
{if $tiki_p_minor eq 'y'}
<input type="checkbox" name="isminor" value="on" />{tr}Minor{/tr}
{/if}
<input type="submit" class="wikiaction" name="save" value="{tr}save{/tr}" /> &nbsp;&nbsp; <input type="submit" class="wikiaction" name="cancel_edit" value="{tr}cancel edit{/tr}" />
{/if}
{/if}
{if $page_ref_id}
<input type="hidden" name="page_ref_id" value="{$page_ref_id}" />
{/if}
{if $current_page_id}
<input type="hidden" name="current_page_id" value="{$current_page_id}" />
{/if}
{if $add_child}
<input type="hidden" name="add_child" value="true" />
{/if}
{if $feature_wysiwyg eq 'y' and $wysiwyg_optional eq 'y'}
{if $wysiwyg ne 'y'}
<span class="button2"><a class="linkbut" href="?page={$page}&amp;wysiwyg=y">{tr}Use wysiwyg editor{/tr}</a></span>
{else}
<span class="button2"><a class="linkbut" href="?page={$page}&amp;wysiwyg=n">{tr}Use normal editor{/tr}</a></span>
{/if}
{/if}

<table class="normal">

{if $categIds}
{section name=o loop=$categIds}
<input type="hidden" name="cat_categories[]" value="{$categIds[o]}" />
{/section}
<input type="hidden" name="categId" value="{$categIdstr}" />
<input type="hidden" name="cat_categorize" value="on" />
{else}
{if $tiki_p_view_categories eq 'y'}
{include file=categorize.tpl}
{/if}
{/if}
{include file=structures.tpl}

{if $feature_wiki_templates eq 'y' and $tiki_p_use_content_templates eq 'y' and !$templateId}
<tr class="formcolor"><td>{tr}Apply template{/tr}:</td><td>
<select name="templateId" onchange="javascript:document.getElementById('editpageform').submit();">
<option value="0">{tr}none{/tr}</option>
{section name=ix loop=$templates}
<option value="{$templates[ix].templateId|escape}" {if $templateId eq $templates[ix].templateId}selected="selected"{/if}>{tr}{$templates[ix].name}{/tr}</option>
{/section}
</select>
</td></tr>
{/if}

{if $feature_wiki_ratings eq 'y' and $tiki_p_wiki_admin_ratings eq 'y'}
<tr class="formcolor"><td>{tr}Use rating{/tr}:</td><td>
{if $poll_rated.info}
<input type="hidden" name="poll_title" value="{$poll_rated.info.title|escape}" />
<a href="tiki-admin_poll_options.php?pollId={$poll_rated.info.pollId}">{$poll_rated.info.title}</a>
<span class="button2"><a class="linkbut" href="tiki-editpage.php?page={$page|escape:"url"}&amp;removepoll={$poll_rated.info.pollId}">{tr}disable{/tr}</a>
{if $tiki_p_admin_poll eq 'y'}
<span class="button2"><a class="linkbut" href="tiki-admin_polls.php">{tr}admin polls{/tr}</a></span>
{/if}
{else}
{if count($polls_templates)}
{tr}type{/tr}
<select name="poll_template">
<option value="0">{tr}none{/tr}</option>
{section name=ix loop=$polls_templates}
<option value="{$polls_templates[ix].pollId|escape}"{if $polls_templates[ix].pollId eq $poll_template} selected="selected"{/if}>{tr}{$polls_templates[ix].title}{/tr}</option>
{/section}
</select>
{tr}title{/tr}
<input type="text" name="poll_title" value="{$poll_title|escape}" size="22" />
{else}
{tr}There is no available poll template.{/tr}
{if $tiki_p_admin_polls ne 'y'}
{tr}You should ask an admin to create them.{/tr}
{/if}
{/if}
{if count($listpolls)}
{tr}or use{/tr}
<select name="olpoll">
<option value="">... {tr}an existing poll{/tr}</option>
{section name=ix loop=$listpolls}
<option value="{$listpolls[ix].pollId|escape}">{tr}{$listpolls[ix].title|default:"<i>... no title ...</i>"}{/tr} ({$listpolls[ix].votes} {tr}votes{/tr})</option>
{/section}
</select>
{/if}
{/if}
</td></tr>
{/if}

{if $feature_smileys eq 'y'&&!$wysiwyg}
<tr class="formcolor"><td>{tr}Smileys{/tr}:</td><td>
{include file="tiki-smileys.tpl" area_name='editwiki'}
</td>
</tr>
{/if}
{if $feature_wiki_description eq 'y'}
<tr class="formcolor"><td>{tr}Description{/tr}:</td><td><input style="width:95%;" class="wikitext" type="text" name="description" value="{$description|escape}" /></td></tr>
{/if}
<tr class="formcolor">
{if $wysiwyg ne 'y'}
<td>
{tr}Edit{/tr}:<br /><br />
{include file="textareasize.tpl" area_name='editwiki' formId='editpageform' ToolbarSet='Tiki'}<br /><br />
{include file=tiki-edit_help_tool.tpl area_name='editwiki'}
</td>
<td>
<textarea id='editwiki' class="wikiedit" name="edit" rows="{$rows}" cols="{$cols}" style="WIDTH: 100%;">{$pagedata|escape}</textarea>
<input type="hidden" name="rows" value="{$rows}"/>
<input type="hidden" name="cols" value="{$cols}"/>
{else}
<td colspan="2">
{editform Meat=$pagedata InstanceName='edit' ToolbarSet="Tiki"}
{/if}
</td></tr>

{if $feature_wiki_replace eq 'y'}
<script type="text/javascript">
{literal}
function searchrep() {
  c = document.getElementById('caseinsens')
  s = document.getElementById('search')
  r = document.getElementById('replace')
  t = document.getElementById('editwiki')

  var opt = 'g';
  if (c.checked == true) {
    opt += 'i'
  }
  var str = t.value
  var re = new RegExp(s.value,opt)
  t.value = str.replace(re,r.value)
}
{/literal}
</script>

<tr class="formcolor"><td>{tr}Search {/tr}:</td><td>
<input style="width:100;" class="wikitext" type="text" id="search"/>
{tr}Replace to{/tr}:
<input style="width:100;" class="wikitext" type="text" id="replace"/>
<input type="checkbox" id="caseinsens" />{tr}Case Insensitivity{/tr}
<input type="button" value="{tr}replace{/tr}" onclick="javascript:searchrep();">
</td></tr>
{/if}

{if $feature_wiki_footnotes eq 'y'}
{if $user}
<tr class="formcolor"><td>{tr}My Footnotes{/tr}:</td><td><textarea name="footnote" rows="8" cols="42" style="width:95%;" >{$footnote|escape}</textarea></td></tr>
{/if}
{/if}

{if $feature_multilingual eq 'y'}
<tr class="formcolor"><td>{tr}Language{/tr}:</td><td>
<select name="lang">
<option value="">{tr}Unknown{/tr}</option>
{section name=ix loop=$languages}
<option value="{$languages[ix].value|escape}"{if $lang eq $languages[ix].value} selected="selected"{/if}>{$languages[ix].name}</option>
{/section}
</select>
</td></tr>
{*<tr class="formcolor"><td>{tr}Is a translation of this page:{/tr}</td><td><input style="width:95%;" type="text" name="translation" value="{$translation|escape}" /></td></tr>*}
{/if}

{if $page|lower neq 'sandbox'}
<tr class="formcolor"><td>{tr}Edit Summary{/tr}:</td><td><input style="width:90%;" class="wikitext" type="text" name="comment" value="{$commentdata|escape}" /></td></tr>
{if $wiki_feature_copyrights  eq 'y'}
<tr class="formcolor"><td>{tr}Copyright{/tr}:</td><td>
<table border="0">
<tr class="formcolor"><td>{tr}Title:{/tr}</td><td><input size="40" class="wikitext" type="text" name="copyrightTitle" value="{$copyrightTitle|escape}" /></td></tr>
<tr class="formcolor"><td>{tr}Year:{/tr}</td><td><input size="4" class="wikitext" type="text" name="copyrightYear" value="{$copyrightYear|escape}" /></td></tr>
<tr class="formcolor"><td>{tr}Authors:{/tr}</td><td><input size="40" class="wikitext" name="copyrightAuthors" type="text" value="{$copyrightAuthors|escape}" /></td></tr>
</table>
</td></tr>
{/if}
{/if}
{if $feature_freetags eq 'y' and $tiki_p_freetags_tag eq 'y'}
  {include file=freetag.tpl}
{/if}
{if $feature_wiki_allowhtml eq 'y' and $tiki_p_use_HTML eq 'y'}
<tr class="formcolor"><td>{tr}Allow HTML{/tr}: </td><td><input type="checkbox" name="allowhtml" {if $allowhtml eq 'y'}checked="checked"{/if}/></td></tr>
{/if}
{if $wiki_spellcheck eq 'y'}
<tr class="formcolor"><td>{tr}Spellcheck{/tr}: </td><td><input type="checkbox" name="spellcheck" {if $spellcheck eq 'y'}checked="checked"{/if}/></td></tr>
{/if}

{if $feature_wiki_import_html eq 'y'}
<tr class="formcolor">
  <td>{tr}Import HTML{/tr}:</td>
  <td>
    <input class="wikitext" type="text" name="suck_url" value="{$suck_url|escape}" />&nbsp;
  </td>
</tr>
<tr class="formcolor">
  <td>&nbsp;</td>
  <td>
    <input type="submit" class="wikiaction" name="do_suck" value="{tr}Import{/tr}" />&nbsp;
    <input type="checkbox" name="parsehtml" {if $parsehtml eq 'y'}checked="checked"{/if}/>&nbsp;
    {tr}Try to convert HTML to wiki{/tr}
  </td>
</tr>
{/if}

{if $tiki_p_admin_wiki eq 'y'}
<tr class="formcolor"><td>{tr}Import page{/tr}:</td><td>
<input type="hidden" name="MAX_FILE_SIZE" value="1000000000" />
<input name="userfile1" type="file" />
<a href="tiki-export_wiki_pages.php?page={$page|escape:"url"}&amp;all=1" class="link">{tr}export all versions{/tr}</a>
</td></tr>
{/if}

{if $feature_wiki_pictures eq 'y' and $tiki_p_upload_picture eq 'y'}
<tr class="formcolor"><td>{tr}Upload picture{/tr}:</td><td>
<input type="hidden" name="MAX_FILE_SIZE" value="1000000000" />
<input type="hidden" name="hasAlreadyInserted" value="" />
<input type="hidden" name="prefix" value="/img/wiki_up/{if $tikidomain}{$tikidomain}/{/if}" />
<input name="picfile1" type="file" onchange="javascript:insertImgFile('editwiki','picfile1','hasAlreadyInserted','img')"/>
</td></tr>
{/if}
{if $feature_wiki_attachments == 'y' and ($tiki_p_wiki_attach_files eq 'y' or $tiki_p_wiki_admin_attachments eq 'y')}
<tr class="formcolor"><td>{tr}Upload file{/tr}:
</td><td>
<input type="hidden" name="MAX_FILE_SIZE" value="1000000000" />
<input type="hidden" name="hasAlreadyInserted2" value="" />
<input type="hidden" name="page2" value="{$page}" />
<input name="userfile2" type="file" id="attach-upload" />
 {tr}comment{/tr}:<input type="text" name="attach_comment" maxlength="250" id="attach-comment" />
<input type="submit" class="wikiaction" name="attach" value="{tr}attach{/tr}" onClick="javascript:insertImgFile('editwiki','userfile2','hasAlreadyInserted2','file', 'page2', 'attach_comment'); return true;" />
</td></tr>
{/if}
{if $feature_wiki_icache eq 'y'}
<tr class="formcolor"><td>{tr}Cache{/tr}</td><td>
    <select name="wiki_cache">
    <option value="0" {if $wiki_cache eq 0}selected="selected"{/if}>0 ({tr}no cache{/tr})</option>
    <option value="60" {if $wiki_cache eq 60}selected="selected"{/if}>1 {tr}minute{/tr}</option>
    <option value="300" {if $wiki_cache eq 300}selected="selected"{/if}>5 {tr}minutes{/tr}</option>
    <option value="600" {if $wiki_cache eq 600}selected="selected"{/if}>10 {tr}minute{/tr}</option>
    <option value="900" {if $wiki_cache eq 900}selected="selected"{/if}>15 {tr}minutes{/tr}</option>
    <option value="1800" {if $wiki_cache eq 1800}selected="selected"{/if}>30 {tr}minute{/tr}</option>
    <option value="3600" {if $wiki_cache eq 3600}selected="selected"{/if}>1 {tr}hour{/tr}</option>
    <option value="7200" {if $wiki_cache eq 7200}selected="selected"{/if}>2 {tr}hours{/tr}</option>
    </select> 
</td></tr>
{/if}

<tr class="formcolor"><td>&nbsp;</td><td><input type="hidden" name="page" value="{$page|escape}" />
<input type="submit" class="wikiaction" name="preview" value="{tr}preview{/tr}" /></td></tr>

{if $feature_antibot eq 'y' && $anon_user eq 'y'}
<tr><td class="formcolor">{tr}Anti-Bot verification code{/tr}:</td>
<td class="formcolor"><img src="tiki-random_num_img.php" alt='{tr}Random Image{/tr}'/></td></tr>
<tr><td class="formcolor">{tr}Enter the code you see above{/tr}:</td>
<td class="formcolor"><input type="text" maxlength="8" size="8" name="antibotcode" /></td></tr>
{/if}

{if $wiki_feature_copyrights  eq 'y'}
<tr class="formcolor"><td>{tr}License{/tr}:</td><td><a href="tiki-index.php?page={$wikiLicensePage}">{tr}{$wikiLicensePage}{/tr}</a></td></tr>
{if $wikiSubmitNotice neq ""}
<tr class="formcolor"><td>{tr}Important{/tr}:</td><td><b>{tr}{$wikiSubmitNotice}{/tr}</b></td>
{/if}
{/if}
{if $feature_wiki_usrlock eq 'y' && ($tiki_p_lock eq 'y' || $tiki_p_admin_wiki eq 'y')}
<tr class="formcolor"><td>{tr}Lock this page{/tr}</td><td><input type="checkbox" name="lock_it" {if $lock_it eq 'y'}checked="checked"{/if}/></td></tr>
{/if}
{if $feature_contribution eq 'y'}
{include file="contribution.tpl"}
{/if}
{if $page|lower neq 'sandbox' or $tiki_p_admin eq 'y'}
<tr class="formcolor"><td>&nbsp;</td><td>
{if $tiki_p_minor eq 'y' and $page|lower ne 'sandbox'}
<input type="submit" class="wikiaction" name="minor" value="{tr}Minor{/tr}" />
{/if}
<input type="submit" class="wikiaction" name="save" value="{tr}save{/tr}" /> &nbsp;&nbsp;
{if $feature_ajax eq 'y'}
<input type="button" class="wikiaction" value="{tr}save draft{/tr}" onclick="save_draft()">
{/if}
{if $page|lower ne 'sandbox'}
<input type="submit" class="wikiaction" name="cancel_edit" value="{tr}cancel edit{/tr}" />
{/if}
{/if}
</td></tr>
</table>
</form>
<br />

{include file=tiki-page_bar.tpl}

{include file=tiki-edit_help.tpl}


