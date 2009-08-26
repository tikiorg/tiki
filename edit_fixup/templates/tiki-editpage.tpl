{* $Id$ *}

{if $prefs.feature_ajax == 'y'}
  <script type="text/javascript" src="lib/wiki/wiki-ajax.js"></script>
{/if}

{* Display edit time out *}

<script type="text/javascript">
<!--//--><![CDATA[//><!--
{literal}

// edit timeout warnings
function editTimerTick() {
	editTimeElapsedSoFar++;
	
	var seconds = editTimeoutSeconds - editTimeElapsedSoFar;
	
	if (editTimerWarnings == 0 && seconds <= 60) {
		alert("{/literal}{tr}Your edit session will expire in{/tr} 1 {tr}minute{/tr}.{tr}You must PREVIEW or SAVE your work now, to avoid losing your edits.{/tr}{literal}");
		editTimerWarnings++;
	} else if (editTimerWarnings == 1 && seconds <= 30) {
		alert("{/literal}{tr}Your edit session will expire in{/tr} 30 {tr}seconds{/tr}.{tr}You must PREVIEW or SAVE your work now, to avoid losing your edits.{/tr}{literal}");
		editTimerWarnings++;
	} else if (editTimerWarnings == 2 && seconds <= 10) {
		alert("{/literal}{tr}Your edit session will expire in{/tr} 10 {tr}seconds{/tr}.{tr}You must PREVIEW or SAVE your work now, to avoid losing your edits.{/tr}{literal}");
	} else if (seconds <= 0) {
		clearInterval(editTimeoutIntervalId);
	}
	
	window.status = "{/literal}{tr}Your edit session will expire in{/tr}{literal}: " + Math.floor(seconds / 60) + ":" + ((seconds % 60 < 10) ? "0" : "") + (seconds % 60);
	if (seconds % 60 == 0) {
		$jq('#edittimeout').text(Math.floor(seconds / 60));
	}
}

function confirmExit() {
	if (needToConfirm) {
		{/literal}return "{tr interactive='n'}You are about to leave this page. If you have made any changes without Saving, your changes will be lost.  Are you sure you want to exit this page?{/tr}";{literal}
	}
}

window.onbeforeunload = confirmExit;
window.onload = function() { editTimeoutIntervalId = setInterval(editTimerTick, 1000) };

var needToConfirm = true;
var editTimeoutSeconds = {/literal}{$edittimeout}{literal};
var editTimeElapsedSoFar = 0;
var editTimeoutIntervalId;
var editTimerWarnings = 0;
// end edit timeout warnings

{/literal}
//--><!]]>
</script>

{if $translation_mode eq 'n'}
	{if $beingStaged eq 'y' and $prefs.wikiapproval_hideprefix == 'y'}{assign var=pp value=$approvedPageName}{else}{assign var=pp value=$page}{/if}
	{title}{if isset($hdr) && $prefs.wiki_edit_section eq 'y'}{tr}Edit Section{/tr}{else}{tr}Edit{/tr}{/if}: {$pp|escape}{if $pageAlias ne ''}&nbsp;({$pageAlias|escape}){/if}{/title}
{else}
   {title}{tr}Update '{$page}'{/tr}{/title}
{/if}
   
{if $beingStaged eq 'y'}
	<div class="tocnav">{icon _id=information style="vertical-align:middle" align="left"} 
		{if $approvedPageExists}
			{tr}You are editing the staging copy of the approved version of this page. Changes will be merged in after approval.{/tr}
		{else}
			{tr}This is a new staging page that has not been approved before.{/tr}
		{/if}
			{if $outOfSync eq 'y'}
				{tr}The current staging copy may contain changes that have yet to be approved.{/tr}
			{/if}
		{if $lastSyncVersion}
			<a class="link" href="tiki-pagehistory.php?page={$page|escape:'url'}&amp;diff2={$lastSyncVersion}" target="_blank">{tr}View changes since last approval.{/tr}</a>
		{/if}
	</div>
{/if}
{if $needsStaging eq 'y'}
	<div class="tocnav">
		{icon _id=information style="vertical-align:middle" align="left"} 
		{tr}You are editing the approved copy of this page.{/tr}
		{if $outOfSync eq 'y'}
			{tr}There are currently changes in the staging copy that have yet to be approved.{/tr}
		{/if}
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
	{remarksbox type='tip' title='{tr}Tip{/tr}'}
		{tr}The SandBox is a page where you can practice your editing skills, use the preview feature to preview the appearance of the page, no versions are stored for this page.{/tr}
	{/remarksbox}
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
					<li>
						<a href="{$likepages[back]|sefurl}" class="wiki">{$likepages[back]|escape}</a>
					</li>
				{/section}
			</ul>
		{else}
			<table class="normal"><tr>
				{cycle name=table values=',,,,</tr><tr>' print=false advance=false}
				{section name=back loop=$likepages}
					<td><a href="{$likepages[back]|sefurl}" class="wiki">{$likepages[back]|escape}</a></td>{cycle name=table}
				{/section}
			</tr></table>
		{/if}
	</div>
{/if}

