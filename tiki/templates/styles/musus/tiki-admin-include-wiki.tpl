<div class="tiki">
	<div class="tiki-title">{tr}Wiki settings{/tr}</div>
	<div class="tiki-content">
		<table class="admin">
			<tr><td valign="top">
				<div class="simplebox">{tr}Dumps{/tr}: <br />
					<a title="{tr}Click here to generate a Wiki Dump{/tr}" href="tiki-admin.php?page=wiki&amp;dump=1">{tr}Generate dump{/tr}</a><br />
					<a title="{tr}Click here to download the most recent Wiki Dump{/tr}" href="dump/{$tikidomain}new.tar">{tr}Download last dump{/tr}</a>
				</div>
				<div class="simplebox">
					<form action="tiki-admin.php?page=wiki" method="post">
					{tr}Create a tag for the current wiki{/tr}<br />
					<label for="wiki-tag_current">{tr}Tag Name{/tr}:&nbsp;</label><input maxlength="20" size="10" type="text" name="tagname" id="wiki-tag_current" />
					<input type="submit" name="createtag" value="{tr}create{/tr}" />
					</form>
				</div>
				<div class="simplebox">
					<form action="tiki-admin.php?page=wiki" method="post">
					{tr}Restore the wiki{/tr}<br />
					<label for="wiki-tag_restore">{tr}Tag Name{/tr}:&nbsp;</label>
					<select name="tagname" id="wiki-tag_restore">
						{section name=sel loop=$tags}
							<option value="{$tags[sel]|escape}">{$tags[sel]}</option>
						{sectionelse}
							<option value=""></option>
						{/section}
					</select>
					<input type="submit" name="restoretag" value="{tr}restore{/tr}"/>
					</form>
				</div>
				<div class="simplebox">
					<form action="tiki-admin.php?page=wiki" method="post">
					{tr}Remove a tag{/tr}<br />
					<label for="wiki-tag_remove">{tr}Tag Name{/tr}:&nbsp;</label>
					<select name="tagname">
						{section name=sel loop=$tags}
							<option value="{$tags[sel]|escape}">{$tags[sel]}</option>
						{sectionelse}
							<option value=""></option>
						{/section}
					</select>
					<input type="submit" name="removetag" value="{tr}remove{/tr}"/>
					</form>
				</div>

		<div class="simplebox">{tr}Wiki comments settings{/tr}
			<form method="post" action="tiki-admin.php?page=wiki">
				<table class="admin">
				<tr><td><label for="wiki-comments">{tr}Default number of comments per page{/tr}:&nbsp;</label></td><td><input size="5" type="text" name="wiki_comments_per_page" id="wiki-comments" value="{$wiki_comments_per_page|escape}" /></td></tr>
				<tr><td><label for="wiki-comments_order">{tr}Comments default ordering{/tr}:&nbsp;</label>
					<select name="wiki_comments_default_ordering" id="wiki-comments_order">
						<option value="commentDate_desc" {if $wiki_comments_default_ordering eq 'commentDate_desc'}selected="selected"{/if}>{tr}Newest first{/tr}</option>
						<option value="commentDate_asc" {if $wiki_comments_default_ordering eq 'commentDate_asc'}selected="selected"{/if}>{tr}Oldest first{/tr}</option>
						<option value="points_desc" {if $wiki_comments_default_ordering eq 'points_desc'}selected="selected"{/if}>{tr}Points{/tr}</option>
					</select>
				</td></tr>
				<tr><td><input type="submit" name="wikiprefs" value="{tr}Change preferences{/tr}" /></td></tr>
				</table>
			</form>
		</div>
		<div class="simplebox">{tr}Wiki attachments{/tr}
			<form method="post" action="tiki-admin.php?page=wiki">
				<table class="admin">
					<tr><td>
						<label for="wiki-attach">{tr}Wiki attachments{/tr}:&nbsp;</label></td><td>
						<input type="checkbox" name="feature_wiki_attachments" id="wiki-attach" {if $feature_wiki_attachments eq 'y'}checked="checked"{/if}/>
					</td></tr>
					<tr><td>
						<label for="wiki-attach_db">{tr}Use database to store files{/tr}:&nbsp;</label></td><td>
						<input type="radio" name="w_use_db" id="wiki-attach_db" value="y" {if $w_use_db eq 'y'}checked="checked"{/if}/>
					</td></tr>
					<tr><td>
						<label for="wiki-attach_dir">{tr}Use a directory to store files{/tr}:&nbsp;</label></td><td>
						<input type="radio" name="w_use_db" id="wiki-attach_dir" value="n" {if $w_use_db eq 'n'}checked="checked"{/if}/>
						{tr}Path{/tr}:<input type="text" name="w_use_dir" value="{$w_use_dir|escape}" />
					</td></tr>
					<tr><td colspan="2">
						<input type="submit" name="wikiattprefs" value="{tr}Change preferences{/tr}" /></td></tr>
				</table>
			</form>
		</div>
		<div class="simplebox">{tr}Export Wiki Pages{/tr}
			<form method="post" action="tiki-admin.php?page=wiki">
			<table class="admin">
				<tr><td align="center"><a title="" href="tiki-export_wiki_pages.php">{tr}Export{/tr}</a></td></tr>
			</table>
			</form>
		</div>
		<div class="simplebox">
			<a title="" href="tiki-admin.php?page=wiki&amp;rmvunusedpic=1">{tr}Remove unused pictures{/tr}</a>
		</div>
		<div class="simplebox">
			<form method="post" action="tiki-admin.php?page=wiki">
				<table class="admin"><tr><td>
					<label for="wiki-homepage">{tr}Wiki Home Page{/tr}:&nbsp;</label><input type="text" name="wikiHomePage" id="wiki-homepage" value="{$wikiHomePage|escape}" /></td><td>
					<input type="submit" name="setwikihome" value="{tr}set{/tr}" />
				</td></tr></table>
			</form>
		</div>
		<div class="simplebox">
			<form method="post" action="tiki-admin.php?page=wiki">
				<table class="admin"><tr><td>{tr}Wiki Discussion{/tr}</td></tr>
					<tr><td>
						<label for="wiki-discuss">{tr}Discuss pages on forums{/tr}:&nbsp;</label></td><td>
						<input type="checkbox" name="feature_wiki_discuss" {if $feature_wiki_discuss eq 'y'}checked="checked"{/if}/>
					</td></tr>
					<tr><td>
						<label for="wiki-forum">{tr}Forum{/tr}:&nbsp;</label></td><td>
						<input maxlength="20" size="10" type="text" name="wiki_forum" value="{$wiki_forum|escape}"/>
					</td></tr>
					<tr><td colspan="2"><input type="submit" name="wikidiscussprefs" value="{tr}Change preferences{/tr}" /></td></tr>
				</table>
			</form>
		</div>
		<div class="simplebox">
			<form method="post" action="tiki-admin.php?page=wiki">
				<table class="admin">
					<tr><td><label for="wiki-page_names">{tr}Wiki Page Names{/tr}</label></td></tr>
					<tr><td><p>{tr}Strict allows page names with only letters, numbers, underscore, dash, period and semicolon (dash, period and semicolon not allowed at the beginning and the end).{/tr}<br />
					{tr}Full adds accented characters.{/tr}<br />
					{tr}Complete allows <em>anything at all</em>.  I (<a title="" href="http://tikiwiki.org/tiki-index.php?page=UserPagerlpowell">rlpowell</a>) cannot guarantee that it is bug-free or secure.{/tr}</p>
					<p>{tr}Note that this does not affect WikiWord recognition, only page names surrounded by (( and )).{/tr}</p>
					<select name="wiki_page_regex" id="wiki-page_names">
						<option value='complete' {if $wiki_page_regex eq 'complete'}selected="selected"{/if}>{tr}complete{/tr}</option>
						<option value='full' {if $wiki_page_regex eq 'full'}selected="selected"{/if}>{tr}full{/tr}</option>
						<option value='strict' {if $wiki_page_regex eq 'strict'}selected="selected"{/if}>{tr}strict{/tr}</option>
					</select>
					<input type="submit" name="setwikiregex" value="{tr}set{/tr}" />
					</td></tr>
				</table>
			</form>
		</div>
		<div class="simplebox">{tr}Wiki page list configuration{/tr}
			<form method="post" action="tiki-admin.php?page=wiki">
				<table class="admin">
					<tr><td>
						<label for="wiki-list_name">{tr}Name{/tr}:&nbsp;</label></td><td>
						<input type="checkbox" name="wiki_list_name" {if $wiki_list_name eq 'y'}checked="checked"{/if} />
					</td></tr>
					<tr><td>
						<label for="wiki-list_hits">{tr}Hits{/tr}:&nbsp;</label></td><td>
						<input type="checkbox" name="wiki_list_hits" {if $wiki_list_hits eq 'y'}checked="checked"{/if} />
					</td></tr>
					<tr><td>
						<label for="wiki-list_mod">{tr}Last modification date{/tr}:&nbsp;</label></td><td>
						<input type="checkbox" name="wiki_list_lastmodif" {if $wiki_list_lastmodif eq 'y'}checked="checked"{/if} />
					</td></tr>
					<tr><td>
						<label for="wiki-list_creator">{tr}Creator{/tr}:&nbsp;</label></td><td>
						<input type="checkbox" name="wiki_list_creator" id="wiki-list_creator" {if $wiki_list_creator eq 'y'}checked="checked"{/if} />
					</td></tr>
					<tr><td>
						<label for="wiki-list_user">{tr}User{/tr}:&nbsp;</label></td><td>
						<input type="checkbox" name="wiki_list_user" id="wiki-list_user" {if $wiki_list_user eq 'y'}checked="checked"{/if} />
					</td></tr>
					<tr><td>
						<label>{tr}Last version{/tr}</label></td><td>
						<input type="checkbox" name="wiki_list_lastver" {if $wiki_list_lastver eq 'y'}checked="checked"{/if} />
					</td></tr>
					<tr><td>
						<label>{tr}Comment{/tr}</label></td><td>
						<input type="checkbox" name="wiki_list_comment" {if $wiki_list_comment eq 'y'}checked="checked"{/if} />
					</td></tr>
					<tr><td>
						<label>{tr}Status{/tr}</label></td><td>
						<input type="checkbox" name="wiki_list_status" {if $wiki_list_status eq 'y'}checked="checked"{/if} />
					</td></tr>
					<tr><td>
						<label>{tr}Versions{/tr}</label></td><td>
						<input type="checkbox" name="wiki_list_versions" {if $wiki_list_versions eq 'y'}checked="checked"{/if} />
					</td></tr>
					<tr><td>
						<label>{tr}Links{/tr}</label></td><td>
						<input type="checkbox" name="wiki_list_links" {if $wiki_list_links eq 'y'}checked="checked"{/if} />
					</td></tr>
					<tr><td>
						<label>{tr}Backlinks{/tr}</label></td><td>
						<input type="checkbox" name="wiki_list_backlinks" {if $wiki_list_backlinks eq 'y'}checked="checked"{/if} />
					</td></tr>
					<tr><td>
						<label>{tr}Size{/tr}</label></td><td>
						<input type="checkbox" name="wiki_list_size" {if $wiki_list_size eq 'y'}checked="checked"{/if} />
					</td></tr>
					<tr><td colspan="2"><input type="submit" name="wikilistprefs" value="{tr}Change preferences{/tr}" /></td></tr>
				</table>
			</form>
		</div>
	</td>

    <td valign="top">
    <div class="simplebox">{tr}Wiki Features{/tr}:<br />
    <form action="tiki-admin.php?page=wiki" method="post">
    <table class="admin">
    <tr><td>
	<label>{tr}Sandbox{/tr}:</label></td><td>
	<input type="checkbox" name="feature_sandbox" {if $feature_sandbox eq 'y'}checked="checked"{/if}/>
