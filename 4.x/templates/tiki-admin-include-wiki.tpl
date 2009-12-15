{* $Id$ *}

{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}Use the 'Quick Edit' module to easily create or edit wiki pages.{/tr} {tr}Select <a class="rbox-link" href="tiki-admin_modules.php">Admin &gt; Modules</a> to add this (or other) modules.{/tr}{/remarksbox}

<form action="tiki-admin.php?page=wiki" method="post">
<div class="heading input_submit_container" style="text-align: right">
	<input type="submit" name="wikisetprefs" value="{tr}Change preferences{/tr}" />
</div>

	{tabset name="admin_wiki"}
		{tab name="{tr}General Preferences{/tr}"}
			{preference name=wikiHomePage}
			{preference name=wiki_page_regex}

<fieldset><legend>{tr}Page display{/tr}</legend>

	{preference name=feature_wiki_description label="{tr}Description{/tr}"}
	{preference name=feature_page_title label="{tr}Title{/tr}"}
	{preference name=feature_wiki_pageid label="{tr}Page ID{/tr}"}
	{preference name=wiki_show_version label="{tr}Page version{/tr}"}

	{preference name=wiki_pagename_strip}

	{preference name=wiki_authors_style label="{tr}List authors{/tr}"}

	<div class="adminoptionboxchild">
		{preference name=wiki_authors_style_by_page label="{tr}Allow override per page{/tr}"}
	</div>

	{preference name=wiki_actions_bar}
	{preference name=wiki_page_navigation_bar}
 	{preference name=wiki_topline_position}
 	{preference name=page_bar_position}
 
</fieldset>

<fieldset><legend>{tr}Edit{/tr}</legend>

<div class="adminoptionbox">
	<div class="adminoption">{if $prefs.lib_spellcheck eq 'y'}<input type="checkbox" name="wiki_spellcheck" id='wiki_spellcheck' {if $prefs.wiki_spellcheck eq 'y'}checked="checked"{/if}/>{else}{tr}Not Installed{/tr}{/if}</div>
	<div class="adminoptionlabel"><label for="wiki_spellcheck">{tr}Spell checking{/tr}</label> {if $prefs.feature_help eq 'y'} {help url="Spellcheck"}{/if}
	<br /><em>{tr}Requires a separate download{/tr}.</em>
	</div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_wiki_templates" name="feature_wiki_templates" {if $prefs.feature_wiki_templates eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="feature_wiki_templates">{tr}Content templates{/tr}</label>{if $prefs.feature_help eq 'y'} {help url="Content+Template"}{/if}</div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id='feature_warn_on_edit' name="feature_warn_on_edit" {if $prefs.feature_warn_on_edit eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="feature_warn_on_edit">{tr}Warn on edit conflict{/tr}.</label></div>
</div>

<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="warn_on_edit_time">{tr}Edit idle timeout:{/tr}</label>
	<select name="warn_on_edit_time" id="warn_on_edit_time">
    <option value="1" {if $prefs.warn_on_edit_time eq 1}selected="selected"{/if}>1</option>
    <option value="2" {if $prefs.warn_on_edit_time eq 2}selected="selected"{/if}>2</option>
    <option value="5" {if $prefs.warn_on_edit_time eq 5}selected="selected"{/if}>5</option>
    <option value="10" {if $prefs.warn_on_edit_time eq 10}selected="selected"{/if}>10</option>
    <option value="15" {if $prefs.warn_on_edit_time eq 15}selected="selected"{/if}>15</option>
    <option value="30" {if $prefs.warn_on_edit_time eq 30}selected="selected"{/if}>30</option>
    </select> {tr}minutes{/tr}
	</div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_wiki_undo" name="feature_wiki_undo" {if $prefs.feature_wiki_undo eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="feature_wiki_undo">{tr}Undo{/tr}</label></div>
</div>

{preference name=feature_wiki_save_draft}

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_wiki_footnotes" name="feature_wiki_footnotes" {if $prefs.feature_wiki_footnotes eq 'y'}checked="checked" {/if}/></div>
	<div class="adminoptionlabel"><label for="feature_wiki_footnotes">{tr}Footnotes{/tr}</label> <a class="link" href="tiki-assignpermission.php?type=wiki&amp;group=Anonymous" title="{tr}Permission{/tr}">{icon _id="key" alt="{tr}Permission{/tr}"}</a></div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_wiki_allowhtml" name="feature_wiki_allowhtml" {if $prefs.feature_wiki_allowhtml eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="feature_wiki_allowhtml">{tr}Allow HTML{/tr}</label></div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="wiki_edit_section" onclick="flip('useeditsection');" name="wiki_edit_section" {if $prefs.wiki_edit_section eq 'y'}checked="checked" {/if}/></div>
	<div class="adminoptionlabel"><label for="wiki_edit_section">{tr}Edit section{/tr}</label>
<div class="adminoptionboxchild" id="useeditsection" style="display:{if $prefs.wiki_edit_section eq 'y'}block{else}none{/if};">
	<div class="adminoptionlabel"><label for="wiki_edit_section_level">{tr}Edit section level:{/tr}</label>
	<select name="wiki_edit_section_level" id="wiki_edit_section_level">
		{section name=level start=0 loop=7 step=1}
		<option value="{$smarty.section.level.index}" {if $smarty.section.level.index eq $prefs.wiki_edit_section_level}selected="selected"{/if}>{if $smarty.section.level.index eq 0}{tr}All{/tr}{else}{$smarty.section.level.index}{/if}</option>
		{/section}
		</select>
	</div>
