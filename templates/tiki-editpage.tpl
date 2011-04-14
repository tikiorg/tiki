{* $Id$ *}
{if $page|lower neq 'sandbox' and $prefs.feature_contribution eq 'y' and $prefs.feature_contribution_mandatory eq 'y'}
	{remarksbox type='tip' title="{tr}Tip{/tr}"}
		<strong class='mandatory_note'>{tr}Fields marked with a * are mandatory.{/tr}</strong>
	{/remarksbox}
{/if}
{if $customTip}
	{remarksbox type='tip' title=$customTipTitle}
	{tr}{$customTip|escape}{/tr}
	{/remarksbox}
{/if}
{if $wikiHeaderTpl}
	{include file="wiki:$wikiHeaderTpl"}
{/if}
	
{if $prefs.ajax_autosave eq "y"}
<div class="floatright">
	{self_link _icon="magnifier" _class="previewBtn" _ajax="n"}{tr}Preview your changes.{/tr}{/self_link}
</div>
{jq} $(".previewBtn").click(function(){
	if ($('#autosave_preview:visible').length === 0) {
		auto_save_data['editwiki'] = "";
		auto_save('editwiki', autoSaveId);
		if (!ajaxPreviewWindow) {
			$('#autosave_preview').slideDown('slow', function(){ ajax_preview( 'editwiki', autoSaveId, true );});
		}
	} else {
		$('#autosave_preview').slideUp('slow');
	}
	return false;
});{/jq}
{/if}
{if $translation_mode eq 'n'}
	{if $beingStaged eq 'y' and $prefs.wikiapproval_hideprefix == 'y'}{assign var=pp value=$approvedPageName}{else}{assign var=pp value=$page}{/if}
	{title}{if isset($hdr) && $prefs.wiki_edit_section eq 'y'}{tr}Edit Section{/tr}{else}{tr}Edit{/tr}{/if}: {$pp|escape}{if $pageAlias ne ''}&nbsp;({$pageAlias|escape}){/if}{/title}
{else}
   {title}{tr}Update '{$page|escape}'{/tr}{/title}
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
	{remarksbox type='tip' title="{tr}Tip{/tr}"}
		{tr}The SandBox is a page where you can practice your editing skills, use the preview feature to preview the appearance of the page, no versions are stored for this page.{/tr}
	{/remarksbox}
{/if}
{if $category_needed eq 'y'}
	{remarksbox type='Warning' title="{tr}Warning{/tr}"}
	<div class="highlight"><em class='mandatory_note'>{tr}A category is mandatory{/tr}</em></div>
	{/remarksbox}
{/if}
{if $contribution_needed eq 'y'}
	{remarksbox type='Warning' title="{tr}Warning{/tr}"}
	<div class="highlight"><em class='mandatory_note'>{tr}A contribution is mandatory{/tr}</em></div>
	{/remarksbox}
{/if}
{if $summary_needed eq 'y'}
	{remarksbox type='Warning' title="{tr}Warning{/tr}"}
	<div class="highlight"><em class='mandatory_note'>{tr}An edit summary is mandatory{/tr}</em></div>
	{/remarksbox}
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

{if $preview or $prefs.ajax_autosave eq "y"}
	{include file='tiki-preview.tpl'}
{/if}
{if $diff_style}
	<div id="diff_outer">
		<div style="overflow:auto;height:20ex;" id="diff_history">
			{if $translation_mode == 'y'}		
				<h2>{tr}Translate from:{/tr} {$source_page|escape}</h2>
				{tr}Changes that need to be translated are highlighted below.{/tr}
			{/if}
			{include file='pagehistory.tpl' cant=0}
		</div>
		{if $diff_summaries}
			<div class="wikitext" id="diff_versions">
				<ul>
					{foreach item=diff from=$diff_summaries}
						<li>
							{tr}Version:{/tr} {$diff.version|escape} - {$diff.comment|escape|default:"<em>{tr}No comment{/tr}</em>"}
							{if count($diff_summaries) gt 1}
								{icon _id="arrow_right"  onclick="\$('input[name=oldver]').val(`$diff.version`);\$('#editpageform').submit();return false;"  _text="{tr}View{/tr}" style="cursor: pointer"}
							{/if}
						</li>
					{/foreach}
					{button  _onclick="\$('input[name=oldver]').val(1);\$('#editpageform').submit();return false;"  _text="{tr}All Versions{/tr}" _ajax="n"}
				</ul>
			</div>
		{/if}
	</div>
{/if}

