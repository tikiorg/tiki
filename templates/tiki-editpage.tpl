{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-editpage.tpl,v 1.130.2.5 2007-11-22 22:42:23 nkoth Exp $ *}
{popup_init src="lib/overlib.js"}
{if $prefs.feature_ajax == 'y'}
  <script language="JavaScript" src="lib/wiki/wiki-ajax.js"></script>
{/if}
<h1>{tr}Edit{/tr}: {if $beingStaged eq 'y' and $prefs.wikiapproval_hideprefix == 'y'}{$approvedPageName|escape}{else}{$page|escape}{/if}{if $pageAlias ne ''}&nbsp;({$pageAlias|escape}){/if}</h1>
{if $beingStaged eq 'y'}
<div class="tocnav">
{tr}You are editing the staging copy of the approved version of this page. Changes will be merged in after approval.{/tr}
{if $outOfSync eq 'y'} {tr}There are currently changes that have yet to be synchronized.{/tr}{/if}
</div>
{/if}
{if $needsStaging eq 'y'}
<div class="tocnav">
{tr}You are editing the approved copy of this page.{/tr}
{if $outOfSync eq 'y'} {tr}There are currently changes in the staging copy that have yet to be synchronized.{/tr}{/if}
 {tr}Are you sure you do not want to edit{/tr} <a class="link" href="tiki-editpage.php?page={$stagingPageName|escape:'url'}">{tr}the staging copy{/tr}</a> {tr}instead?{/tr}