{if $preview && $translation_mode eq 'n'}
	{include file='tiki-preview.tpl'}
{/if}
{if $diff_style}
<div style="overflow:auto;height:200px;">
	{include file='pagehistory.tpl'}
</div>
{/if}
<form  enctype="multipart/form-data" method="post" action="tiki-editpage.php?page={$page|escape:'url'}" id='editpageform' name='editpageform'>
	{if $diff_style}
		<select name="diff_style">
		
			{if $diff_style eq "htmldiff"}
				<option value="htmldiff" selected="selected">html</option>
			{else}
				<option value="htmldiff">html</option>
			{/if}
			{if $diff_style eq "inlinediff"}
				<option value="inlinediff" selected="selected">text</option>
			{else}
				<option value="inlinediff">text</option>
			{/if}   
		</select>
		<input type="submit" class="wikiaction" onmouseover="return overlib('{tr}Change the style used to display differences to be translated.{/tr}');" onmouseout="nd();" name="preview" value="{tr}Change diff styles{/tr}" onclick="needToConfirm=false;" />
	{/if}
	
	{if $page_ref_id}<input type="hidden" name="page_ref_id" value="{$page_ref_id}" />{/if}
	{if isset($hdr)}<input type="hidden" name="hdr" value="{$hdr}" />{/if}
	{if isset($cell)}<input type="hidden" name="cell" value="{$cell}" />{/if}
	{if isset($pos)}<input type="hidden" name="pos" value="{$pos}" />{/if}
	{if $current_page_id}<input type="hidden" name="current_page_id" value="{$current_page_id}" />{/if}
	{if $add_child}<input type="hidden" name="add_child" value="true" />{/if}
	
	{if $page|lower neq 'sandbox'}
		{remarksbox type='tip' title='{tr}Tip{/tr}'}
		{tr}This edit session will expire in{/tr} <span id="edittimeout">{math equation='x / y' x=$edittimeout y=60}</span> {tr}minutes{/tr}. {tr}<strong>Preview</strong> or <strong>Save</strong> your work to restart the edit session timer.{/tr}
		{/remarksbox}
	{/if}
	
	{if ( $preview && $staging_preview neq 'y' ) or $prefs.wiki_actions_bar eq 'top' or $prefs.wiki_actions_bar eq 'both'}
		<div class='top_actions'>
			{include file='wiki_edit_actions.tpl'}
		</div>
	{/if}
	
	{if $prefs.feature_wysiwyg eq 'y' and $prefs.wysiwyg_optional eq 'y' and !isset($hdr)}
		{if $wysiwyg ne 'y'}
			<input type="submit" class="wikiaction" onmouseover="return overlib('{tr}Switch to WYSIWYG editor.{/tr}');" onmouseout="nd();" name="mode_wysiwyg" value="{tr}Use wysiwyg editor{/tr}" onclick="needToConfirm=false;" />
		{else}
			<input type="submit" class="wikiaction" onmouseover="return overlib('{tr}Switch to normal (wiki) editor.{/tr}');" onmouseout="nd();" name="mode_normal" value="{tr}Use normal editor{/tr}" onclick="needToConfirm=false;" />
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
			{if $page|lower ne 'sandbox'}
				{include file='categorize.tpl'}
			{/if}{* sandbox *}
		{/if}
		
		{include file='structures.tpl'}
		
		{if $prefs.feature_wiki_templates eq 'y' and $tiki_p_use_content_templates eq 'y' and !$templateId}
			<tr class="formcolor">
				<td><label for="templateId">{tr}Apply template{/tr}:</label></td>
				<td>
					<select id="templateId" name="templateId" onchange="javascript:document.getElementById('editpageform').submit();" onclick="needToConfirm = false;">
						<option value="0">{tr}none{/tr}</option>
						{section name=ix loop=$templates}
						<option value="{$templates[ix].templateId|escape}" {if $templateId eq $templates[ix].templateId}selected="selected"{/if}>{tr}{$templates[ix].name}{/tr}</option>
						{/section}
					</select>
					{if $tiki_p_edit_content_templates eq 'y'}
						<a style="align=right;" href="tiki-admin_content_templates.php" class="link" onclick="needToConfirm = true;">{tr}Admin Content Templates{/tr}</a>
					{/if}
				</td>
			</tr>
		{/if}
		
		{if $prefs.feature_wiki_ratings eq 'y' and $tiki_p_wiki_admin_ratings eq 'y'}
			<tr class="formcolor">
				<td>{tr}Use rating{/tr}:</td>
				<td>
					{if $poll_rated.info}
						<input type="hidden" name="poll_title" value="{$poll_rated.info.title|escape}" />
						<a href="tiki-admin_poll_options.php?pollId={$poll_rated.info.pollId}">{$poll_rated.info.title}</a>
						{assign var=thispage value=$page|escape:"url"}
						{assign var=thispoll_rated value=$poll_rated.info.pollId}
						{button href="?page=$thispage&amp;removepoll=$thispoll_rated" _text="{tr}Disable{/tr}"}
						{if $tiki_p_admin_poll eq 'y'}
							{button href="tiki-admin_polls.php" _text="{tr}Admin Polls{/tr}"}
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
				</td>
			</tr>
		{/if}
		{if $prefs.feature_wiki_description eq 'y' or $prefs.metatag_pagedesc eq 'y'}
		<tr class="formcolor">
			{if $prefs.metatag_pagedesc eq 'y'}
				<td><label for="description">{tr}Description (used for metatags){/tr}:</label></td>
			{else}
				<td><label for="description">{tr}Description{/tr}:</label></td>
			{/if}
			<td><input style="width:98%;" type="text" id="description" name="description" value="{$description|escape}" /></td>
		</tr>
		{/if}
		<tr class="formcolor">
			<td colspan="2">
				{if $wysiwyg ne 'y' or $prefs.javascript_enabled ne 'y'}
					{*include file='wiki_edit.tpl'*}
