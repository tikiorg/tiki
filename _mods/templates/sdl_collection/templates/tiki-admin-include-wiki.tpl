<div class="cbox">
<div class="cbox-title">{tr}Wiki Settings{/tr}</div>
<div class="cbox-data">
    <table class="admin">
    <tr><td  valign="top">
    <div class="simplebox">
    {tr}Dumps{/tr}:<br />
    <a class="link" href="tiki-admin.php?page=wiki&amp;dump=1">{tr}Generate dump{/tr}</a><br />
    <a class="link" href="dump/{$tikidomain}new.tar">{tr}Download last dump{/tr}</a>
    </div>
    
    <div class="simplebox">
    <form action="tiki-admin.php?page=wiki" method="post">
    {tr}Create a tag for the current wiki{/tr}<br />
    {tr}Tag Name{/tr}<input  maxlength="20" size="10" type="text" name="tagname"/>
    <input type="submit" name="createtag" value="{tr}Create{/tr}"/>
    </form>
    </div>
    
    <div class="simplebox">
    <form action="tiki-admin.php?page=wiki" method="post">
    {tr}Restore the wiki{/tr}<br />
    {tr}Tag Name{/tr}: <select name="tagname">
          {section name=sel loop=$tags}
          <option value="{$tags[sel]|escape}">{$tags[sel]}</option>
          {sectionelse}
          <option value=''></option>
          {/section}
          </select>
    <input type="submit" name="restoretag" value="{tr}Restore{/tr}"/>          
    </form>
    </div>
    
    <div class="simplebox">
    <form action="tiki-admin.php?page=wiki" method="post">
    {tr}Remove a tag{/tr}<br />
    {tr}Tag Name{/tr}<select name="tagname">
          {section name=sel loop=$tags}
          <option value="{$tags[sel]|escape}">{$tags[sel]}</option>
          {sectionelse}
          <option value=''></option>
          {/section}
          </select>
    <input type="submit" name="removetag" value="{tr}Remove{/tr}" onclick="return confirmTheLink(this,'{tr}Are you sure you want to delete this tag?{/tr}')" title="{tr}Delete item from wiki?{/tr}"/>          
    </form>
    </div>    
    
    
    <div class="simplebox">
    {tr}Wiki comments settings{/tr}
    <form method="post" action="tiki-admin.php?page=wiki">
    <table class="admin">
    <tr><td class="form">{tr}Default number of comments per page{/tr}: </td><td><input size="5" type="text" name="wiki_comments_per_page" value="{$wiki_comments_per_page|escape}" /></td></tr>
    <tr><td class="form">{tr}Comments default ordering{/tr}
    </td><td>
    <select name="wiki_comments_default_ordering">
    <option value="commentDate_desc" {if $wiki_comments_default_ordering eq 'commentDate_desc'}selected="selected"{/if}>{tr}Newest first{/tr}</option>
		<option value="commentDate_asc" {if $wiki_comments_default_ordering eq 'commentDate_asc'}selected="selected"{/if}>{tr}Oldest first{/tr}</option>
    <option value="points_desc" {if $wiki_comments_default_ordering eq 'points_desc'}selected="selected"{/if}>{tr}Points{/tr}</option>
    </select>
    </td></tr>
    <tr><td colspan="2" class="button"><input type="submit" name="wikiprefs" value="{tr}Change Preferences{/tr}" /></td></tr>
    </table>
    </form>
    </div>
    
    <div class="simplebox">
    {tr}Wiki attachments{/tr}
    <form method="post" action="tiki-admin.php?page=wiki">
    <table class="admin">
    <tr><td class="form">{tr}Wiki attachments{/tr}:</td><td><input type="checkbox" name="feature_wiki_attachments" {if $feature_wiki_attachments eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Use database to store files{/tr}:</td><td><input type="radio" name="w_use_db" value="y" {if $w_use_db eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Use a directory to store files{/tr}:</td><td class="form"><input type="radio" name="w_use_db" value="n" {if $w_use_db eq 'n'}checked="checked"{/if}/> {tr}Path{/tr}:<input type="text" name="w_use_dir" value="{$w_use_dir|escape}" /> </td></tr>
    <tr><td colspan="2" class="button"><input type="submit" name="wikiattprefs" value="{tr}Change Preferences{/tr}" /></td></tr>
    </table>
    </form>
    </div>
    
    <div class="simplebox">
    {tr}Export Wiki Pages{/tr}
    <form method="post" action="tiki-admin.php?page=wiki">
    <table class="admin">
    <tr><td align="center" colspan="2"><a class="link" href="tiki-export_wiki_pages.php">{tr}Export{/tr}</a></tr>
    </table>
    </form>
    </div>
    
    
    <div class="simplebox">
    <a class="link" href="tiki-admin.php?page=wiki&amp;rmvunusedpic=1">{tr}Remove unused pictures{/tr}</a>
    </div>
    
    <div class="simplebox">
    <form method="post" action="tiki-admin.php?page=wiki">
    <table class="admin"><tr>
    <td class="form">{tr}Wiki Home Page{/tr}</td><td class="form"><input type="text" name="wikiHomePage" value="{$wikiHomePage|escape}" />
    <input type="submit" name="setwikihome" value="{tr}Set{/tr}" />
    </td>
    </tr></table>
    </form>
    </div>
    
    <div class="simplebox">
    <form method="post" action="tiki-admin.php?page=wiki">
    <table class="admin"><tr>
    <td colspan="2" class="form">{tr}Wiki Discussion{/tr}</td></tr><tr><td class="form">
    {tr}Discuss pages on forums{/tr}: </td><td>
    <input type="checkbox" name="feature_wiki_discuss" {if $feature_wiki_discuss eq 'y'}checked="checked"{/if}/> </td></tr>
    <tr><td class="form">{tr}Forum{/tr}:</td><td class="form">
    <input maxlength="20" size="10" type="text" name="wiki_forum" value="{$wiki_forum|escape}"/></td></tr>
    <tr><td colspan="2" class="button"><input type="submit" name="wikidiscussprefs" value="{tr}Change Preferences{/tr}" />
    </td>
    </tr></table>
    </form>
    </div>
  
    <div class="simplebox">
    <form method="post" action="tiki-admin.php?page=wiki">
    <table class="admin"><tr>
    <td colspan="2" class="form">{tr}Wiki Link Format{/tr}</td></tr><tr><td class="form">
    {tr}Controls recognition of Wiki links using the two parenthesis Wiki link syntax <i>((page name))</i>.{/tr}
    </td></tr><tr><td>
    <select name="wiki_page_regex">
    <option value='complete' {if $wiki_page_regex eq 'complete'}selected="selected"{/if}>{tr}complete{/tr}</option>
    <option value='full' {if $wiki_page_regex eq 'full'}selected="selected"{/if}>{tr}latin{/tr}</option>
    <option value='strict' {if $wiki_page_regex eq 'strict'}selected="selected"{/if}>{tr}english{/tr}</option>
    </select>
    <input type="submit" name="setwikiregex" value="{tr}Set{/tr}" />
    </td>
    </tr></table>
    </form>
    </div>
    
    <div class="simplebox">
    {tr}Wiki page list configuration{/tr}
    <form method="post" action="tiki-admin.php?page=wiki">
    <table class="admin">
    	<tr>
    		<td class="form">{tr}Name{/tr}</td>
    		<td class="form">
    			<input type="checkbox" name="wiki_list_name" {if $wiki_list_name eq 'y'}checked="checked"{/if} />
    		</td>
    	</tr>
    	<tr>
    		<td class="form">{tr}Hits{/tr}</td>
    		<td class="form">
    			<input type="checkbox" name="wiki_list_hits" {if $wiki_list_hits eq 'y'}checked="checked"{/if} />
    		</td>
    	</tr>
     	<tr>
    		<td class="form">{tr}Last modification date{/tr}</td>
    		<td class="form">
    			<input type="checkbox" name="wiki_list_lastmodif" {if $wiki_list_lastmodif eq 'y'}checked="checked"{/if} />
    		</td>
    	</tr>
    	<tr>
    		<td class="form">{tr}Creator{/tr}</td>
    		<td class="form">
    			<input type="checkbox" name="wiki_list_creator" {if $wiki_list_creator eq 'y'}checked="checked"{/if} />
    		</td>
    	</tr>
    	<tr>
    		<td class="form">{tr}User{/tr}</td>
    		<td class="form">
    			<input type="checkbox" name="wiki_list_user" {if $wiki_list_user eq 'y'}checked="checked"{/if} />
    		</td>
    	</tr>
    	<tr>
    		<td class="form">{tr}Last version{/tr}</td>
    		<td class="form">
    			<input type="checkbox" name="wiki_list_lastver" {if $wiki_list_lastver eq 'y'}checked="checked"{/if} />
    		</td>
    	</tr>
    	<tr>
    		<td class="form">{tr}Comment{/tr}</td>
    		<td class="form">
    			<input type="checkbox" name="wiki_list_comment" {if $wiki_list_comment eq 'y'}checked="checked"{/if} />
    		</td>
    	</tr>
    	<tr>
    		<td class="form">{tr}Status{/tr}</td>
    		<td class="form">
    			<input type="checkbox" name="wiki_list_status" {if $wiki_list_status eq 'y'}checked="checked"{/if} />
    		</td>
    	</tr>
    	<tr>
    		<td class="form">{tr}Versions{/tr}</td>
    		<td class="form">
    			<input type="checkbox" name="wiki_list_versions" {if $wiki_list_versions eq 'y'}checked="checked"{/if} />
    		</td>
    	</tr>
    	<tr>
    		<td class="form">{tr}Links{/tr}</td>
    		<td class="form">
    			<input type="checkbox" name="wiki_list_links" {if $wiki_list_links eq 'y'}checked="checked"{/if} />
    		</td>
    	</tr>
    	<tr>
    		<td class="form">{tr}Backlinks{/tr}</td>
    		<td class="form">
    			<input type="checkbox" name="wiki_list_backlinks" {if $wiki_list_backlinks eq 'y'}checked="checked"{/if} />
    		</td>
    	</tr>
    	<tr>
    		<td class="form">{tr}Size{/tr}</td>
    		<td class="form">
    			<input type="checkbox" name="wiki_list_size" {if $wiki_list_size eq 'y'}checked="checked"{/if} />
    		</td>
    	</tr>

    	<tr>
    		<td colspan="2" class="button">
   	 			<input type="submit" name="wikilistprefs" value="{tr}Change Preferences{/tr}" />
    		</td>
    	</tr>
    </table>
    </form>
    </div>

    </td>
    
    <td  valign="top">
    <div class="simplebox">
    {tr}Wiki Features{/tr}:<br />
    <form action="tiki-admin.php?page=wiki" method="post">
    <table class="admin">
    <tr><td class="form">{tr}Sandbox{/tr}:</td><td><input type="checkbox" name="feature_sandbox" {if $feature_sandbox eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Last changes{/tr}:</td><td><input type="checkbox" name="feature_lastChanges" {if $feature_lastChanges eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Dump{/tr}:</td><td><input type="checkbox" name="feature_dump" {if $feature_dump eq 'y'}checked="checked"{/if}/></td></tr>
    <!--<tr><td class="form">{tr}Ranking{/tr}:</td><td><input type="checkbox" name="feature_ranking" {if $feature_ranking eq 'y'}checked="checked"{/if}/></td></tr>-->
    <tr><td class="form">{tr}History{/tr}:</td><td><input type="checkbox" name="feature_history" {if $feature_history eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}List pages{/tr}:</td><td><input type="checkbox" name="feature_listPages" {if $feature_listPages eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Backlinks{/tr}:</td><td><input type="checkbox" name="feature_backlinks" {if $feature_backlinks eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Like pages{/tr}:</td><td><input type="checkbox" name="feature_likePages" {if $feature_likePages eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Rankings{/tr}:</td><td><input type="checkbox" name="feature_wiki_rankings" {if $feature_wiki_rankings eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Undo{/tr}:</td><td><input type="checkbox" name="feature_wiki_undo" {if $feature_wiki_undo eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}MultiPrint{/tr}:</td><td><input type="checkbox" name="feature_wiki_multiprint" {if $feature_wiki_multiprint eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}PDF generation{/tr}:</td><td><input type="checkbox" name="feature_wiki_pdf" {if $feature_wiki_pdf eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Comments{/tr}:</td><td><input type="checkbox" name="feature_wiki_comments" {if $feature_wiki_comments eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Spellchecking{/tr}:</td><td><input type="checkbox" name="wiki_spellcheck" {if $wiki_spellcheck eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Use templates{/tr}:</td><td><input type="checkbox" name="feature_wiki_templates" {if $feature_wiki_templates eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Warn on edit{/tr}:</td><td><input type="checkbox" name="feature_warn_on_edit" {if $feature_warn_on_edit eq 'y'}checked="checked"{/if}/>
    <select name="warn_on_edit_time">
    <option value="1" {if $warn_on_edit_time eq 1}selected="selected"{/if}>1</option>
    <option value="2" {if $warn_on_edit_time eq 2}selected="selected"{/if}>2</option>
    <option value="5" {if $warn_on_edit_time eq 5}selected="selected"{/if}>5</option>
    <option value="10" {if $warn_on_edit_time eq 10}selected="selected"{/if}>10</option>
    <option value="15" {if $warn_on_edit_time eq 15}selected="selected"{/if}>15</option>
    <option value="30" {if $warn_on_edit_time eq 30}selected="selected"{/if}>30</option>
    </select> {tr}mins{/tr}
    </td></tr>
    <tr><td class="form">{tr}Pictures{/tr}:</td><td><input type="checkbox" name="feature_wiki_pictures" {if $feature_wiki_pictures eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Use page description{/tr}:</td><td><input type="checkbox" name="feature_wiki_description" {if $feature_wiki_description eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Show page title{/tr}:</td><td><input type="checkbox" name="feature_page_title" {if $feature_page_title eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Cache wiki pages (global){/tr}:</td><td>
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
	<tr><td class="form">{tr}Individual cache{/tr}:</td><td><input type="checkbox" name="feature_wiki_icache" {if $feature_wiki_icache eq 'y'}checked="checked"{/if}/></td></tr>    
	
    <tr><td class="form">{tr}Footnotes{/tr}:</td><td><input type="checkbox" name="feature_wiki_footnotes" {if $feature_wiki_footnotes eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Users can lock pages (if perm){/tr}:</td><td><input type="checkbox" name="feature_wiki_usrlock" {if $feature_wiki_usrlock eq 'y'}checked="checked"{/if}/></td></tr>    
    <tr><td class="form">{tr}Use WikiWords{/tr}:</td><td><input type="checkbox" name="feature_wikiwords" {if $feature_wikiwords eq 'y'}checked="checked"{/if}/></td></tr>    
    <tr><td class="form">{tr}Link plural WikiWords to their singular forms{/tr}:</td><td><input type="checkbox" name="feature_wiki_plurals" {if $feature_wiki_plurals eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Page creators are admin of their pages{/tr}:</td><td><input type="checkbox" name="wiki_creator_admin" {if $wiki_creator_admin eq 'y'}checked="checked"{/if}/></td></tr>    
    <tr><td class="form">{tr}Tables syntax{/tr}:</td><td>
    <select name="feature_wiki_tables">
    <option value="old" {if $feature_wiki_tables eq 'old'}selected="selected"{/if}>{tr}|| for rows{/tr}</option>
    <option value="new" {if $feature_wiki_tables eq 'new'}selected="selected"{/if}>{tr}\n for rows{/tr}</option>
    </select>
    </td></tr>
    <tr><td class="form">{tr}Automonospaced text{/tr}:</td><td><input type="checkbox" name="feature_wiki_monosp" {if $feature_wiki_monosp eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Uses Slideshow{/tr}:</td><td><input type="checkbox" name="wiki_uses_slides" {if $wiki_uses_slides eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td colspan="2" class="button"><input type="submit" name="wikifeatures" value="{tr}Change Preferences{/tr}" /></td></tr>
    </table>
    </form>
    </div>
    
    <div class="simplebox">
    {tr}Wiki History{/tr}
    <form action="tiki-admin.php?page=wiki" method="post">
    <table class="admin">
    <tr><td class="form">{tr}Maximum number of versions for history{/tr}: </td><td><input size="5" type="text" name="maxVersions" value="{$maxVersions|escape}" /></td></tr>
    <tr><td class="form">{tr}Never delete versions younger than days{/tr}: </td><td><input size="5" type="text" name="keep_versions" value="{$keep_versions|escape}" /></td></tr>
    <tr><td colspan="2" class="button"><input type="submit" name="wikisetprefs" value="{tr}Change Preferences{/tr}" /></td></tr>
    </table>
    </form>
    </div>

    <div class="simplebox">
    {tr}Copyright Management{/tr}
    <form action="tiki-admin.php?page=wiki" method="post">
    <table class="admin">
    <tr><td class="form">{tr}Enable Feature{/tr}:</td><td><input type="checkbox" name="wiki_feature_copyrights" {if $wiki_feature_copyrights eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}License Page{/tr}: </td><td><input type="text" name="wikiLicensePage" value="{$wikiLicensePage|escape}" /></td></tr>
    <tr><td class="form">{tr}Submit Notice{/tr}: </td><td><input type="text" name="wikiSubmitNotice" value="{$wikiSubmitNotice|escape}" /></td></tr>
    <tr><td colspan="2" class="button"><input type="submit" name="wikisetcopyright" value="{tr}Change Preferences{/tr}" /></td></tr>    
    </table>
    </form>
    </div>

    <div class="simplebox">
    {tr}Wiki Watch{/tr}:<br />
    <form action="tiki-admin.php?page=wiki" method="post">
    <table class="admin">
    <tr><td class="form">{tr}Create watch for author on page creation{/tr}:</td><td><input type="checkbox" name="wiki_watch_author" {if $wiki_watch_author eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Enable watches on comments{/tr}:</td><td><input type="checkbox" name="wiki_watch_comments" {if $wiki_watch_comments eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Enable watch events when I am the editor{/tr}:</td><td><input type="checkbox" name="wiki_watch_editor" {if $wiki_watch_editor eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td align="center" colspan="2"><input type="submit" name="wikisetwatch" value="{tr}Set{/tr}" /></td></tr>
    </table>
    </form>
    </div>

    </td></tr>
    </table>
</div>
</div>