</td></tr>
    <tr><td>
	<label>{tr}Last changes{/tr}:</label></td><td>
	<input type="checkbox" name="feature_lastChanges" {if $feature_lastChanges eq 'y'}checked="checked"{/if}/>
</td></tr>
    <tr><td>
	<label>{tr}Dump{/tr}:</label></td><td>
	<input type="checkbox" name="feature_dump" {if $feature_dump eq 'y'}checked="checked"{/if}/>
</td></tr>
    {* <tr><td>
	<label>{tr}Ranking{/tr}:</label></td><td>
	<input type="checkbox" name="feature_ranking" {if $feature_ranking eq 'y'}checked="checked"{/if}/>
</td></tr> *}
    <tr><td>
	<label>{tr}History{/tr}:</label></td><td>
	<input type="checkbox" name="feature_history" {if $feature_history eq 'y'}checked="checked"{/if}/>
</td></tr>
    <tr><td>
	<label>{tr}List pages{/tr}:</label></td><td>
	<input type="checkbox" name="feature_listPages" {if $feature_listPages eq 'y'}checked="checked"{/if}/>
</td></tr>
    <tr><td>
	<label>{tr}Backlinks{/tr}:</label></td><td>
	<input type="checkbox" name="feature_backlinks" {if $feature_backlinks eq 'y'}checked="checked"{/if}/>