<!--					<input type="hidden" name="rows" value="{$rows}"/>-->
<!--					<input type="hidden" name="cols" value="{$cols}"/>-->
<!--					<input type="hidden" name="wysiwyg" value="n" />-->
					{textarea _toolbars="y"}{$pagedata}{/textarea}
				{else}
					{capture name=autosave}
						{if $prefs.feature_ajax eq 'y' and $prefs.feature_ajax_autosave eq 'y' and $noautosave neq 'y'}
							{autosave test='n' id='edit' default=$pagedata preview=$preview}
						{else}
							{$pagedata}
						{/if}
					{/capture}
					{if $prefs.feature_ajax eq 'y' and $prefs.feature_ajax_autosave eq 'y' and $noautosave neq 'y' and $has_autosave eq 'y'}
						{remarksbox type="warning" title="{tr}AutoSave{/tr}"}
							{tr}If you want the saved version instead of the autosaved one{/tr}&nbsp;{self_link noautosave='y' _ajax='n'}{tr}Click Here{/tr}{/self_link}
						{/remarksbox}
					{/if}
				</td>
			</tr>
			<tr>
				<td colspan="2">
					{editform Meat=$smarty.capture.autosave InstanceName='edit' ToolbarSet="Tiki"}
					<input type="hidden" name="wysiwyg" value="y" />
				{/if}
			</td>
		</tr>
		
		{if $prefs.feature_wiki_replace eq 'y'}
			<script type="text/javascript">