</div>
{/if}
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
<input type="submit" class="wikiaction" name="preview" value="{tr}Preview{/tr}" onclick="needToConfirm = false;" />
{if $page|lower neq 'sandbox'}
{if $tiki_p_minor eq 'y'}
  <input type="submit" class="wikiaction" name="minor" value="{tr}Minor{/tr}" onclick="needToConfirm=false;" />
{/if}
<input type="submit" class="wikiaction" name="save" value="{tr}Save{/tr}" onclick="needToConfirm = false;" /> &nbsp;&nbsp; <input type="submit" class="wikiaction" name="cancel_edit" value="{tr}Cancel Edit{/tr}" />
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
{if $prefs.feature_wysiwyg eq 'y' and $prefs.wysiwyg_optional eq 'y'}
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
{if $prefs.feature_wiki_categorize_structure eq 'y'}
<tr class="formcolor"><td colspan="2">{tr}Categories will be inherited from the structure top page{/tr}</td></tr>
{/if}
{else}
{if $tiki_p_view_categories eq 'y'}
{include file=categorize.tpl}
{/if}
{/if}
{include file=structures.tpl}
{if $prefs.feature_wiki_templates eq 'y' and $tiki_p_use_content_templates eq 'y' and !$templateId}
  <tr class="formcolor">
    <td>{tr}Apply template{/tr}:</td>
    <td>
      <select name="templateId" onchange="javascript:document.getElementById('editpageform').submit();" onclick="needToConfirm = false;">
      <option value="0">{tr}none{/tr}</option>
      {section name=ix loop=$templates}
        <option value="{$templates[ix].templateId|escape}" {if $templateId eq $templates[ix].templateId}selected="selected"{/if}>{tr}{$templates[ix].name}{/tr}</option>
      {/section}
      </select>
    </td>
  </tr>
{/if}
{if $prefs.feature_wiki_ratings eq 'y' and $tiki_p_wiki_admin_ratings eq 'y'}
<tr class="formcolor"><td>{tr}Use rating{/tr}:</td><td>
{if $poll_rated.info}
<input type="hidden" name="poll_title" value="{$poll_rated.info.title|escape}" />
<a href="tiki-admin_poll_options.php?pollId={$poll_rated.info.pollId}">{$poll_rated.info.title}</a>
<span class="button2"><a class="linkbut" href="tiki-editpage.php?page={$page|escape:"url"}&amp;removepoll={$poll_rated.info.pollId}">{tr}disable{/tr}</a>
{if $tiki_p_admin_poll eq 'y'}
<span class="button2"><a class="linkbut" href="tiki-admin_polls.php">{tr}Admin Polls{/tr}</a></span>
{/if}
{else}
{if count($polls_templates)}
{tr}Type{/tr}
<select name="poll_template">
<option value="0">{tr}none{/tr}</option>
{section name=ix loop=$polls_templates}
<option value="{$polls_templates[ix].pollId|escape}"{if $polls_templates[ix].pollId eq $poll_template} selected="selected"{/if}>{tr}{$polls_templates[ix].title}{/tr}</option>
{/section}
</select>
{tr}Title{/tr}
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
{if $prefs.feature_smileys eq 'y'&&!$wysiwyg}
<tr class="formcolor"><td>{tr}Smileys{/tr}:</td><td>
{include file="tiki-smileys.tpl" area_name='editwiki'}
</td>
</tr>
{/if}
{if $prefs.feature_wiki_description eq 'y'}
  <tr class="formcolor">
    <td>{tr}Description{/tr}:</td>
    <td><input style="width:98%;" type="text" name="description" value="{$description|escape}" /></td>
  </tr>
{/if}
<tr class="formcolor">
{if $wysiwyg ne 'y'}
<td>
{tr}Edit{/tr}:<br /><br />
{include file="textareasize.tpl" area_name='editwiki' formId='editpageform' ToolbarSet='Tiki'}<br /><br />
{if $prefs.quicktags_over_textarea neq 'y'}
  {include file=tiki-edit_help_tool.tpl area_name='editwiki'}
{/if}
</td>
<td>
{if $wysiwyg ne 'y' and $prefs.quicktags_over_textarea eq 'y'}
  {include file=tiki-edit_help_tool.tpl area_name='editwiki'}
{/if}
<textarea id='editwiki' class="wikiedit" name="edit" rows="{$rows}" cols="{$cols}" style="WIDTH: 98%;">{$pagedata|escape:'htmlall':'UTF-8'}</textarea>
<input type="hidden" name="rows" value="{$rows}"/>
<input type="hidden" name="cols" value="{$cols}"/>
{else}
<td colspan="2">
{editform Meat=$pagedata InstanceName='edit' ToolbarSet="Tiki"}
{/if}
</td></tr>
{if $prefs.feature_wiki_replace eq 'y'}
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
<tr class="formcolor"><td>{tr}Regex search {/tr}:</td><td>
<input style="width:100;" class="wikitext" type="text" id="search"/>
{tr}Replace to{/tr}:
<input style="width:100;" class="wikitext" type="text" id="replace"/>
<input type="checkbox" id="caseinsens" />{tr}Case Insensitivity{/tr}
<input type="button" value="{tr}Replace{/tr}" onclick="javascript:searchrep();">
</td></tr>
{/if}
{if $prefs.feature_wiki_footnotes eq 'y'}
{if $user}
<tr class="formcolor"><td>{tr}My Footnotes{/tr}:</td><td><textarea name="footnote" rows="8" cols="42" style="width:98%;" >{$footnote|escape}</textarea></td></tr>
{/if}
{/if}
{if $prefs.feature_multilingual eq 'y'}
<tr class="formcolor"><td>{tr}Language{/tr}:</td><td>
<select name="lang">
<option value="">{tr}Unknown{/tr}</option>
{section name=ix loop=$languages}
<option value="{$languages[ix].value|escape}"{if $lang eq $languages[ix].value} selected="selected"{/if}>{$languages[ix].name}</option>
{/section}
</select>
</td></tr>
{*<tr class="formcolor"><td>{tr}Is a translation of this page:{/tr}</td><td><input style="width:98%;" type="text" name="translation" value="{$translation|escape}" /></td></tr>*}
{/if}
{if $page|lower neq 'sandbox'}
<tr class="formcolor" id="input_edit_summary"><td>{tr}Edit Summary{/tr}:</td><td><input style="width:98%;" class="wikitext" type="text" name="comment" value="{$commentdata|escape}" /></td></tr>
{if $prefs.wiki_feature_copyrights  eq 'y'}
<tr class="formcolor"><td>{tr}Copyright{/tr}:</td><td>
<table border="0">
<tr class="formcolor"><td>{tr}Title:{/tr}</td><td><input size="40" class="wikitext" type="text" name="copyrightTitle" value="{$copyrightTitle|escape}" /></td>
{if !empty($copyrights)}<td rowspan="3"><a href="copyrights.php?page={$page|escape}">{tr}To edit the copyright notices{/tr}</a></td>{/if}</tr>
<tr class="formcolor"><td>{tr}Year:{/tr}</td><td><input size="4" class="wikitext" type="text" name="copyrightYear" value="{$copyrightYear|escape}" /></td></tr>
<tr class="formcolor"><td>{tr}Authors:{/tr}</td><td><input size="40" class="wikitext" name="copyrightAuthors" type="text" value="{$copyrightAuthors|escape}" /></td></tr>
</table>
</td></tr>
{/if}
{/if}
{if $prefs.feature_freetags eq 'y' and $tiki_p_freetags_tag eq 'y'}
  {include file=freetag.tpl}
{/if}
{if $prefs.feature_wiki_allowhtml eq 'y' and $tiki_p_use_HTML eq 'y' and $wysiwyg neq 'y'}
<tr class="formcolor"><td>{tr}Allow HTML{/tr}: </td><td><input type="checkbox" name="allowhtml" {if $allowhtml eq 'y'}checked="checked"{/if}/></td></tr>
{/if}
{if $prefs.wiki_spellcheck eq 'y'}
<tr class="formcolor"><td>{tr}Spellcheck{/tr}: </td><td><input type="checkbox" name="spellcheck" {if $spellcheck eq 'y'}checked="checked"{/if}/></td></tr>
{/if}
{if $prefs.feature_wiki_import_html eq 'y'}
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
{if $tiki_p_admin_wiki eq 'y' && $prefs.feature_wiki_import_page eq 'y'}
<tr class="formcolor"><td>{tr}Import page{/tr}:</td><td>
<input type="hidden" name="MAX_FILE_SIZE" value="1000000000" />
<input name="userfile1" type="file" />
{if $prefs.feature_wiki_export eq 'y' and $tiki_p_admin_wiki eq 'y'}
<a href="tiki-export_wiki_pages.php?page={$page|escape:"url"}&amp;all=1" class="link">{tr}export all versions{/tr}</a>
{/if}
</td></tr>
{/if}
{if $wysiwyg neq 'y'}
{if $prefs.feature_wiki_pictures eq 'y' and $tiki_p_upload_picture eq 'y'}
<tr class="formcolor"><td>{tr}Upload picture{/tr}:</td><td>
{if $prefs.feature_filegals_manager eq 'y'}
<input type="submit" class="wikiaction" value="{tr}Add another image{/tr}" onclick="javascript:needToConfirm = false;javascript:window.open('{$url_path}tiki-file_galleries.php?filegals_manager','_blank','menubar=1,scrollbars=1,resizable=1,height=400,width=800');return false;">
{else}
<input type="hidden" name="MAX_FILE_SIZE" value="1000000000" />
<input type="hidden" name="hasAlreadyInserted" value="" />
<input type="hidden" name="prefix" value="/img/wiki_up/{if $tikidomain}{$tikidomain}/{/if}" />
<input name="picfile1" type="file" onchange="javascript:insertImgFile('editwiki','picfile1','hasAlreadyInserted','img')"/>
<div id="new_img_form"></div>
<a href="javascript:addImgForm()" onclick="needToConfirm = false;">{tr}Add another image{/tr}</a>
{/if}
</td></tr>
{/if}
{if $prefs.feature_wiki_attachments == 'y' and ($tiki_p_wiki_attach_files eq 'y' or $tiki_p_wiki_admin_attachments eq 'y')}
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
{/if}
{if $prefs.feature_wiki_icache eq 'y'}
<tr class="formcolor"><td>{tr}Cache{/tr}</td><td>
    <select name="wiki_cache">
    <option value="0" {if $prefs.wiki_cache eq 0}selected="selected"{/if}>0 ({tr}no cache{/tr})</option>
    <option value="60" {if $prefs.wiki_cache eq 60}selected="selected"{/if}>1 {tr}minute{/tr}</option>
    <option value="300" {if $prefs.wiki_cache eq 300}selected="selected"{/if}>5 {tr}minutes{/tr}</option>
    <option value="600" {if $prefs.wiki_cache eq 600}selected="selected"{/if}>10 {tr}minute{/tr}</option>
    <option value="900" {if $prefs.wiki_cache eq 900}selected="selected"{/if}>15 {tr}minutes{/tr}</option>
    <option value="1800" {if $prefs.wiki_cache eq 1800}selected="selected"{/if}>30 {tr}minute{/tr}</option>
    <option value="3600" {if $prefs.wiki_cache eq 3600}selected="selected"{/if}>1 {tr}hour{/tr}</option>
    <option value="7200" {if $prefs.wiki_cache eq 7200}selected="selected"{/if}>2 {tr}hours{/tr}</option>
    </select> 