</td></tr>
    <tr><td>
	<label>{tr}Like pages{/tr}:</label></td><td>
	<input type="checkbox" name="feature_likePages" {if $feature_likePages eq 'y'}checked="checked"{/if}/>
</td></tr>
    <tr><td>
	<label>{tr}Rankings{/tr}:</label></td><td>
	<input type="checkbox" name="feature_wiki_rankings" {if $feature_wiki_rankings eq 'y'}checked="checked"{/if}/>
</td></tr>
    <tr><td>
	<label>{tr}Undo{/tr}:</label></td><td>
	<input type="checkbox" name="feature_wiki_undo" {if $feature_wiki_undo eq 'y'}checked="checked"{/if}/>
</td></tr>
    <tr><td>
	<label>{tr}MultiPrint{/tr}:</label></td><td>
	<input type="checkbox" name="feature_wiki_multiprint" {if $feature_wiki_multiprint eq 'y'}checked="checked"{/if}/>
</td></tr>
    <tr><td>
	<label>{tr}PDF generation{/tr}:</label></td><td>
	<input type="checkbox" name="feature_wiki_pdf" {if $feature_wiki_pdf eq 'y'}checked="checked"{/if}/>
</td></tr>
    <tr><td>
	<label>{tr}Comments{/tr}:</label></td><td>
	<input type="checkbox" name="feature_wiki_comments" {if $feature_wiki_comments eq 'y'}checked="checked"{/if}/>