<!--//--><![CDATA[//><!--
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
//--><!]]>
			</script>
			<tr class="formcolor">
				<td><label for="search">{tr}Regex search {/tr}:</label></td>
				<td>
					<input style="width:100;" class="wikiedit" type="text" id="search"/>
					<label>{tr}Replace to{/tr}:
					<input style="width:100;" class="wikiedit" type="text" id="replace"/></label>
					<label><input type="checkbox" id="caseinsens" />{tr}Case Insensitivity{/tr}</label>
					<input type="button" value="{tr}Replace{/tr}" onclick="javascript:searchrep();">
				</td>
			</tr>
		{/if}
		
		{if $prefs.feature_wiki_footnotes eq 'y'}
			{if $user}
				<tr class="formcolor">
					<td><label for="footnote">{tr}My Footnotes{/tr}:</label></td>
					<td><textarea id="footnote" name="footnote" rows="8" cols="42" style="width:98%;" >{$footnote|escape}</textarea></td>
				</tr>
			{/if}
		{/if}
		{if $prefs.feature_multilingual eq 'y'}
			{if not($data.page_id)}
				<tr class="formcolor">
					<td><label for="lang">{tr}Language{/tr}:</label></td>
					<td>
						<select name="lang" id="lang">
							<option value="">{tr}Unknown{/tr}</option>
							{section name=ix loop=$languages}
								<option value="{$languages[ix].value|escape}"{if $lang eq $languages[ix].value or (not($data.page_id) and $lang eq '' and $languages[ix].value eq $prefs.language)} selected="selected"{/if}>{$languages[ix].name}</option>
							{/section}
						</select>
						{if $translationOf}
							<input type="hidden" name="translationOf" value="{$translationOf|escape}"/>
						{/if}
					</td>
				</tr>
			{else}
				{if $trads|@count > 1}
					<tr class="formcolor"{if $prefs.feature_urgent_translation neq 'y' or $diff_style} style="display:none;"{/if}>
						<td><label>{tr}Translation request{/tr}:</td>
						<td>
							<input type="hidden" name="lang" value="{$lang|escape}"/>
							<input type="checkbox" id="translation_critical" name="translation_critical" id="translation_critical"{if $translation_critical} checked="checked"{/if}/>
							<label for="translation_critical">{tr}Send urgent translation request.{/tr}</label>
							{if $diff_style}
								<input type="hidden" name="oldver" value="{$diff_oldver|escape}"/>
								<input type="hidden" name="newver" value="{$diff_newver|escape}"/>
								<input type="hidden" name="source_page" value="{$source_page|escape}"/>
							{/if}
						</td>
					</tr>
				{/if}
			{/if}
		{/if}
		
		{if $page|lower neq 'sandbox'}
			<tr class="formcolor" id="input_edit_summary" style="vertical-align: middle">
				<td style="width: 25%"><label for="comment">{tr}Edit Comment{/tr}:</label></td>
				<td><input style="width:98%;" class="wikiedit" type="text" id="comment" name="comment" value="{$commentdata|escape}" /></td>
			</tr>
			{if $show_watch eq 'y'}
				<tr class="formcolor">
					<td><label for="watch">{tr}Monitor this page{/tr}:</label></td>
					<td><input type="checkbox" id="watch" name="watch" value="1"{if $watch_checked eq 'y'} checked="checked"{/if} /></td>
				</tr>
			{/if}
			{if $prefs.wiki_feature_copyrights  eq 'y'}
				<tr class="formcolor">
					<td>{tr}Copyright{/tr}:</td>
					<td>
						<table border="0">
							<tr class="formcolor">
								<td><label for="copyrightTitle">{tr}Title:{/tr}</label></td>
								<td><input size="40" class="wikiedit" type="text" id="copyrightTitle" name="copyrightTitle" value="{$copyrightTitle|escape}" /></td>
								{if !empty($copyrights)}
									<td rowspan="3"><a href="copyrights.php?page={$page|escape}">{tr}To edit the copyright notices{/tr}</a></td>
								{/if}
							</tr>
							<tr class="formcolor">
								<td><label for="copyrightYear">{tr}Year:{/tr}</label></td>
								<td><input size="4" class="wikiedit" type="text" id="copyrightYear" name="copyrightYear" value="{$copyrightYear|escape}" /></td>
							</tr>
							<tr class="formcolor">
								<td><label for="copyrightAuthors">{tr}Authors:{/tr}</label></td>
								<td><input size="40" class="wikiedit" id="copyrightAuthors" name="copyrightAuthors" type="text" value="{$copyrightAuthors|escape}" /></td>
							</tr>
						</table>
					</td>
				</tr>
			{/if}
			{if $prefs.feature_freetags eq 'y' and $tiki_p_freetags_tag eq 'y'}
				{include file='freetag.tpl'}
			{/if}
		{/if}
		
		{if $prefs.feature_wiki_allowhtml eq 'y' and $tiki_p_use_HTML eq 'y' and $wysiwyg neq 'y'}
			<tr class="formcolor">
				<td><label for="allowhtml">{tr}Allow HTML{/tr}:</label></td>
				<td><input type="checkbox" id="allowhtml" name="allowhtml" {if $allowhtml eq 'y'}checked="checked"{/if}/></td>
			</tr>
		{/if}
		{if $prefs.wiki_spellcheck eq 'y'}
			<tr class="formcolor">
				<td><label for="spellcheck">{tr}Spellcheck{/tr}:</label></td>
				<td><input type="checkbox" id="spellcheck"name="spellcheck" {if $spellcheck eq 'y'}checked="checked"{/if}/></td>
			</tr>
		{/if}
		{if $prefs.feature_wiki_import_html eq 'y'}
			<tr class="formcolor">
				<td><label for="suck_url">{tr}Import HTML{/tr}:</label></td>
				<td>
					<input class="wikiedit" type="text" id="suck_url" name="suck_url" value="{$suck_url|escape}" />&nbsp;
				</td>
			</tr>
			<tr class="formcolor">
				<td>&nbsp;</td>
				<td>
					<input type="submit" class="wikiaction" name="do_suck" value="{tr}Import{/tr}" onclick="needToConfirm=false;" />&nbsp;
					<label><input type="checkbox" name="parsehtml" {if $parsehtml eq 'y'}checked="checked"{/if}/>&nbsp;
					{tr}Try to convert HTML to wiki{/tr}. </label>
				</td>
			</tr>
		{/if}
		
		{if $tiki_p_admin_wiki eq 'y' && $prefs.feature_wiki_import_page eq 'y'}
			<tr class="formcolor">
				<td><label for="userfile1">{tr}Import page{/tr}:</label></td>
				<td>
					<input type="hidden" name="MAX_FILE_SIZE" value="1000000000" />
					<input id="userfile1" name="userfile1" type="file" />
					{if $prefs.feature_wiki_export eq 'y' and $tiki_p_admin_wiki eq 'y'}
						<a href="tiki-export_wiki_pages.php?page={$page|escape:"url"}&amp;all=1" class="link">{tr}export all versions{/tr}</a>
					{/if}
				</td>
			</tr>
		{/if}
		
		{if $wysiwyg neq 'y'}
			{if $prefs.feature_wiki_pictures eq 'y' and $tiki_p_upload_picture eq 'y'}
				<tr class="formcolor">
					<td><label for="uploadpicture">{tr}Upload picture{/tr}:</label></td>
					<td>
						{if $prefs.feature_filegals_manager eq 'y' and $prefs.feature_file_galleries == 'y' and $tiki_p_list_file_galleries == 'y'}
							<input type="submit" class="wikiaction" value="{tr}Add another image{/tr}" onclick="javascript:needToConfirm = false;javascript:openFgalsWindow('{filegal_manager_url area_name=editwiki}');return false;" name="uploadpicture" />
						{else}
							<input type="hidden" name="MAX_FILE_SIZE" value="1000000000" />
							<input type="hidden" name="hasAlreadyInserted" value="" />
							<input type="hidden" name="prefix" value="/img/wiki_up/{if $tikidomain}{$tikidomain}/{/if}" />
							<input name="picfile1" type="file" onchange="javascript:insertImgFile('editwiki','picfile1','hasAlreadyInserted','img')"/>
							<div id="new_img_form"></div>
							<a href="javascript:addImgForm()" onclick="needToConfirm = false;">{tr}Add another image{/tr}</a>
						{/if}
					</td>
				</tr>
			{/if}
		
			{if $prefs.feature_wiki_attachments == 'y' and ($tiki_p_wiki_attach_files eq 'y' or $tiki_p_wiki_admin_attachments eq 'y')}
				<tr class="formcolor">
					<td><label for="page2">{tr}Upload file{/tr}:</label></td>
					<td>
						<input type="hidden" name="MAX_FILE_SIZE" value="1000000000" />
						<input type="hidden" name="hasAlreadyInserted2" value="" />
						<input type="hidden" id="page2" name="page2" value="{$page}" />
						<input name="userfile2" type="file" id="attach-upload" />
						 <label>{tr}Comment{/tr}:<input type="text" name="attach_comment" maxlength="250" id="attach-comment" /></label>
						<input type="submit" class="wikiaction" name="attach" value="{tr}Attach{/tr}" onclick="javascript:needToConfirm=false;insertImgFile('editwiki','userfile2','hasAlreadyInserted2','file', 'page2', 'attach_comment'); return true;" />
					</td>
				</tr>
			{/if}
		{/if}
		
		{if $page|lower ne 'sandbox'}
			{if $prefs.feature_wiki_icache eq 'y'}
				<tr class="formcolor">
					<td><label for="wiki_cache">{tr}Cache{/tr}</a></td>
					<td>
					    <select id="wiki_cache" name="wiki_cache">
						    <option value="0" {if $prefs.wiki_cache eq 0}selected="selected"{/if}>0 ({tr}no cache{/tr})</option>
						    <option value="60" {if $prefs.wiki_cache eq 60}selected="selected"{/if}>1 {tr}minute{/tr}</option>
						    <option value="300" {if $prefs.wiki_cache eq 300}selected="selected"{/if}>5 {tr}minutes{/tr}</option>
						    <option value="600" {if $prefs.wiki_cache eq 600}selected="selected"{/if}>10 {tr}minute{/tr}</option>
						    <option value="900" {if $prefs.wiki_cache eq 900}selected="selected"{/if}>15 {tr}minutes{/tr}</option>
						    <option value="1800" {if $prefs.wiki_cache eq 1800}selected="selected"{/if}>30 {tr}minute{/tr}</option>
						    <option value="3600" {if $prefs.wiki_cache eq 3600}selected="selected"{/if}>1 {tr}hour{/tr}</option>
						    <option value="7200" {if $prefs.wiki_cache eq 7200}selected="selected"{/if}>2 {tr}hours{/tr}</option>
					    </select> 
					</td>
				</tr>
			{/if}
			{if $prefs.feature_antibot eq 'y' && $anon_user eq 'y'}
				{include file='antibot.tpl' tr_style="formcolor"}
			{/if}
			
			{if $prefs.wiki_feature_copyrights  eq 'y'}
				<tr class="formcolor">
					<td>{tr}License{/tr}:</td>
					<td><a href="{$prefs.wikiLicensePage|sefurl}">{tr}{$prefs.wikiLicensePage}{/tr}</a></td>
				</tr>
				{if $prefs.wikiSubmitNotice neq ""}
					<tr class="formcolor">
						<td>{tr}Important{/tr}:</td>
						<td><b>{tr}{$prefs.wikiSubmitNotice}{/tr}</b></td>
					</tr>
				{/if}
			{/if}
			
			{if $prefs.feature_wiki_usrlock eq 'y' && ($tiki_p_lock eq 'y' || $tiki_p_admin_wiki eq 'y')}
				<tr class="formcolor">
					<td><label for="lock_it">{tr}Lock this page{/tr}</label></td>
					<td><input type="checkbox" id="lock_it" name="lock_it" {if $lock_it eq 'y'}checked="checked"{/if}/></td>
				</tr>
			{/if}
			
			{if $prefs.feature_contribution eq 'y'}
				{include file='contribution.tpl'}
			{/if}
			
			{if $tiki_p_admin_wiki eq 'y' && $prefs.wiki_authors_style_by_page eq 'y'}
			  {include file='wiki_authors_style.tpl' tr_class='formcolor' wiki_authors_style_site='y' style='tr'}
			{/if}
		{/if}{* sandbox *}
		
		{if $prefs.wiki_actions_bar neq 'top'}
			<tr class="formcolor">
				<td colspan="2" style="text-align:center;">
					{include file='wiki_edit_actions.tpl'}
				</td>
			</tr>
		{/if}
	</table>
	{if $prefs.feature_wiki_allowhtml eq 'y' and $tiki_p_use_HTML eq 'y' and $wysiwyg eq 'y' and $allowhtml eq 'y'}
	  <input type="hidden" name="allowhtml" checked="checked"/>
	{/if}
</form>
{include file='tiki-page_bar.tpl'}