</td></tr>
{/if}
{if $prefs.feature_antibot eq 'y' && $anon_user eq 'y'}
{include file=antibot.tpl}
{/if}
{if $prefs.wiki_feature_copyrights  eq 'y'}
<tr class="formcolor"><td>{tr}License{/tr}:</td><td><a href="tiki-index.php?page={$prefs.wikiLicensePage}">{tr}{$prefs.wikiLicensePage}{/tr}</a></td></tr>
{if $prefs.wikiSubmitNotice neq ""}
<tr class="formcolor"><td>{tr}Important{/tr}:</td><td><b>{tr}{$prefs.wikiSubmitNotice}{/tr}</b></td>
{/if}
{/if}
{if $prefs.feature_wiki_usrlock eq 'y' && ($tiki_p_lock eq 'y' || $tiki_p_admin_wiki eq 'y')}
<tr class="formcolor"><td>{tr}Lock this page{/tr}</td><td><input type="checkbox" name="lock_it" {if $lock_it eq 'y'}checked="checked"{/if}/></td></tr>
{/if}
{if $prefs.feature_contribution eq 'y'}
{include file="contribution.tpl"}
{/if}
{if $page|lower neq 'sandbox' or $tiki_p_admin eq 'y'}
<tr class="formcolor">
<td>&nbsp;</td>
<td>
<input type="hidden" name="page" value="{$page|escape}" />
<input type="submit" class="wikiaction" name="preview" value="{tr}Preview{/tr}" onclick="needToConfirm=false;" />
{if $tiki_p_minor eq 'y' and $page|lower ne 'sandbox'}
  <input type="submit" class="wikiaction" name="minor" value="{tr}Minor{/tr}" onclick="needToConfirm=false;" />
{/if}
<input type="submit" class="wikiaction" name="save" value="{tr}Save{/tr}" onclick="needToConfirm=false;" />
{if $prefs.feature_ajax eq 'y'}
  <input type="submit" class="wikiaction" value="{tr}Save Draft{/tr}" onclick="save_draft()" />
{/if}
{if $page|lower ne 'sandbox'}
  <input type="submit" class="wikiaction" name="cancel_edit" value="{tr}Cancel Edit{/tr}" onclick="needToConfirm = false;" />
{/if}
{/if}
</td></tr>
</table>
{if $prefs.feature_wiki_allowhtml eq 'y' and $tiki_p_use_HTML eq 'y' and $wysiwyg eq 'y' and $allowhtml eq 'y'}
  <input type="hidden" name="allowhtml" checked="checked"/>
{/if}
</form>
<br />
{include file="tiki-page_bar.tpl"}
{include file=tiki-edit_help.tpl}