</td></tr>
    <tr><td>
	<label>{tr}Spellchecking{/tr}:</label></td><td>
	<input type="checkbox" name="wiki_spellcheck" {if $wiki_spellcheck eq 'y'}checked="checked"{/if}/>
</td></tr>
    <tr><td>
<label>{tr}Use templates{/tr}:</label></td><td>
<input type="checkbox" name="feature_wiki_templates" {if $feature_wiki_templates eq 'y'}checked="checked"{/if}/>
</td></tr>
    <tr><td>
<label>{tr}Warn on edit{/tr}:</label></td><td>
<input type="checkbox" name="feature_warn_on_edit" {if $feature_warn_on_edit eq 'y'}checked="checked"{/if}/>
    <select name="warn_on_edit_time">
    <option value="1" {if $warn_on_edit_time eq 1}selected="selected"{/if}>1</option>
    <option value="2" {if $warn_on_edit_time eq 2}selected="selected"{/if}>2</option>
    <option value="5" {if $warn_on_edit_time eq 5}selected="selected"{/if}>5</option>
    <option value="10" {if $warn_on_edit_time eq 10}selected="selected"{/if}>10</option>
    <option value="15" {if $warn_on_edit_time eq 15}selected="selected"{/if}>15</option>
    <option value="30" {if $warn_on_edit_time eq 30}selected="selected"{/if}>30</option>
    </select> {tr}mins{/tr}
    </td></tr>
    <tr><td>