{if $prompt_for_edit_or_translate == 'y'}
	{include file='tiki-edit-page-include-prompt_for_edit_or_translate.tpl'}
{/if}

<form  enctype="multipart/form-data" method="post" action="tiki-editpage.php?page={$page|escape:'url'}" id='editpageform' name='editpageform'>

	<input type="hidden" name="no_bl" value="y" />
	{if !empty($smarty.request.returnto)}<input type="hidden" name="returnto" value="{$smarty.request.returnto}" />{/if}
	{if $diff_style}
		<select name="diff_style" class="wikiaction"title="{tr}Edit wiki page{/tr}|{tr}Select the style used to display differences to be translated.{/tr}">
			<option value="htmldiff"{if $diff_style eq "htmldiff"} selected="selected"{/if}>{tr}html{/tr}</option>
			<option value="inlinediff"{if $diff_style eq "inlinediff"} selected="selected"{/if} >{tr}text{/tr}</option>			  
			<option value="inlinediff-full"{if $diff_style eq "inlinediff-full"} selected="selected"{/if} >{tr}text full{/tr}</option>			  
		</select>
		<input type="submit" class="wikiaction tips" title="{tr}Edit wiki page{/tr}|{tr}Change the style used to display differences to be translated.{/tr}" name="preview" value="{tr}Change diff styles{/tr}" onclick="needToConfirm=false;" />
	{/if}
	
	{if $page_ref_id}<input type="hidden" name="page_ref_id" value="{$page_ref_id}" />{/if}
	{if isset($hdr)}<input type="hidden" name="hdr" value="{$hdr}" />{/if}
	{if isset($cell)}<input type="hidden" name="cell" value="{$cell}" />{/if}
	{if isset($pos)}<input type="hidden" name="pos" value="{$pos}" />{/if}
	{if $current_page_id}<input type="hidden" name="current_page_id" value="{$current_page_id}" />{/if}
	{if $add_child}<input type="hidden" name="add_child" value="true" />{/if}
	
	{if ( $preview && $staging_preview neq 'y' ) or $prefs.wiki_actions_bar eq 'top' or $prefs.wiki_actions_bar eq 'both'}
		<div class='top_actions'>
			{include file='wiki_edit_actions.tpl'}
		</div>
	{/if}

	<table class="formcolor" width="100%">
		<tr>
			<td colspan="2">
				{if isset($page_badchars_display)}
					{if $prefs.wiki_badchar_prevent eq 'y'}
						{remarksbox type=errors title="{tr}Invalid page name{/tr}"}
							{tr 0=$page_badchars_display|escape}The page name specified contains unallowed characters. It will not be possible to save the page until those are removed: <strong>%0</strong>{/tr}
						{/remarksbox}
					{else}
						{remarksbox type=tip title="{tr}Tip{/tr}"}
							{tr 0=$page_badchars_display|escape}The page name specified contains characters that may render the page hard to access. You may want to consider removing those: <strong>%0</strong>{/tr}
						{/remarksbox}
					{/if}
					<p>{tr}Page name:{/tr} <input type="text" name="page" value="{$page|escape}" /></p>
				{else}
					<input type="hidden" name="page" value="{$page|escape}" /> 
					{* the above hidden field is needed for auto-save to work *}
				{/if}
				{tabset name='tabs_editpage'}
					{tab name="{tr}Edit page{/tr}"}
						{if $translation_mode == 'y'}
							<div class="translation_message">
								<h2>{tr}Translate to:{/tr} {$target_page|escape}</h2>
								<p>{tr}Reproduce the changes highlighted on the left using the editor below{/tr}.</p>
							</div>
						{/if}
						{textarea}{$pagedata}{/textarea}
						{if $page|lower neq 'sandbox'}
							<fieldset>
								<label for="comment">{tr}Describe the change you made:{/tr} {help url='Editing+Wiki+Pages' desc="{tr}Edit comment: Enter some text to describe the changes you are currently making{/tr}"}</label>
								<input style="width:98%;" class="wikiedit" type="text" id="comment" name="comment" value="{$commentdata|escape}" />
								{if $show_watch eq 'y'}
									<label for="watch">{tr}Monitor this page:{/tr}</label>
									<input type="checkbox" id="watch" name="watch" value="1"{if $watch_checked eq 'y'} checked="checked"{/if} />
								{/if}
							</fieldset>
							{if $prefs.feature_contribution eq 'y'}
								<fieldset>
									<legend>{tr}Contributions{/tr}</legend>
									<table>
										{include file='contribution.tpl'}
									</table>
								</fieldset>
							{/if}
							{if $wysiwyg neq 'y' and $prefs.feature_wiki_pictures eq 'y' and $tiki_p_upload_picture eq 'y' and $prefs.feature_filegals_manager neq 'y'}
								<fieldset>
									<legend>{tr}Upload picture:{/tr}</legend>
									<input type="hidden" name="MAX_FILE_SIZE" value="1000000000" />
									<input type="hidden" name="hasAlreadyInserted" value="" />
									<input type="hidden" name="prefix" value="/img/wiki_up/{if $tikidomain}{$tikidomain}/{/if}" />
									<input name="picfile1" type="file" onchange="javascript:insertImgFile('editwiki','picfile1','hasAlreadyInserted','img')"/>
									<div id="new_img_form"></div>
									<a href="javascript:addImgForm()" onclick="needToConfirm = false;">{tr}Add another image{/tr}</a>
								</fieldset>
							{/if}
				
						{/if}
					{/tab}
					{if $prefs.feature_categories eq 'y' and $tiki_p_modify_object_categories eq 'y' and count($categories) gt 0}
						{tab name="{tr}Categories{/tr}"}
							{if $categIds}
								{remarksbox type="note" title="{tr}Note:{/tr}"}
								<strong>{tr}Categorization has been preset for this edit{/tr}</strong>
								{/remarksbox}
								{section name=o loop=$categIds}
									<input type="hidden" name="cat_categories[]" value="{$categIds[o]}" />
								{/section}
								<input type="hidden" name="categId" value="{$categIdstr}" />
								<input type="hidden" name="cat_categorize" value="on" />
								
								{if $prefs.feature_wiki_categorize_structure eq 'y'}
									{tr}Categories will be inherited from the structure top page{/tr}
								{/if}
							{else}
								{if $page|lower ne 'sandbox'}
									{include file='categorize.tpl' notable='y'}
								{/if}{* sandbox *}
							{/if}
						{/tab}
					{/if}
					{if !empty($showPropertiesTab)}
						{tab name="{tr}Properties{/tr}"}
							{if $prefs.feature_wiki_templates eq 'y' and $tiki_p_use_content_templates eq 'y'}
								<fieldset>
									<legend>{tr}Apply template:{/tr}</legend>
									<select id="templateId" name="templateId" onchange="javascript:document.getElementById('editpageform').submit();" onclick="needToConfirm = false;">
										<option value="0">{tr}none{/tr}</option>
										{section name=ix loop=$templates}
										<option value="{$templates[ix].templateId|escape}" {if $templateId eq $templates[ix].templateId}selected="selected"{/if}>{tr}{$templates[ix].name|escape}{/tr}</option>
										{/section}
									</select>
									{if $tiki_p_edit_content_templates eq 'y'}
										<a style="align=right;" href="tiki-admin_content_templates.php" class="link" onclick="needToConfirm = true;">{tr}Admin Content Templates{/tr}</a>
									{/if}
								</fieldset>
							{/if}
							{if $prefs.feature_wiki_usrlock eq 'y' && ($tiki_p_lock eq 'y' || $tiki_p_admin_wiki eq 'y')}
								<fieldset>
									<legend>{tr}Lock this page{/tr}</legend>
									<input type="checkbox" id="lock_it" name="lock_it" {if $lock_it eq 'y'}checked="checked"{/if}/>
								</fieldset>
							{/if}
							{if $prefs.wiki_comments_allow_per_page neq 'n'}
								<fieldset>
									<legend>{tr}Allow comments on this page{/tr}</legend>
									<input type="checkbox" id="comments_enabled" name="comments_enabled" {if $comments_enabled eq 'y'}checked="checked"{/if}/>
								</fieldset>
							{/if}
				
							{if $prefs.feature_wiki_allowhtml eq 'y' and $tiki_p_use_HTML eq 'y' and $wysiwyg neq 'y'}
								<fieldset>
									<legend>{tr}Allow HTML:{/tr}</legend>
									<input type="checkbox" id="allowhtml" name="allowhtml" {if $allowhtml eq 'y'}checked="checked"{/if}/>
								</fieldset>
								{if $prefs.ajax_autosave eq "y"}{jq}