</div>
	</div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="wiki_edit_icons_toggle" name="wiki_edit_icons_toggle" {if $prefs.wiki_edit_icons_toggle eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="wiki_edit_icons_toggle">{tr}Toggle display of section and plugin edit icons{/tr}</label></div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="wiki_edit_minor" name="wiki_edit_minor" {if $prefs.wiki_edit_minor eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="wiki_edit_minor">{tr}Allow minor edits{/tr}.</label>
	{remarksbox type=note title=Note}{tr}Minor edits do not flag new content for translation and do not send watch notifications.{/tr}.<br />
	{tr}Only user groups granted the tiki_p_minor permission (and admins) will be able to save minor edits when this is enabled.{/tr}
	<a class="link" href="tiki-assignpermission.php?type=wiki&amp;group=Registered" title="{tr}Permission{/tr}">{icon _id="key" alt="{tr}Permission{/tr}"}</a>{/remarksbox}
	</div>
</div>

<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="feature_wiki_mandatory_category">{tr}Force and limit categorization to within subtree of:{/tr}</label>
	<select name="feature_wiki_mandatory_category" id="feature_wiki_mandatory_category">
	<option value="-1" {if $prefs.feature_wiki_mandatory_category eq -1 or $prefs.feature_wiki_mandatory_category eq ''}selected="selected"{/if}>{tr}None{/tr}</option>
	<option value="0" {if $prefs.feature_wiki_mandatory_category eq 0}selected="selected"{/if}>{tr}All{/tr}</option>
	{section name=ix loop=$catree}
	<option value="{$catree[ix].categId|escape}" {if $catree[ix].categId eq $prefs.feature_wiki_mandatory_category}selected="selected"{/if}>{if $catree[ix].categpath}{$catree[ix].categpath|escape}{else}{$catree[ix].name|escape}{/if}</option>
	{/section}
	</select>
	{if $prefs.feature_categories ne 'y'}<br />{icon _id=information}{tr}Categories are disabled.{/tr} <a href="tiki-admin.php?page=features" title="{tr}Features{/tr}">{tr}Enable now{/tr}</a>.{/if}
	
	</div>
</div>


<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_actionlog_bytes" name="feature_actionlog_bytes" {if $prefs.feature_actionlog_bytes eq 'y'}checked="checked" {/if}/></div>
	<div class="adminoptionlabel"><label for="feature_actionlog_bytes">{tr}Log bytes changes (+/-) in action logs{/tr}.</label>
	<br /><em>{icon _id=information} {tr}May impact performance{/tr}.</em>
	</div>
</div>
</fieldset>
		{/tab}

		{tab name="{tr}Features{/tr}"}
<input type="hidden" name="wikifeatures" />    	
	<div class="adminoptionbox">
		<div class="adminoption"><input type="checkbox" id="feature_sandbox" name="feature_sandbox" {if $prefs.feature_sandbox eq 'y'}checked="checked" {/if}/></div>
		<div class="adminoptionlabel"><label for="feature_sandbox">{tr}Sandbox{/tr}</label></div>
	</div>

	<div class="adminoptionbox">
		<div class="adminoption"><input type="checkbox" id="feature_wiki_comments" name="feature_wiki_comments" {if $prefs.feature_wiki_comments eq 'y'}checked="checked" {/if}onclick="flip('usecomments');" /></div>
		<div class="adminoptionlabel"><label for="feature_wiki_comments">{tr}Comments{/tr}</label>{if $prefs.feature_help eq 'y'} {help url="Comments"}{/if} <a class="link" href="tiki-assignpermission.php?type=wiki&amp;group=Anonymous" title="{tr}Permission{/tr}">{icon _id="key" alt="{tr}Permission{/tr}"}</a></div>
		<input type="hidden" name="wikiprefs" />
	</div>
	<div class="adminoptionboxchild" id="usecomments" style="display:{if $prefs.feature_wiki_comments eq 'y'}block{else}none{/if};">
	<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="wiki_comments_displayed_default" 
name="wiki_comments_displayed_default" {if $prefs.wiki_comments_displayed_default eq 'y'}checked="checked" {/if} /></div>
	<div class="adminoptionlabel"><label for="wiki_comments_displayed_default">{tr}Display by default{/tr}.</label></div>
	</div>
	
	<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="wiki_comments_per_page">{tr}Default number per page{/tr}:</label> <input size="5" type="text" id="wiki_comments_per_page" name="wiki_comments_per_page" value="{$prefs.wiki_comments_per_page|escape}" /></div>
	</div>
	
	<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="wiki_comments_default_ordering">{tr}Default ordering{/tr}:</label>
    <select name="wiki_comments_default_ordering" id="wiki_comments_default_ordering">
    <option value="commentDate_desc" {if $prefs.wiki_comments_default_ordering eq 'commentDate_desc'}selected="selected"{/if}>{tr}Newest first{/tr}</option>
		<option value="commentDate_asc" {if $prefs.wiki_comments_default_ordering eq 'commentDate_asc'}selected="selected"{/if}>{tr}Oldest first{/tr}</option>
    <option value="points_desc" {if $prefs.wiki_comments_default_ordering eq 'points_desc'}selected="selected"{/if}>{tr}Points{/tr}</option>
    </select>
    </div>
    {preference name=wiki_comments_allow_per_page}
	</div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_wiki_attachments" name="feature_wiki_attachments" {if $prefs.feature_wiki_attachments eq 'y'}checked="checked" {/if}onclick="flip('useattachments');" /></div>
	<div class="adminoptionlabel"><label for="feature_wiki_attachments">{tr}Attachments{/tr}</label>{if $prefs.feature_help eq 'y'} {help url="Attachments"}{/if} <a class="link" href="tiki-assignpermission.php?type=wiki&amp;group=Anonymous" title="{tr}Permission{/tr}">{icon _id="key" alt="{tr}Permission{/tr}"}</a></div>
	<input type="hidden" name="wikiattprefs" />
		<div class="adminoptionboxchild" id="useattachments" style="display:{if $prefs.feature_wiki_attachments eq 'y'}block{else}none{/if};">

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="w_displayed_default"  
name="w_displayed_default" {if $prefs.w_displayed_default eq 'y'} checked="checked"{/if} /> </div>
	<div class="adminoptionlabel"><label for='w_displayed_default'>{tr}Display by default{/tr}.</label> </div>
</div>
		
<div class="adminoptionbox">
	<div class="adminoptionlabel"><input type="radio" id="w_use_db_1" name="w_use_db" value="y" {if $prefs.w_use_db eq 'y'}checked="checked"{/if} onclick="flip('directorypath');" /><label for="w_use_db_1">{tr}Store in database{/tr}.</label></div>
	<div class="adminoptionlabel"><input type="radio" id="w_use_db_2" name="w_use_db" value="n" {if $prefs.w_use_db eq 'n'}checked="checked"{/if} onclick="flip('directorypath');" /><label for="w_use_db_2">{tr}Store in directory{/tr}.</label></div>
	<div class="adminoptionboxchild" id="directorypath" style="display:{if $prefs.w_use_db eq 'n'}block{else}none{/if};">
		<div class="adminoptionlabel"><label for="w_use_dir">{tr}Path:{/tr}</label> <input type="text" name="w_use_dir" value="{$prefs.w_use_dir}" id="w_use_dir" /></div>
	</div>
</div>

<div class="adminoptionbox">
	<div class="adminoptionlabel"><a class="button" href="tiki-admin.php?page=wikiatt">{tr}Manage attachments{/tr}</a></div>
</div>
	</div>		
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_dump" name="feature_dump" {if $prefs.feature_dump eq 'y'}checked="checked"{/if} onclick="flip('usedumps');" /></div>
	<div class="adminoptionlabel"><label for="feature_dump">{tr}Dumps{/tr}</label></div>
<div class="adminoptionboxchild" id="usedumps" style="display:{if $prefs.feature_dump eq 'y'}block{else}none{/if};">
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="tagname">{tr}Tag for current wiki{/tr}:</label> <input  maxlength="20" size="20" type="text" name="tagname" id="tagname" /><input type="submit" name="createtag" value="{tr}Create{/tr}" /></div>
</div>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="restoretag">{tr}Restore wiki to tag{/tr}:</label> 
			<select name="tagname" id="restoretag"{if $tags|@count eq '0'} disabled="disabled"{/if}>
          {section name=sel loop=$tags}
          <option value="{$tags[sel]|escape}">{$tags[sel]}</option>
          {sectionelse}
          <option value=''>{tr}None{/tr}</option>
          {/section}
          </select><input type="submit" name="restoretag" value="{tr}Restore{/tr}"{if $tags|@count eq '0'} disabled="disabled"{/if} />
	</div>
</div>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="removetag">{tr}Remove a tag{/tr}:</label> 
		<select name="tagname" id="removetag"{if $tags|@count eq '0'} disabled="disabled"{/if}>
          {section name=sel loop=$tags}
          <option value="{$tags[sel]|escape}">{$tags[sel]}</option>
          {sectionelse}
          <option value=''>{tr}None{/tr}</option>
          {/section}
          </select><input type="submit" name="removetag" value="{tr}Remove{/tr}"{if $tags|@count eq '0'} disabled="disabled"{/if} />
	</div>
</div>

<div class="adminoptionbox">
	<div class="adminoptionlabel"><a class="button" href="tiki-admin.php?page=wiki&amp;dump=1">{tr}Generate dump{/tr}</a></div>
</div>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><a class="button" href="dump/{if $tikidomain}{$tikidomain}/{/if}new.tar">{tr}Download last dump{/tr}</a></div>
</div>
</div>	
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_wiki_pictures" name="feature_wiki_pictures" {if $prefs.feature_wiki_pictures eq 'y'}checked="checked"{/if} onclick="flip('usepictures');" /></div>
	<div class="adminoptionlabel"><label for="feature_wiki_pictures">{tr}Pictures{/tr}</label>{if $prefs.feature_help eq 'y'} {help url="Wiki-Syntax Images"}{/if} <a class="link" href="tiki-assignpermission.php?type=wiki&amp;group=Anonymous" title="{tr}Permission{/tr}">{icon _id="key" alt="{tr}Permission{/tr}"}</a></div>

<div class="adminoptionboxchild" id="usepictures" style="display:{if $prefs.feature_wiki_pictures eq 'y'}block{else}none{/if};">

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="feature_filegals_manager" id="feature_filegals_manager" {if $prefs.feature_filegals_manager eq 'y'}checked="checked" {/if}/> </div>
	<div class="adminoptionlabel"><label for="feature_filegals_manager">{tr}Use File Galleries to store pictures {/tr}.</label></div>
</div>


<div class="adminoptionbox">
	<div class="adminoptionlabel"><a class="button" href="tiki-admin.php?page=wiki&amp;rmvunusedpic=1">{tr}Remove unused pictures{/tr}</a></div>
</div>
</div>
</div>
    
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_wiki_export" name="feature_wiki_export" {if $prefs.feature_wiki_export eq 'y'}checked="checked"{/if} onclick="flip('useexport');" /></div>
	<div class="adminoptionlabel"><label for="feature_wiki_export">{tr}Export{/tr}</label> <a class="link" href="tiki-assignpermission.php?type=wiki&amp;group=Anonymous" title="{tr}Permission{/tr}">{icon _id="key" alt="{tr}Permission{/tr}"}</a></div>

<div class="adminoptionboxchild" id="useexport" style="display:{if $prefs.feature_wiki_export eq 'y'}block{else}none{/if};">
<div class="adminoptionbox">
	<div class="adminoptionlabel"><a class="button" href="tiki-export_wiki_pages.php">{tr}Export Wiki Pages{/tr}</a></div>
</div>
</div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_wikiwords" name="feature_wikiwords" {if $prefs.feature_wikiwords eq 'y'}checked="checked"{/if} onclick="flip('usewikiwords');" /></div>
	<div class="adminoptionlabel"><label for="feature_wikiwords">{tr}WikiWords{/tr}</label></div>
<div class="adminoptionboxchild" id="usewikiwords" style="display:{if $prefs.feature_wikiwords eq 'y'}block{else}none{/if};">
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_wikiwords_usedash" name="feature_wikiwords_usedash" {if $prefs.feature_wikiwords_usedash eq 'y'}checked="checked" {/if}/></div>
	<div class="adminoptionlabel"><label for="feature_wikiwords_usedash">{tr}Accept dashes and underscores in WikiWords{/tr}.</label></div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_wiki_plurals" name="feature_wiki_plurals" {if $prefs.feature_wiki_plurals eq 'y'}checked="checked" {/if}/></div>
	<div class="adminoptionlabel"><label for="feature_wiki_plurals">{tr}Link plural WikiWords to their singular forms{/tr}.</label></div>
</div>
</div>
</div>
	
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_history" name="feature_history" {if $prefs.feature_history eq 'y'}checked="checked" {/if}onclick="flip('usehistory');" /></div>
	<div class="adminoptionlabel"><label for="feature_history">{tr}History{/tr}</label>{if $prefs.feature_help eq 'y'} {help url="History"}{/if} <a class="link" href="tiki-assignpermission.php?type=wiki&amp;group=Anonymous" title="{tr}Permission{/tr}">{icon _id="key" alt="{tr}Permission{/tr}"}</a></div>
<div class="adminoptionboxchild" id="usehistory" style="display:{if $prefs.feature_history eq 'y'}block{else}none{/if};">
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="maxVersions">{tr}Maximum number of versions:{/tr}</label> <input size="5" type="text" name="maxVersions" value="{$prefs.maxVersions|escape}" id="maxVersions" /><br /><em>Use <strong>0</strong> for unlimited versions.</em></div>
</div>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="keep_versions">{tr}Never delete versions younger than{/tr}:</label> <input size="5" type="text" name="keep_versions" id='keep_versions' value="{$prefs.keep_versions|escape}" /> {tr}days{/tr}.</div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_wiki_history_ip" name="feature_wiki_history_ip" {if $prefs.feature_wiki_history_ip eq 'y'}checked="checked" {/if}/></div>
	<div class="adminoptionlabel"><label for="feature_wiki_history_ip">{tr}Display IP address{/tr}.</label></div>
</div>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="default_wiki_diff_style">{tr}Default diff style{/tr}:</label>
	<select name="default_wiki_diff_style" id="default_wiki_diff_style">
       <option value="old" {if $prefs.default_wiki_diff_style eq 'old'}selected="selected"{/if}>{tr}Only with last version{/tr}</option>
  <option value="htmldiff" {if $prefs.default_wiki_diff_style == "htmldiff"}selected="selected"{/if}>{tr}HTML diff{/tr}</option>
  <option value="sidediff" {if $prefs.default_wiki_diff_style == "sidediff"}selected="selected"{/if}>{tr}Side-by-side diff{/tr}</option>
  <option value="sidediff-char" {if $prefs.default_wiki_diff_style == "sidediff-char"}selected="selected"{/if}>{tr}Side-by-side diff by characters{/tr}</option>
  <option value="inlinediff" {if $prefs.default_wiki_diff_style == "inlinediff"}selected="selected"{/if}>{tr}Inline diff{/tr}</option>
  <option value="inlinediff-char" {if $prefs.default_wiki_diff_style == "inlinediff-char"}selected="selected"{/if}>{tr}Inline diff by characters{/tr}</option>
  <option value="sidediff-full" {if $prefs.default_wiki_diff_style == "sidediff-full"}selected="selected"{/if}>{tr}Full side-by-side diff{/tr}</option>
  <option value="sidediff-full-char" {if $prefs.default_wiki_diff_style == "sidediff-full-char"}selected="selected"{/if}>{tr}Full side-by-side diff by characters{/tr}</option>
  <option value="inlinediff-full" {if $prefs.default_wiki_diff_style == "inlinediff-full"}selected="selected"{/if}>{tr}Full inline diff{/tr}</option>
  <option value="inlinediff-full-char" {if $prefs.default_wiki_diff_style == "inlinediff-full-char"}selected="selected"{/if}>{tr}Full inline diff by characters{/tr}</option>
  <option value="unidiff" {if $prefs.default_wiki_diff_style == "unidiff"}selected="selected"{/if}>{tr}Unified diff{/tr}</option>
  <option value="sideview" {if $prefs.default_wiki_diff_style == "sideview"}selected="selected"{/if}>{tr}Side-by-side view{/tr}</option>
    </select>
	</div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_wiki_history_full" name="feature_wiki_history_full" {if $prefs.feature_wiki_history_full eq 'y'}checked="checked" {/if}/></div>
	<div class="adminoptionlabel"><label for="feature_wiki_history_full">{tr}History all instead of only page data, description, and change comment{/tr}.</label></div>
</div>

</div>	
</div>

<div class="adminoptionbox">
	<div class="adminoption">
		<input type="hidden" name="wikidiscussprefs" />
		<input type="checkbox" onclick="flip('discussforum');" id="feature_wiki_discuss" name="feature_wiki_discuss" {if $prefs.feature_wiki_discuss eq 'y'}checked="checked"{/if} {if $prefs.feature_forums ne 'y'} disabled="disabled"{/if} />
	</div>
	<div class="adminoptionlabel">
		<label for="feature_wiki_discuss">{tr}Discuss pages on forums{/tr}.</label>
		{if $prefs.feature_forums ne 'y'}
			<br />
			{icon _id=information}{tr}Forums are disabled.{/tr} <a href="tiki-admin.php?page=features" title="{tr}Features{/tr}">{tr}Enable now{/tr}</a>.
		{/if}
	</div>
	<div class="adminoptionboxchild" id="discussforum" style="display:{if ($prefs.feature_wiki_discuss eq 'y') and ($prefs.feature_forums eq 'y')}block{else}none{/if};">
		<div class="adminoptionbox">
			<div class="adminoptionboxlabel">
				<label for="wiki_forum_id">{tr}Forum for discussion:{/tr}</label>
				{if $prefs.feature_forums eq 'y'} 
					<a class="link" href="tiki-assignpermission.php?level=forum" title="{tr}Permission{/tr}">{icon _id="key" alt="{tr}Permission{/tr}"}</a>
				{/if}
				<select id="wiki_forum_id" name="wiki_forum_id"{if $prefs.feature_forums ne 'y' or !$all_forums} disabled="disabled"{/if}>
					{if $all_forums}
						{section name=ix loop=$all_forums}
							<option value="{$all_forums[ix].forumId|escape}" {if $all_forums[ix].forumId eq $prefs.wiki_forum_id}selected="selected"{/if}>{$all_forums[ix].name}</option>
						{/section}
					{else}    
						<option value="">{tr}None{/tr}</option>
					{/if}
				</select>
				{if ($prefs.feature_forums eq 'y') and !$all_forums}
					<div class="adminoptionbox">
						<a href="tiki-admin_forums.php" title="{tr}Forums{/tr}" class="button">Create a Forum</a>
					</div>
				{/if}
			</div>
		</div>
	</div>	
</div>


<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_source" name="feature_source" {if $prefs.feature_source eq 'y'}checked="checked" {/if}/></div>
	<div class="adminoptionlabel"><label for="feature_source">{tr}View source{/tr}</label> <a class="link" href="tiki-assignpermission.php?type=wiki&amp;group=Anonymous" title="{tr}Permission{/tr}">{icon _id="key" alt="{tr}Permission{/tr}"}</a></div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_wiki_ratings" name="feature_wiki_ratings" {if $prefs.feature_wiki_ratings eq 'y'}checked="checked" {/if}/></div>
	<div class="adminoptionlabel"><label for="feature_wiki_ratings">{tr}Rating{/tr}{if $prefs.feature_help eq 'y'} {help url="Rating"}{/if}</label> <a class="link" href="tiki-assignpermission.php?type=wiki&amp;group=Anonymous" title="{tr}Permission{/tr}">{icon _id="key" alt="{tr}Permission{/tr}"}</a>
	{if $prefs.feature_polls ne 'y'}<br />{icon _id=information}{tr}Polls are disabled.{/tr} <a href="tiki-admin.php?page=features" title="{tr}Features{/tr}">{tr}Enable now{/tr}</a>.{/if}
	</div>
</div>



<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_backlinks" name="feature_backlinks" {if $prefs.feature_backlinks eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="feature_backlinks">{tr}Backlinks{/tr}</label>{if $prefs.feature_help eq 'y'} {help url="Backlinks"}{/if} <a class="link" href="tiki-assignpermission.php?type=wiki&amp;group=Anonymous" title="{tr}Permission{/tr}">{icon _id="key" alt="{tr}Permission{/tr}"}</a></div>
</div>

			{preference name=feature_semantic}


<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_likePages" name="feature_likePages" {if $prefs.feature_likePages eq 'y'}checked="checked" {/if}/></div>
	<div class="adminoptionlabel"><label for="feature_likePages">{tr}Similar{/tr} ({tr}like pages{/tr})</label></div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id='feature_wiki_rankings' name="feature_wiki_rankings" {if $prefs.feature_wiki_rankings eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="feature_wiki_rankings">{tr}Rankings{/tr}</label></div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_wiki_structure" name="feature_wiki_structure" {if $prefs.feature_wiki_structure eq 'y'}checked="checked" {/if}onclick="flip('usestructures');" /></div>
	<div class="adminoptionlabel"><label for="feature_wiki_structure">{tr}Structures{/tr}</label>{if $prefs.feature_help eq 'y'} {help url="Structure"}{/if}</div>
<div id="usestructures" class="adminoptionboxchild" style="display:{if $prefs.feature_wiki_structure eq 'y'}block{else}none{/if};">

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_wiki_open_as_structure" name="feature_wiki_open_as_structure" {if $prefs.feature_wiki_open_as_structure eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="feature_wiki_open_as_structure">{tr}Open page as structure{/tr}.</label></div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_wiki_make_structure" name="feature_wiki_make_structure" {if $prefs.feature_wiki_make_structure eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="feature_wiki_make_structure">{tr}Make structure from page{/tr}.</label></div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_wiki_categorize_structure" name="feature_wiki_categorize_structure" {if $prefs.feature_wiki_categorize_structure eq 'y'}checked="checked" {/if}/></div>
	<div class="adminoptionlabel"><label for="feature_wiki_categorize_structure">{tr}Categorize structure pages together{/tr}.</label></div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_create_webhelp" name="feature_create_webhelp" {if $prefs.feature_create_webhelp eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="feature_create_webhelp">{tr}Create webhelp from structure:{/tr}</label></div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="page_n_times_in_a_structure" name="page_n_times_in_a_structure" {if $prefs.page_n_times_in_a_structure eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="page_n_times_in_a_structure">{tr}A page can occur multiple times in a structure:{/tr}</label></div>
</div>

</div>	
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_wiki_import_html" name="feature_wiki_import_html" {if $prefs.feature_wiki_import_html eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="feature_wiki_import_html">{tr}Import HTML{/tr}</label></div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id='feature_wiki_import_page' name="feature_wiki_import_page" {if $prefs.feature_wiki_import_page eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="feature_wiki_import_page">{tr}Import pages{/tr}</label></div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="wiki_uses_slides" name="wiki_uses_slides" {if $prefs.wiki_uses_slides eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="wiki_uses_slides">{tr}Slideshows{/tr}</label>{if $prefs.feature_help eq 'y'} {help url="Slideshow"}{/if}</div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_wiki_userpage" name="feature_wiki_userpage" {if $prefs.feature_wiki_userpage eq 'y'}checked="checked" {/if}onclick="flip('useuserpage');" /></div>
	<div class="adminoptionlabel"><label for="feature_wiki_userpage">{tr}User's page{/tr}</label></div>
<div class="adminoptionboxchild" id="useuserpage" style="display:{if $prefs.feature_wiki_userpage eq 'y'}block{else}none{/if};">
	<div class="adminoptionlabel"><label for="feature_wiki_userpage_prefix">{tr}UserPage prefix:{/tr}</label> <input type="text" name="feature_wiki_userpage_prefix" id="feature_wiki_userpage_prefix" value="{$prefs.feature_wiki_userpage_prefix|default:'UserPage'}" size="40" /></div>
</div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_wiki_usrlock" name="feature_wiki_usrlock" {if $prefs.feature_wiki_usrlock eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="feature_wiki_usrlock">{tr}Users can lock pages{/tr}</label> <a class="link" href="tiki-assignpermission.php?type=wiki&amp;group=Anonymous" title="{tr}Permission{/tr}">{icon _id="key" alt="{tr}Permission{/tr}"}</a></div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="wiki_creator_admin" name="wiki_creator_admin" {if $prefs.wiki_creator_admin eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="wiki_creator_admin">{tr}Page creators are admin of their pages{/tr}.</label></div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_wiki_print" onclick="flip('useprint');" name="feature_wiki_print" {if $prefs.feature_wiki_print eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="feature_wiki_print">{tr}Print{/tr}</label></div>

<div class="adminoptionboxchild" id="useprint" style="display:{if $prefs.feature_wiki_print eq 'y'}block{else}none{/if};">
	<div class="adminoption"><input type="checkbox" id="feature_wiki_multiprint" name="feature_wiki_multiprint" {if $prefs.feature_wiki_multiprint eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="feature_wiki_multiprint">{tr}MultiPrint{/tr}</label></div>
</div>
</div>

<div class="adminoptionbox">
<fieldset><legend>{tr}Wiki watch{/tr}{if $prefs.feature_help eq 'y'} {help url="Watch"}{/if}</legend>
{if $prefs.feature_user_watches ne 'y'}
<div class="adminoptionbox">{icon _id=information} {tr}Feature disabled{/tr}. <a href="tiki-admin.php?page=features" title="{tr}Features{/tr}">{tr}Enable now{/tr}</a>.</div>
{else}
<input type="hidden" name="wikisetwatch" />
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="wiki_watch_author" name="wiki_watch_author" {if $prefs.wiki_watch_author eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="wiki_watch_author">{tr}Create watch for author on page creation{/tr}.</label></div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="wiki_watch_editor" name="wiki_watch_editor" {if $prefs.wiki_watch_editor eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="wiki_watch_editor">{tr}Enable watch events when I am the editor{/tr}.</label></div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="wiki_watch_comments" name="wiki_watch_comments" {if $prefs.wiki_watch_comments eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="wiki_watch_comments">{tr}Enable watches on comments{/tr}.</label></div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="wiki_watch_minor" name="wiki_watch_minor" {if $prefs.wiki_watch_minor eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="wiki_watch_minor">{tr}Watch minor edits{/tr}.</label></div>
</div>
{/if}
</fieldset>
</div>
		{/tab}

		{tab name="{tr}Staging &amp; Approval{/tr}"}
<input type="hidden" name="wikiapprovalprefs" />    
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_wikiapproval" onclick="flip('usestaging');" name="feature_wikiapproval" {if $prefs.feature_wikiapproval eq 'y'}checked="checked" {/if}/></div>
	<div class="adminoptionlabel"><label for="feature_wikiapproval">{tr}Use wiki page staging and approval{/tr}.{if $prefs.feature_help eq 'y'} {help url="Wiki+Page+Staging+and+Approval"}{/if}</label>
<div id="usestaging" style="display:{if $prefs.feature_wikiapproval eq 'y'}block{else}none{/if};">

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="wikiapproval_block_editapproved" name="wikiapproval_block_editapproved" {if $prefs.wikiapproval_block_editapproved eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="wikiapproval_block_editapproved">{tr}Force bounce of editing of approved pages to staging{/tr}.</label></div>
</div>
 
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="wikiapproval_delete_staging"  name="wikiapproval_delete_staging" {if $prefs.wikiapproval_delete_staging eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="wikiapproval_delete_staging">{tr}Delete staging pages at approval{/tr}.</label></div>
</div>

<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="wikiapproval_master_group">{tr}If not in the group, edit is always redirected to the staging page edit:{/tr}</label>
	<select name="wikiapproval_master_group" id="wikiapproval_master_group">
	<option value=""{if $prefs.wikiapproval_master_group eq ''} selected="selected"{/if}></option>
	{foreach from=$all_groups item=g}
	<option value="{$g|escape}"{if $prefs.wikiapproval_master_group eq $g} selected="selected"{/if}>{$g|escape}</option>
	{/foreach}
	</select>
	</div>
</div>

<fieldset><legend>{tr}Page name{/tr}</legend>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="wikiapproval_prefix">{tr}Unique page name prefix to indicate staging copy:{/tr}  <input id="wikiapproval_prefix" type="text" name="wikiapproval_prefix" value="{if $prefs.wikiapproval_prefix}{$prefs.wikiapproval_prefix|escape}{else}*{/if}" /></label></div>
</div>	

<div class="adminoptionboxchild">
	<div class="adminoption"><input type="checkbox" id="wikiapproval_hideprefix" name="wikiapproval_hideprefix" {if $prefs.wikiapproval_hideprefix eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="wikiapproval_hideprefix">{tr}Hide page name prefix{/tr}.</label></div>
</div>
</fieldset>

<fieldset><legend>{tr}Category{/tr}</legend>
<div class="adminoptionbox">
{if $prefs.feature_categories ne 'y'}<br />{icon _id=information}{tr}Categories are disabled.{/tr} <a href="tiki-admin.php?page=features" title="{tr}Features{/tr}">{tr}Enable now{/tr}</a>.{/if}
</div>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="wikiapproval_staging_category">{tr}Staging{/tr}:</label>
	<select id="wikiapproval_staging_category" name="wikiapproval_staging_category">
	<option value="0" {if $prefs.feature_wikiapproval_staging_category eq 0}selected="selected"{/if}>{tr}None{/tr}</option>
	{section name=ix loop=$catree}	
	<option value="{$catree[ix].categId|escape}" {if $prefs.wikiapproval_staging_category eq $catree[ix].categId}selected="selected"{/if}>{if $catree[ix].categpath}{$catree[ix].categpath}{else}{$catree[ix].name}{/if}</option>
	{/section}	
	</select>
	</div>
</div>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="wikiapproval_approved_category">{tr}Approved{/tr} {tr}(mandatory for feature to work){/tr}:</label>
	<select name="wikiapproval_approved_category" id="wikiapproval_approved_category">
	<option value="0" {if $prefs.feature_wikiapproval_approved_category eq 0}selected="selected"{/if}>{tr}None{/tr}</option>
	{section name=ix loop=$catree}	
	<option value="{$catree[ix].categId|escape}" {if $prefs.wikiapproval_approved_category eq $catree[ix].categId}selected="selected"{/if}>{if $catree[ix].categpath}{$catree[ix].categpath}{else}{$catree[ix].name}{/if}</option>
	{/section}	
	</select>
	</div>
</div>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="wikiapproval_outofsync_category">{tr}Out-of-sync{/tr}:</label>
	<select name="wikiapproval_outofsync_category" id="wikiapproval_outofsync_category">
	<option value="0" {if $prefs.feature_wikiapproval_outofsync_category eq 0}selected="selected"{/if}>{tr}None{/tr}</option>
	{section name=ix loop=$catree}	
	<option value="{$catree[ix].categId|escape}" {if $prefs.wikiapproval_outofsync_category eq $catree[ix].categId}selected="selected"{/if}>{if $catree[ix].categpath}{$catree[ix].categpath}{else}{$catree[ix].name}{/if}</option>
	{/section}	
	</select></div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="wikiapproval_sync_categories" name="wikiapproval_sync_categories" {if $prefs.wikiapproval_sync_categories eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="wikiapproval_sync_categories">{tr}Categorize approved pages with categories of staging copy on approval{/tr}.</label></div>
</div>
</fieldset>

<fieldset><legend>{tr}Freetags{/tr}</legend>

<div class="adminoptionbox">
{if $prefs.feature_freetags ne 'y'}<br />{icon _id=information}{tr}Freetags are disabled.{/tr} <a href="tiki-admin.php?page=features" title="{tr}Features{/tr}">{tr}Enable now{/tr}</a>.{/if}
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="wikiapproval_update_freetags" name="wikiapproval_update_freetags" {if $prefs.wikiapproval_update_freetags eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="wikiapproval_update_freetags">{tr}Replace freetags with that of staging pages, on approval{/tr}.</label></div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="wikiapproval_combine_freetags" name="wikiapproval_combine_freetags" {if $prefs.wikiapproval_combine_freetags eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="wikiapproval_combine_freetags">{tr}Add new freetags of approved copy (into tags field) when editing staging pages{/tr}.</label></div>
</div>

</fieldset>

</div>
	</div>
</div>
		{/tab}

		{tab name="{tr}Page Listings{/tr}"}
<input type="hidden" name="wikilistprefs" />	  

	{preference name=feature_listPages}
	{preference name=feature_lastChanges}
	{preference name=feature_listorphanPages}
	{preference name='wiki_dft_list_pages_lang_to_current'}

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id='feature_listorphanStructure' name="feature_listorphanStructure" {if $prefs.feature_listorphanStructure eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="feature_listorphanStructure">{tr}Pages not in structure{/tr} </label></div>
</div>	 

<div class="adminoptionbox">
<fieldset><legend>{tr}Configuration{/tr}</legend>
<div class="adminoptionbox">
{tr}Select which items to display when listing pages{/tr}:
</div>

<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="wiki_list_sortorder">{tr}Default sort order:{/tr}</label>
	<select name="wiki_list_sortorder" id="wiki_list_sortorder">
				{foreach from=$options_sortorder key=key item=item}
				<option value="{$item}" {if $prefs.wiki_list_sortorder eq $item} selected="selected"{/if}>{$key}</option>
				{/foreach}
				</select>
	</div>
<div class="adminoptionboxchild">
	<div class="adminoptionlabel"><input type="radio" id="wiki_list_sortdirection" name="wiki_list_sortdirection" value="desc" {if $prefs.wiki_list_sortdirection eq 'desc'}checked="checked"{/if} /><label for="wiki_list_sortdirection">{tr}Descending{/tr}</label></div>
	<div class="adminoptionlabel"><input type="radio" name="wiki_list_sortdirection" id="wiki_list_sortdirection2" value="asc" {if $prefs.wiki_list_sortdirection eq 'asc'}checked="checked"{/if} /><label for="wiki_list_sortdirection2">{tr}Ascending{/tr}</label></div>

</div>
</div>


<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="wiki_list_id" name="wiki_list_id" {if $prefs.wiki_list_id eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="wiki_list_id">{tr}Page ID{/tr} </label></div>
</div>	 

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="wiki_list_name" name="wiki_list_name" {if $prefs.wiki_list_name eq 'y'}checked="checked"{/if} onclick="flip('namelength');" /></div>
	<div class="adminoptionlabel"><label for="wiki_list_name">{tr}Name{/tr} </label></div>
	<div class="adminoptionboxchild" id="namelength" style="display:{if $prefs.wiki_list_name eq 'y'}block{else}none{/if};">
		<div class="adminoptionlabel">{tr}Name length:{/tr} <input type="text" name="wiki_list_name_len" value="{$prefs.wiki_list_name_len}" size="3" /></div>
	</div>
</div>	 

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="wiki_list_hits" name="wiki_list_hits" {if $prefs.wiki_list_hits eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="wiki_list_hits">{tr}Hits{/tr}</label></div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="wiki_list_lastmodif" name="wiki_list_lastmodif" {if $prefs.wiki_list_lastmodif eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="wiki_list_lastmodif">{tr}Last modification date{/tr}</label></div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="wiki_list_creator" name="wiki_list_creator" {if $prefs.wiki_list_creator eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="wiki_list_creator">{tr}Creator{/tr}</label></div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="wiki_list_user" name="wiki_list_user" {if $prefs.wiki_list_user eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="wiki_list_user">{tr}Last modified by{/tr}</label></div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="wiki_list_lastver" name="wiki_list_lastver" {if $prefs.wiki_list_lastver eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="wiki_list_lastver">{tr}Version{/tr}</label></div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" onclick="flip('commentlength');" id="wiki_list_comment" name="wiki_list_comment" {if $prefs.wiki_list_comment eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="wiki_list_comment">{tr}Edit comments{/tr}</label></div>
	<div class="adminoptionboxchild" id="commentlength" style="display:{if $prefs.wiki_list_comment eq 'y'}block{else}none{/if}">{tr}Edit Comments length:{/tr}<input type="text" name="wiki_list_comment_len" value="{$prefs.wiki_list_comment_len}" size="3" /></div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="wiki_list_description" name="wiki_list_description" {if $prefs.wiki_list_description eq 'y'}checked="checked" {/if} onclick="flip('descriptionlength');" /></div>
	<div class="adminoptionlabel"><label for="wiki_list_description">{tr}Description{/tr}</label></div>
	<div class="adminoptionboxchild" id="descriptionlength" style="display:{if $prefs.wiki_list_description eq 'y'}block{else}none{/if};"><label for="wiki_list_description_len">{tr}Description length:{/tr} </label><input type="text" name="wiki_list_description_len" value="{$prefs.wiki_list_description_len}" size="3" id="wiki_list_description_len" /></div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="wiki_list_status" name="wiki_list_status" {if $prefs.wiki_list_status eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="wiki_list_status">{tr}Status{/tr}</label></div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="wiki_list_versions" name="wiki_list_versions" {if $prefs.wiki_list_versions eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="wiki_list_versions">{tr}Versions{/tr}</label></div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="wiki_list_links" name="wiki_list_links" {if $prefs.wiki_list_links eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="wiki_list_links">{tr}Links{/tr}</label></div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="wiki_list_backlinks" name="wiki_list_backlinks" {if $prefs.wiki_list_backlinks eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="wiki_list_backlinks">{tr}Backlinks{/tr}</label></div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="wiki_list_size" name="wiki_list_size" {if $prefs.wiki_list_size eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="wiki_list_size">{tr}Size{/tr}</label></div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="wiki_list_language" name="wiki_list_language" {if $prefs.wiki_list_language eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="wiki_list_language">{tr}Language{/tr}</label>
{if $prefs.feature_multilingual ne 'y'}<br />{icon _id=information}{tr}Feature is disabled.{/tr} <a href="tiki-admin.php?page=i18n" title="{tr}Features{/tr}">{tr}Enable now{/tr}</a>.{/if}</div>	
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="wiki_list_categories" name="wiki_list_categories" {if $prefs.wiki_list_categories eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="wiki_list_categories">{tr}Categories{/tr}</label>{if $prefs.feature_categories ne 'y'}<br />{icon _id=information}{tr}Categories are disabled.{/tr} <a href="tiki-admin.php?page=features" title="{tr}Features{/tr}">{tr}Enable now{/tr}</a>.{/if}</div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="wiki_list_categories_path" name="wiki_list_categories_path" {if $prefs.wiki_list_categories_path eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="wiki_list_categories_path">{tr}Categories path{/tr}</label>{if $prefs.feature_categories ne 'y'}<br />{icon _id=information}{tr}Categories are disabled.{/tr} <a href="tiki-admin.php?page=features" title="{tr}Features{/tr}">{tr}Enable now{/tr}</a>.{/if}</div>
</div>


</fieldset>  
</div>
		{/tab}
		
	{if $prefs.feature_morcego eq 'y'}
		{tab name="{tr}Wiki 3D Browser Configuration{/tr}"}
			<fieldset>
				<legend>{tr}General{/tr}</legend>
				{preference name=wiki_feature_3d}
				{preference name=wiki_3d_autoload}
				{preference name=wiki_3d_width}
				{preference name=wiki_3d_height}
			</fieldset>
			<fieldset>
				<legend>{tr}Graph appearance{/tr}</legend>
				{preference name=wiki_3d_navigation_depth}
				{preference name=wiki_3d_node_size}
				{preference name=wiki_3d_text_size}
				{preference name=wiki_3d_spring_size}
				{preference name=wiki_3d_existing_page_color}
				{preference name=wiki_3d_missing_page_color}
			</fieldset>
			<fieldset>
				<legend>{tr}Camera settings{/tr}</legend>
				{preference name=wiki_3d_adjust_camera}
				{preference name=wiki_3d_camera_distance}
				{preference name=wiki_3d_fov}
				{preference name=wiki_3d_feed_animation_interval}
			</fieldset>
			<fieldset>
				<legend>{tr}Physics engine{/tr}</legend>
				{preference name=wiki_3d_friction_constant}
				{preference name=wiki_3d_elastic_constant}
				{preference name=wiki_3d_eletrostatic_constant}
				{preference name=wiki_3d_node_mass}
				{preference name=wiki_3d_node_charge}
			</fieldset>
		{/tab}
	{/if}
		{tab name="{tr}Screencasts{/tr}"}
			{preference name=feature_wiki_screencasts}
			{preference name=feature_wiki_screencasts_upload_type label="{tr}Upload Type{/tr}"}
			{preference name=feature_wiki_screencasts_max_size label="{tr}Maximum size{/tr}"}
			{preference name=feature_wiki_screencasts_base label="{tr}Data location{/tr}"}
			{preference name=feature_wiki_screencasts_httpbase label="{tr}HTTP Prefix{/tr}"}
			{preference name=feature_wiki_screencasts_user label="{tr}WebDav username{/tr}"}
			{preference name=feature_wiki_screencasts_pass label="{tr}WebDav password{/tr}"}
		{/tab}
	{/tabset}
<div class="heading input_submit_container" style="text-align: center">
	<input type="submit" name="wikisetprefs" value="{tr}Change preferences{/tr}" />
</div>
</form>