<label>{tr}Pictures{/tr}:</label></td><td>
<input type="checkbox" name="feature_wiki_pictures" {if $feature_wiki_pictures eq 'y'}checked="checked"{/if}/>
</td></tr>
    <tr><td>
<label>{tr}Use page description{/tr}:</label></td><td>
<input type="checkbox" name="feature_wiki_description" {if $feature_wiki_description eq 'y'}checked="checked"{/if}/>
</td></tr>
    <tr><td>
<label>{tr}Show page title{/tr}:</label></td><td>
<input type="checkbox" name="feature_page_title" {if $feature_page_title eq 'y'}checked="checked"{/if}/>
</td></tr>
    <tr><td>
<label>{tr}Cache wiki pages (global){/tr}:</label></td><td>
    <select name="wiki_cache">
    <option value="0" {if $wiki_cache eq 0}selected="selected"{/if}>0 ({tr}no cache{/tr})</option>
    <option value="60" {if $wiki_cache eq 60}selected="selected"{/if}>1 {tr}minute{/tr}</option>
    <option value="300" {if $wiki_cache eq 300}selected="selected"{/if}>5 {tr}minutes{/tr}</option>
    <option value="600" {if $wiki_cache eq 600}selected="selected"{/if}>10 {tr}minutes{/tr}</option>
    <option value="900" {if $wiki_cache eq 900}selected="selected"{/if}>15 {tr}minutes{/tr}</option>
    <option value="1800" {if $wiki_cache eq 1800}selected="selected"{/if}>30 {tr}minutes{/tr}</option>
    <option value="3600" {if $wiki_cache eq 3600}selected="selected"{/if}>1 {tr}hour{/tr}</option>
    <option value="7200" {if $wiki_cache eq 7200}selected="selected"{/if}>2 {tr}hours{/tr}</option>
    </select> 
    </td></tr>
	<tr><td>
<label>{tr}Individual cache{/tr}:</label></td><td>
<input type="checkbox" name="feature_wiki_icache" {if $feature_wiki_icache eq 'y'}checked="checked"{/if}/>
</td></tr>
    <tr><td>
<label>{tr}Footnotes{/tr}:</label></td><td>
<input type="checkbox" name="feature_wiki_footnotes" {if $feature_wiki_footnotes eq 'y'}checked="checked"{/if}/>
</td></tr>
    <tr><td>
<label>{tr}Users can lock pages (if perm){/tr}:</label></td><td>
<input type="checkbox" name="feature_wiki_usrlock" {if $feature_wiki_usrlock eq 'y'}checked="checked"{/if}/>
</td></tr>    
    <tr><td>
<label>{tr}Use WikiWords{/tr}:</label></td><td>
<input type="checkbox" name="feature_wikiwords" {if $feature_wikiwords eq 'y'}checked="checked"{/if}/>
</td></tr>    
    <tr><td>
<label>{tr}Link plural WikiWords to their singular forms{/tr}:</label></td><td>
<input type="checkbox" name="feature_wiki_plurals" {if $feature_wiki_plurals eq 'y'}checked="checked"{/if}/>
</td></tr>
    <tr><td>
<label>{tr}Page creators are admin of their pages{/tr}:</label></td><td>
<input type="checkbox" name="wiki_creator_admin" {if $wiki_creator_admin eq 'y'}checked="checked"{/if}/>
</td></tr>    
    <tr><td>