$("#allowhtml").change(function() {
	auto_save( "editwiki", autoSaveId );
});
								{/jq}{/if}
							{/if}
							{if $prefs.feature_wiki_import_html eq 'y'}
								<fieldset>
									<legend>{tr}Import HTML:{/tr}</legend>
									<input class="wikiedit" type="text" id="suck_url" name="suck_url" value="{$suck_url|escape}" />&nbsp;
									<input type="submit" class="wikiaction" name="do_suck" value="{tr}Import{/tr}" onclick="needToConfirm=false;" />&nbsp;
									<label><input type="checkbox" name="parsehtml" {if $parsehtml eq 'y'}checked="checked"{/if}/>&nbsp;
									{tr}Try to convert HTML to wiki{/tr}. </label>
								</fieldset>
							{/if}
							
							{if $tiki_p_export_wiki eq 'y' && $prefs.feature_wiki_import_page eq 'y'}
								<fieldset>
									<legend>{tr}Import page:{/tr}</legend>
									<input type="hidden" name="MAX_FILE_SIZE" value="1000000000" />
									<input id="userfile1" name="userfile1" type="file" />
									{if $prefs.feature_wiki_export eq 'y' and $tiki_p_export_wiki eq 'y'}
										<a href="tiki-export_wiki_pages.php?page={$page|escape:"url"}&amp;all=1" class="link">{tr}export all versions{/tr}</a>
									{/if}
								</fieldset>
							{/if}
							
							{if $wysiwyg neq 'y'}
								{if $prefs.feature_wiki_attachments == 'y' and ($tiki_p_wiki_attach_files eq 'y' or $tiki_p_wiki_admin_attachments eq 'y')}
									<fieldset>
										<legend>{tr}Upload file:{/tr}</legend>
										<input type="hidden" name="MAX_FILE_SIZE" value="1000000000" />
										<input type="hidden" name="hasAlreadyInserted2" value="" />
										<input type="hidden" id="page2" name="page2" value="{$page}" />
										<input name="userfile2" type="file" id="attach-upload" />
										 <label>{tr}Comment:{/tr}<input type="text" name="attach_comment" maxlength="250" id="attach-comment" /></label>
										<input type="submit" class="wikiaction" name="attach" value="{tr}Attach{/tr}" onclick="javascript:needToConfirm=false;insertImgFile('editwiki','userfile2','hasAlreadyInserted2','file', 'page2', 'attach_comment'); return true;" />
									</fieldset>
								{/if}
	
							{/if}
							{* merged tool and property tabs for tiki 6 *}
							{if $page|lower neq 'sandbox'}
								{if $prefs.wiki_feature_copyrights  eq 'y'}
									<fieldset>
										<legend>{tr}Copyright:{/tr}</legend>
										<table class="formcolor" border="0">
											<tr>
												<td><label for="copyrightTitle">{tr}Title:{/tr}</label></td>
												<td><input size="40" class="wikiedit" type="text" id="copyrightTitle" name="copyrightTitle" value="{$copyrightTitle|escape}" /></td>
												{if !empty($copyrights)}
													<td rowspan="3"><a href="copyrights.php?page={$page|escape}">{tr}To edit the copyright notices{/tr}</a></td>
												{/if}
											</tr>
											<tr>
												<td><label for="copyrightYear">{tr}Year:{/tr}</label></td>
												<td><input size="4" class="wikiedit" type="text" id="copyrightYear" name="copyrightYear" value="{$copyrightYear|escape}" /></td>
											</tr>
											<tr>
												<td><label for="copyrightAuthors">{tr}Authors:{/tr}</label></td>
												<td><input size="40" class="wikiedit" id="copyrightAuthors" name="copyrightAuthors" type="text" value="{$copyrightAuthors|escape}" /></td>
											</tr>
										</table>
									</fieldset>
								{/if}
								{if $prefs.feature_freetags eq 'y' and $tiki_p_freetags_tag eq 'y'}
									<fieldset>
										<legend>{tr}Freetags{/tr}</legend>
										<table>
											{include file='freetag.tpl'}
										</table>
									</fieldset>
								{/if}
								{if $prefs.feature_wiki_icache eq 'y'}
									<fieldset>
										<legend>{tr}Cache{/tr}</legend>
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
									</fieldset>
								{/if}
								{if $prefs.feature_wiki_structure eq 'y'}
									<fieldset>
										<legend>{tr}Structures{/tr}</legend>
											<div id="showstructs">
												{if $showstructs|@count gt 0}
													<ul>
														{foreach from=$showstructs item=page_info}
															<li>{$page_info.pageName}{if !empty($page_info.page_alias)}({$page_info.page_alias}){/if}</li>
														{/foreach}  
													</ul>
												{/if}
											  
												{if $tiki_p_edit_structures eq 'y'}
													<a href="tiki-admin_structures.php">{tr}Manage structures{/tr} {icon _id='wrench'}</a>
												{/if}
											</div>
									</fieldset>	
								{/if}
								{if $prefs.wiki_feature_copyrights  eq 'y'}
									<fieldset>
										<legend>{tr}License:{/tr}</legend>
										<a href="{$prefs.wikiLicensePage|sefurl}">{tr}{$prefs.wikiLicensePage}{/tr}</a>
										{if $prefs.wikiSubmitNotice neq ""}
											{remarksbox type="note" title="{tr}Important:{/tr}"}
												<strong>{tr}{$prefs.wikiSubmitNotice}{/tr}</strong>
											{/remarksbox}
										{/if}
									</fieldset>
								{/if}
								{if $tiki_p_admin_wiki eq 'y' && $prefs.wiki_authors_style_by_page eq 'y'}
									<fieldset>
										<legend>{tr}Authors' style{/tr}</legend>
										{include file='wiki_authors_style.tpl' tr_class='formcolor' wiki_authors_style_site='y' style=''}
									</fieldset>
								{/if}
							{/if}{*end if sandbox *}
							{if $prefs.feature_wiki_description eq 'y' or $prefs.metatag_pagedesc eq 'y'}
								<fieldset>
									{if $prefs.metatag_pagedesc eq 'y'}
										<legend>{tr}Description (used for metatags):{/tr}</legend>
									{else}
										<legend>{tr}Description:{/tr}</legend>
									{/if}
									<input style="width:98%;" type="text" id="description" name="description" value="{$description|escape}" />
								</fieldset>
							{/if}
							{if $prefs.feature_wiki_footnotes eq 'y'}
								{if $user}
									<fieldset>
										<legend>{tr}My Footnotes:{/tr}</legend>
										<textarea id="footnote" name="footnote" rows="8" cols="42" style="width:98%;" >{$footnote|escape}</textarea>
									</fieldset>
								{/if}
							{/if}
							{if $prefs.feature_wiki_ratings eq 'y' and $tiki_p_wiki_admin_ratings eq 'y'}
								<fieldset>
									<legend>{tr}Use rating:{/tr}</legend>

									{foreach from=$poll_rated item=rating}
										<div>
											<a href="tiki-admin_poll_options.php?pollId={$rating.info.pollId}">{$rating.info.title}</a>
											{assign var=thispage value=$page|escape:"url"}
											{assign var=thispoll_rated value=$rating.info.pollId}
											{button href="?page=$thispage&amp;removepoll=$thispoll_rated" _text="{tr}Disable{/tr}"}
										</div>
									{/foreach}

									{if $tiki_p_admin_poll eq 'y'}
										{button href="tiki-admin_polls.php" _text="{tr}Admin Polls{/tr}"}
									{/if}

									{if $poll_rated|@count <= 1 or $prefs.poll_multiple_per_object eq 'y'}
										<div>
											{if count($polls_templates)}
												{tr}Type{/tr}
												<select name="poll_template">
													<option value="0">{tr}none{/tr}</option>
													{foreach item=template from=$polls_templates}
														<option value="{$template.pollId|escape}"{if $template.pollId eq $poll_template} selected="selected"{/if}>{tr}{$template.title|escape}{/tr}</option>
													{/foreach}
												</select>
												{tr}Title{/tr}
												<input type="text" name="poll_title" size="22" />
											{else}
												{tr}There is no available poll template.{/tr}
												{if $tiki_p_admin_polls ne 'y'}
													{tr}You should ask an admin to create them.{/tr}
												{/if}
											{/if}
										</div>
									{/if}
								</fieldset>
							{/if}
							{if $prefs.feature_multilingual eq 'y'}
								<fieldset>
									<legend>{tr}Language:{/tr}</legend>
									<select name="lang" id="lang">
										<option value="">{tr}Unknown{/tr}</option>
										{section name=ix loop=$languages}
											<option value="{$languages[ix].value|escape}"{if $lang eq $languages[ix].value or (!($data.page_id) and $lang eq '' and $languages[ix].value eq $prefs.language)} selected="selected"{/if}>{$languages[ix].name}</option>
										{/section}
									</select>
									{if $translationOf}
										<input type="hidden" name="translationOf" value="{$translationOf|escape}"/>
									{/if}
								</fieldset>
								{if $trads|@count > 1 and $urgent_allowed}
									<fieldset {if $prefs.feature_urgent_translation neq 'y' or $diff_style} style="display:none;"{/if}>
										<legend>{tr}Translation request:{/tr}</legend>
										<input type="hidden" name="lang" value="{$lang|escape}"/>
										<input type="checkbox" id="translation_critical" name="translation_critical" id="translation_critical"{if $translation_critical} checked="checked"{/if}/>
										<label for="translation_critical">{tr}Send urgent translation request.{/tr}</label>
										{if $diff_style}
											<input type="hidden" name="oldver" value="{$diff_oldver|escape}"/>
											<input type="hidden" name="newver" value="{$diff_newver|escape}"/>
										{/if}
									</fieldset>
								{/if}
							{/if}
							{if $prefs.geo_locate_wiki eq 'y'}
								{$headerlib->add_map()}
								<div class="map-container" data-target-field="geolocation" style="height: 250px; width: 250px;"></div>
								<input type="hidden" name="geolocation" value="{$geolocation_string}" />
							{/if}
							{if $tiki_p_admin_wiki eq "y"}
								<a href="tiki-admin.php?page=wiki">{tr}Admin wiki preferences{/tr} {icon _id='wrench'}</a>
							{/if}
						{/tab}
					{/if}
				{/tabset}
			</td>
		</tr>
		
		
		{if $page|lower ne 'sandbox'}
			{if $prefs.feature_antibot eq 'y' && $anon_user eq 'y'}
				{include file='antibot.tpl' tr_style="formcolor"}
			{/if}
		{/if}{* sandbox *}
		
		{if $prefs.wiki_actions_bar neq 'top'}
			<tr>
				<td colspan="2" style="text-align:center;">
					{include file='wiki_edit_actions.tpl'}
				</td>
			</tr>
		{/if}
	</table>
</form>
{include file='tiki-page_bar.tpl'}
{if $prefs.javascript_enabled eq "n"}{include file='tiki-edit_help.tpl'}{/if}
{include file='tiki-edit_help_plugins.tpl'}