<label>{tr}Tables syntax{/tr}:</label></td><td>
    <select name="feature_wiki_tables">
    <option value="old" {if $feature_wiki_tables eq 'old'}selected="selected"{/if}>{tr}|| for rows{/tr}</option>
    <option value="new" {if $feature_wiki_tables eq 'new'}selected="selected"{/if}>{tr}\n for rows{/tr}</option>
    </select>
    </td></tr>
    <tr><td>
<label>{tr}Automonospaced text{/tr}:</label></td><td>
<input type="checkbox" name="feature_wiki_monosp" {if $feature_wiki_monosp eq 'y'}checked="checked"{/if}/>
</td></tr>
    <tr><td>
<label>{tr}Uses Slideshow{/tr}:</label></td><td>
<input type="checkbox" name="wiki_uses_slides" {if $wiki_uses_slides eq 'y'}checked="checked"{/if}/>
</td></tr>
    <tr><td colspan="2"><input type="submit" name="wikifeatures" value="{tr}Change preferences{/tr}" /></td></tr>
    </table>
    </form>
    </div>
    
    <div class="simplebox">{tr}Wiki History{/tr}
    <form action="tiki-admin.php?page=wiki" method="post">
    <table class="admin">
    <tr><td>
<label>{tr}Maximum number of versions for history{/tr}: </label></td><td>
<input size="5" type="text" name="maxVersions" value="{$maxVersions|escape}" />
</td></tr>
    <tr><td>
<label>{tr}Never delete versions younger than days{/tr}: </label></td><td>
<input size="5" type="text" name="keep_versions" value="{$keep_versions|escape}" />
</td></tr>
    <tr><td colspan="2"><input type="submit" name="wikisetprefs" value="{tr}Change preferences{/tr}" /></td></tr>
    </table>
    </form>
    </div>

    <div class="simplebox">{tr}Copyright Management{/tr}
    <form action="tiki-admin.php?page=wiki" method="post">
    <table class="admin">
    <tr><td>
<label>{tr}Enable Feature{/tr}:</label></td><td>
<input type="checkbox" name="wiki_feature_copyrights" {if $wiki_feature_copyrights eq 'y'}checked="checked"{/if}/>
</td></tr>
    <tr><td>
<label>{tr}License Page{/tr}: </label></td><td>
<input type="text" name="wikiLicensePage" value="{$wikiLicensePage|escape}" />
</td></tr>
    <tr><td>
<label>{tr}Submit Notice{/tr}: </label></td><td>
<input type="text" name="wikiSubmitNotice" value="{$wikiSubmitNotice|escape}" />
</td></tr>
    <tr><td colspan="2"><input type="submit" name="wikisetcopyright" value="{tr}Change preferences{/tr}" /></td></tr>
    </table>
    </form>
    </div>

    <div class="simplebox">{tr}Wiki Watch{/tr}:<br />
    <form action="tiki-admin.php?page=wiki" method="post">
    <table class="admin">
    <tr><td>
<label>{tr}Create watch for author on page creation{/tr}:</label></td><td>
<input type="checkbox" name="wiki_watch_author" {if $wiki_watch_author eq 'y'}checked="checked"{/if}/>
</td></tr>
    <tr><td>
<label>{tr}Enable watches on comments{/tr}:</label></td><td>
<input type="checkbox" name="wiki_watch_comments" {if $wiki_watch_comments eq 'y'}checked="checked"{/if}/>
</td></tr>
    <tr><td>
<label>{tr}Enable watch events when I am the editor{/tr}:</label></td><td>
<input type="checkbox" name="wiki_watch_editor" {if $wiki_watch_editor eq 'y'}checked="checked"{/if}/>
</td></tr>
    <tr><td align="center" colspan="2"><input type="submit" name="wikisetwatch" value="{tr}Set{/tr}" /></td></tr>
    </table>
    </form>
    </div>
    </td></tr>
    </table>
</div>
</div>
