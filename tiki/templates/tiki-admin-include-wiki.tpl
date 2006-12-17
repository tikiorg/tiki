
<div class="rbox" name="tip">
<div class="rbox-title" name="tip">{tr}Tip{/tr}</div>  
<div class="rbox-data" name="tip">{tr}To add/edit wiki pages easily, add the module quick_edit via "Modules" on the application menu, or{/tr} <a class="rbox-link" href="tiki-admin_modules.php">{tr}click here{/tr}</a>.</div>
</div>
<br />

<table class="admin">
<tr><td  valign="top">
    
  <div class="cbox">
    <div class="cbox-title">
    {tr}Dumps{/tr}
    </div>
    <div class="cbox-data">
    <a class="link" href="tiki-admin.php?page=wiki&amp;dump=1">{tr}Generate dump{/tr}</a><br />
    <a class="link" href="dump/{if $tikidomain}{$tikidomain}/{/if}new.tar">{tr}Download last dump{/tr}</a>
         
    <br /><br />
    <div class="heading">{tr}Create a Tag for the Current Wiki{/tr}</div>
    
    <form action="tiki-admin.php?page=wiki" method="post">
    {tr}Tag name{/tr}: <input  maxlength="20" size="10" type="text" name="tagname"/>
    <input type="submit" name="createtag" value="{tr}create{/tr}"/>
    </form>
    
    <br />
    <div class="heading">{tr}Restore the Wiki{/tr}</div>

    <form action="tiki-admin.php?page=wiki" method="post">
    {tr}Tag name{/tr}: <select name="tagname">
          {section name=sel loop=$tags}
          <option value="{$tags[sel]|escape}">{$tags[sel]}</option>
          {sectionelse}
          <option value=''></option>
          {/section}
          </select>
    <input type="submit" name="restoretag" value="{tr}restore{/tr}"/>          
    </form>
    
    <br />
    <div class="heading">{tr}Remove a Tag{/tr}</div>

    <form action="tiki-admin.php?page=wiki" method="post">
    {tr}Tag name{/tr}: <select name="tagname">
          {section name=sel loop=$tags}
          <option value="{$tags[sel]|escape}">{$tags[sel]}</option>
          {sectionelse}
          <option value=''></option>
          {/section}
          </select>
    <input type="submit" name="removetag" value="{tr}remove{/tr}"/>          
    </form>
    <br />
    </div>
  </div>
  
  <div class="cbox">
    <div class="cbox-title">
    {tr}Wiki Comments Settings{/tr}
    </div>
    <div class="cbox-data">
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
    <tr><td colspan="2" class="button"><input type="submit" name="wikiprefs" value="{tr}Change settings{/tr}" /></td></tr>
    </table>
    </form>
    </div>
    </div>
    
    
  <div class="cbox">
    <div class="cbox-title">
    {tr}Export Wiki Pages{/tr}
    </div>
    <div class="cbox-data">
    <form method="post" action="tiki-admin.php?page=wiki">
    <table class="admin">
    <tr><td align="center" colspan="2"><a class="link" href="tiki-export_wiki_pages.php">{tr}Export{/tr}</a></td></tr>
    </table>
    </form>
    </div>
  </div>
    
    {*next two probably don't belong here *}
    
  <div class="cbox">
    <div class="cbox-title">
    {tr}Wiki attachments{/tr}
    </div>
    <div class="cbox-data"> 
		<form method="post" action="tiki-admin.php?page=wiki">
		<table class="admin">
		<tr><td class="form">{tr}Wiki attachments{/tr}:</td><td><input type="checkbox" name="feature_wiki_attachments" {if $feature_wiki_attachments eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Use database to store files{/tr}:</td><td><input type="radio" name="w_use_db" value="y" {if $w_use_db eq 'y'}checked="checked"{/if} /></td></tr>
    <tr><td class="form">{tr}Use a directory to store files{/tr}:</td><td><input type="radio" name="w_use_db" value="n" {if $w_use_db eq 'n'}checked="checked"{/if} /></td></tr>
    <tr><td class="form">{tr}Path{/tr}:</td><td><input type="text" name="w_use_dir" value="{$w_use_dir}" /></td></tr>
		<tr><td colspan="2" class="button"><input type="submit" name="wikiattprefs" value="{tr}Change preferences{/tr}" /></td></tr>
		</table>
		</form>

    <a class="link" href="tiki-admin.php?page=wikiatt">{tr}Manage attachments{/tr}</a>
    </div>
	</div>

	<div class="cbox">
		<div class="cbox-title">
		{tr}Wiki Administration{/tr}
		</div>

    <div class="cbox-data">
    <a class="link" href="tiki-admin.php?page=wiki&amp;rmvunusedpic=1">{tr}Remove unused pictures{/tr}</a>
    </div>
  </div>

  <div class="cbox">
    <div class="cbox-title">
    {tr}Wiki Home Page{/tr}
    </div>
    <div class="cbox-data">
     <form method="post" action="tiki-admin.php?page=wiki">
    <table class="admin"><tr>
    <td class="form"><input type="text" name="wikiHomePage" value="{$wikiHomePage|escape}" />
    <input type="submit" name="setwikihome" value="{tr}set{/tr}" />
    </td>
    </tr></table>
    </form>
    </div>
  </div>
  
  <div class="cbox">
    <div class="cbox-title">
    {tr}Wiki Discussion{/tr}
    </div>
    <div class="cbox-data">
    <form method="post" action="tiki-admin.php?page=wiki">
    <table class="admin">
    <tr><td class="form">
    {tr}Discuss pages on forums{/tr}: </td><td>
    <input type="checkbox" name="feature_wiki_discuss" {if $feature_wiki_discuss eq 'y'}checked="checked"{/if}/> </td></tr>
    <tr><td class="form">{tr}Forum{/tr}:</td><td class="form">
	  <select name="wiki_forum_id">
    {section name=ix loop=$all_forums}
    <option value="{$all_forums[ix].forumId|escape}" {if $all_forums[ix].forumId eq $wiki_forum_id}selected="selected"{/if}>{$all_forums[ix].name}</option>
    {/section}
    </select>

    </td></tr>
    <tr><td colspan="2" class="button"><input type="submit" name="wikidiscussprefs" value="{tr}Change preferences{/tr}" />
    </td>
    </tr></table>
    </form>
    </div>
  </div>
  
  <div class="cbox">
    <div class="cbox-title">
    {tr}Wiki Link Format{/tr}
    </div>
    <div class="cbox-data">
    <form method="post" action="tiki-admin.php?page=wiki">
    {tr}Controls recognition of Wiki links using the two parenthesis Wiki link syntax <i>((page name))</i>.{/tr}<br />
    <select name="wiki_page_regex">
    <option value='complete' {if $wiki_page_regex eq 'complete'}selected="selected"{/if}>{tr}complete{/tr}</option>
    <option value='full' {if $wiki_page_regex eq 'full'}selected="selected"{/if}>{tr}latin{/tr}</option>
    <option value='strict' {if $wiki_page_regex eq 'strict'}selected="selected"{/if}>{tr}english{/tr}</option>
    </select><br />
		{tr}Page name display stripper: choose a character that will be used as a delimiter to strip a final part of the page name (only concerns display){/tr}<br />
		<input type="text" name="wiki_pagename_strip" value="{$wiki_pagename_strip}" />
    <input type="submit" name="setwikiregex" value="{tr}set{/tr}" />
    </form>
    </div>
  </div>
    
  <div class="cbox">
    <div class="cbox-title">
	<a href="tiki-listpages.php">{tr}Wiki Page List Configuration{/tr}</a>
	</div>
    <div class="cbox-data">
    <form method="post" action="tiki-admin.php?page=wiki">
    <table class="admin">
    	<tr>
    		<td class="form">{tr}Name{/tr}</td>
    		<td class="form">
    			<input type="checkbox" name="wiki_list_name" {if $wiki_list_name eq 'y'}checked="checked"{/if} />
    		</td>
    	</tr>
    	<tr>
			<td class="form">{tr}Name length{/tr}:</td>
			<td class="form">
				<input type="text" name="wiki_list_name_len" value="{$wiki_list_name_len}" size="3" />
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
   	 			<input type="submit" name="wikilistprefs" value="{tr}Change configuration{/tr}" />
    		</td>
    	</tr>
    </table>
    </form>
    </div>
  </div>
{if $feature_morcego eq 'y'}
  <div class="cbox">
    <div class="cbox-title">
    {tr}Wiki 3D Browser Configuration{/tr}
    </div>
    <div class="cbox-data">
    <form action="tiki-admin.php?page=wiki" method="post">
    <table class="admin">
      <tr>
        <td colspan="2" class="heading">General</td>
      </tr>
      <tr>
        <td class="form">{tr}Enable wiki 3D browser{/tr}:</td>
        <td><input type="checkbox" name="wiki_feature_3d" {if $wiki_feature_3d eq 'y'}checked="checked"{/if}/></td>
      </tr>
      <tr>
        <td class="form">{tr}Load page on navigation{/tr}: </td>
	<td><input type="checkbox" name="wiki_3d_autoload" value="true" {if $wiki_3d_missing_page_color eq 'true'}checked{/if} /></td>
      </tr>
      <tr>
        <td class="form">{tr}Browser width{/tr}: </td>
	<td><input type="text" name="wiki_3d_width" value="{$wiki_3d_width|escape}" size="3" /></td>
      </tr>
      <tr>
        <td class="form">{tr}Browser height{/tr}: </td>
	<td><input type="text" name="wiki_3d_height" value="{$wiki_3d_height|escape}" size="3" /></td>
      </tr>
      <tr>
        <td colspan="2" class="heading">Graph appearance</td>
      </tr>
      <tr>
        <td class="form">{tr}Navigation depth{/tr}: </td>
	<td><input type="text" name="wiki_3d_navigation_depth" value="{$wiki_3d_navigation_depth|escape}" size="2" /></td>
      </tr>
      <tr>
        <td class="form">{tr}Node size{/tr}: </td>
	<td><input type="text" name="wiki_3d_node_size" value="{$wiki_3d_node_size|default:"30"}" size="2" /></td>
      </tr>
      <tr>
        <td class="form">{tr}Text size{/tr}: </td>
	<td><input type="text" name="wiki_3d_text_size" value="{$wiki_3d_text_size|default:"40"}" size="3" /></td>
      </tr>
      <tr>
        <td class="form">{tr}Spring (connection) size{/tr}: </td>
	<td><input type="text" name="wiki_3d_spring_size" value="{$wiki_3d_spring_size|default:"100"}" size="3" /></td>
      </tr>
      <tr>
        <td class="form">{tr}Existing page node color{/tr}: </td>
	<td><input type="text" name="wiki_3d_existing_page_color" value="{$wiki_3d_existing_page_color|escape}" size="7" /></td>
      </tr>
      <tr>
        <td class="form">{tr}Missing page node color{/tr}: </td>
	<td><input type="text" name="wiki_3d_missing_page_color" value="{$wiki_3d_missing_page_color|escape}" size="7" /></td>
      </tr>
      <tr>
        <td colspan="2" class="heading">Camera settings</td>
     </tr>
      <tr>
        <td class="form">{tr}Camera distance adjusted relative to nearest node{/tr}: </td>
	<td><input type="checkbox" name="wiki_3d_adjust_camera" value="true" {if $wiki_3d_adjust_camera eq 'true'}checked{/if} /></td>
	</tr><tr>
        <td class="form">{tr}Load page on navigation{/tr}: </td>
	<td><input type="checkbox" name="wiki_3d_autoload" {if $wiki_3d_autoload eq 'true'}checked="checked"{/if} /></td>
      </tr>
      <tr>
        <td class="form">{tr}Camera distance{/tr}: </td>
	<td><input type="text" name="wiki_3d_camera_distance" value="{$wiki_3d_camera_distance|default:"200"}" size="3" /></td>
      </tr>
      <tr>
        <td class="form">{tr}Field of view{/tr}: </td>
	<td><input type="text" name="wiki_3d_fov" value="{$wiki_3d_fov|default:"250"}" size="3" /></td>
      </tr>
      <tr>
        <td class="form">{tr}Feed animation interval (milisecs){/tr}: </td>
	<td><input type="text" name="wiki_3d_feed_animation_interval" value="{$wiki_3d_feed_animation_interval|escape}" size="4" /></td>
      </tr>
      {* new fields *}
      <tr>
        <td colspan="2" class="heading">Physics engine</td>
     </tr>
      <tr>
        <td class="form">{tr}Friction constant{/tr}: </td>
	<td><input type="text" name="wiki_3d_friction_constant" value="{$wiki_3d_friction_constant|default:"0.4f"}" size="7" /></td>
      </tr>
      <tr>
        <td class="form">{tr}Elastic constant{/tr}: </td>
	<td><input type="text" name="wiki_3d_elastic_constant" value="{$wiki_3d_elastic_constant|default:"0.5f"}" size="7" /></td>
      </tr>
      <tr>
        <td class="form">{tr}Eletrostatic constant{/tr}: </td>
	<td><input type="text" name="wiki_3d_eletrostatic_constant" value="{$wiki_3d_eletrostatic_constant|default:"1000f"}" size="7" /></td>
      </tr>
      <tr>
        <td class="form">{tr}Node mass{/tr}: </td>
	<td><input type="text" name="wiki_3d_node_mass" value="{$wiki_3d_node_mass|default:"5"}" size="7" /></td>
      </tr>
      <tr>
        <td class="form">{tr}Node charge{/tr}: </td>
	<td><input type="text" name="wiki_3d_node_charge" value="{$wiki_3d_node_charge|default:"1"}" size="7" /></td>
      </tr>

      <tr>
        <td colspan="2" class="button"><input type="submit" name="wikiset3d" value="{tr}Change configuration{/tr}" /></td>
      </tr>    
    </table>
    </form>
    </div>
  </div>
{/if}

  </td>
  <td  valign="top">
    
  <div class="cbox">
    <div class="cbox-title">
    {tr}Wiki Features{/tr}
    </div>
    <div class="cbox-data">
    <form action="tiki-admin.php?page=wiki" method="post">
    <table class="admin">
    <tr><td class="form">{tr}Sandbox{/tr}:</td><td><input type="checkbox" name="feature_sandbox" {if $feature_sandbox eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Anonymous editors must input anti-bot code{/tr}:</td><td><input type="checkbox" name="feature_antibot" {if $feature_antibot eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Last changes{/tr}:</td><td><input type="checkbox" name="feature_lastChanges" {if $feature_lastChanges eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Dump{/tr}:</td><td><input type="checkbox" name="feature_dump" {if $feature_dump eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Export{/tr}:</td><td><input type="checkbox" name="feature_wiki_export" {if $feature_wiki_export eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Rating{/tr}:</td><td><input type="checkbox" name="feature_wiki_ratings" {if $feature_wiki_ratings eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}History{/tr}:</td><td><input type="checkbox" name="feature_history" {if $feature_history eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}View source{/tr}:</td><td><input type="checkbox" name="feature_source" {if $feature_source eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}List pages{/tr}:</td><td><input type="checkbox" name="feature_listPages" {if $feature_listPages eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Backlinks{/tr}:</td><td><input type="checkbox" name="feature_backlinks" {if $feature_backlinks eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Similar{/tr}:</td><td><input type="checkbox" name="feature_likePages" {if $feature_likePages eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Rankings{/tr}:</td><td><input type="checkbox" name="feature_wiki_rankings" {if $feature_wiki_rankings eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Undo{/tr}:</td><td><input type="checkbox" name="feature_wiki_undo" {if $feature_wiki_undo eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}MultiPrint{/tr}:</td><td><input type="checkbox" name="feature_wiki_multiprint" {if $feature_wiki_multiprint eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}PDF generation{/tr}:</td><td><input type="checkbox" name="feature_wiki_pdf" {if $feature_wiki_pdf eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Comments{/tr}:</td><td><input type="checkbox" name="feature_wiki_comments" {if $feature_wiki_comments eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Spellchecking{/tr}:</td><td>{if $lib_spellcheck eq 'y'}<input type="checkbox" name="wiki_spellcheck" {if $wiki_spellcheck eq 'y'}checked="checked"{/if}/>{else}{tr}Not Installed{/tr}{/if}</td></tr>
    <tr><td class="form">{tr}Use templates{/tr}:</td><td><input type="checkbox" name="feature_wiki_templates" {if $feature_wiki_templates eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Warn on edit conflict{/tr}:</td><td><input type="checkbox" name="feature_warn_on_edit" {if $feature_warn_on_edit eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Edit idle timeout{/tr}:</td><td class="form">
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
    <tr><td class="form">{tr}Show page ID{/tr}:</td><td><input type="checkbox" name="feature_wiki_pageid" {if $feature_wiki_pageid eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Cache wiki pages (global){/tr}:</td><td>
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
	<tr><td class="form">{tr}Individual cache{/tr}:</td><td><input type="checkbox" name="feature_wiki_icache" {if $feature_wiki_icache eq 'y'}checked="checked"{/if}/></td></tr>    
	
    <tr><td class="form">{tr}Footnotes{/tr}:</td><td><input type="checkbox" name="feature_wiki_footnotes" {if $feature_wiki_footnotes eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Users can lock pages (if perm){/tr}:</td><td><input type="checkbox" name="feature_wiki_usrlock" {if $feature_wiki_usrlock eq 'y'}checked="checked"{/if}/></td></tr>    
    <tr><td class="form">{tr}Use WikiWords{/tr}:</td><td><input type="checkbox" name="feature_wikiwords" {if $feature_wikiwords eq 'y'}checked="checked"{/if}/></td></tr>    
    <tr><td class="form">{tr}Accept dashes and underscores in WikiWords{/tr}:</td><td><input type="checkbox" name="feature_wikiwords_usedash" {if $feature_wikiwords_usedash eq 'y'}checked="checked"{/if}/></td></tr>    
    <tr><td class="form">{tr}Link plural WikiWords to their singular forms{/tr}:</td><td><input type="checkbox" name="feature_wiki_plurals" {if $feature_wiki_plurals eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Use Wiki paragraph formatting{/tr}:</td><td><input type="checkbox" name="feature_wiki_paragraph_formatting" {if $feature_wiki_paragraph_formatting eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Automonospaced text{/tr}:</td><td><input type="checkbox" name="feature_wiki_monosp" {if $feature_wiki_monosp eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Tables syntax{/tr}:</td><td>
    <select name="feature_wiki_tables">
    <option value="old" {if $feature_wiki_tables eq 'old'}selected="selected"{/if}>{tr}|| for rows{/tr}</option>
    <option value="new" {if $feature_wiki_tables eq 'new'}selected="selected"{/if}>{tr}\n for rows{/tr}</option>
    </select>
    </td></tr>
    <tr><td class="form">{tr}Uses Slideshow{/tr}:</td><td><input type="checkbox" name="wiki_uses_slides" {if $wiki_uses_slides eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Open page as structure{/tr}:</td><td><input type="checkbox" name="feature_wiki_open_as_structure" {if $feature_wiki_open_as_structure eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Use external link icons{/tr}:</td><td><input type="checkbox" name="feature_wiki_ext_icon" {if $feature_wiki_ext_icon eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}User's Page{/tr}:</td><td><input type="checkbox" name="feature_wiki_userpage" {if $feature_wiki_userpage eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}UserPage prefix{/tr}:</td><td><input type="text" name="feature_wiki_userpage_prefix" value="{$feature_wiki_userpage_prefix|default:'UserPage'}" size="12" /></td></tr>
    <tr><td class="form">{tr}Page creators are admin of their pages{/tr}:</td><td><input type="checkbox" name="wiki_creator_admin" {if $wiki_creator_admin eq 'y'}checked="checked"{/if}/></td></tr>    
    <tr><td class="form">{tr}Import HTML{/tr}:</td><td><input type="checkbox" name="feature_wiki_import_html" {if $feature_wiki_import_html eq 'y'}checked="checked"{/if}/></td></tr>    

    <tr><td class="form">{tr}List authors{/tr}:</td><td>
    <select name="wiki_authors_style">
    <option value="classic" {if $wiki_authors_style eq 'classic'}selected="selected"{/if}>{tr}as Creator &amp; Last Editor{/tr}</option>
    <option value="business" {if $wiki_authors_style eq 'business'}selected="selected"{/if}>{tr}Business style{/tr}</option>
    <option value="collaborative" {if $wiki_authors_style eq 'collaborative'}selected="selected"{/if}>{tr}Collaborative style{/tr}</option>
    <option value="none" {if $wiki_authors_style eq 'none'}selected="selected"{/if}>{tr}no (disabled){/tr}</option>
    </select> 
    </td></tr>
    <tr><td class="form">{tr}Protect email against spam{/tr}</td><td><input type="checkbox" name="feature_wiki_protect_email" {if $feature_wiki_protect_email eq 'y'}checked="checked"{/if}/></td></tr> 
    <tr><td class="form">{tr}When viewing a page, if it doesn't exist and has one like page, automatic redirection to this like page{/tr}</td><td><input type="checkbox" name="feature_wiki_1like_redirection" {if $feature_wiki_1like_redirection eq 'y'}checked="checked"{/if}/></td></tr>
     <tr><td class="form">{tr}Show/hide heading icon displayed before the heading{/tr}</td><td><input type="checkbox" name="feature_wiki_show_hide_before" {if $feature_wiki_show_hide_before eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Mandatory category in the category tree{/tr}</td>
    <td class="form"><select name="feature_wiki_mandatory_category">
	<option value="-1" {if $feature_wiki_mandatory_category eq -1 or $feature_wiki_mandatory_category eq ''}selected="selected"{/if}>{tr}None{/tr}</option>
	<option value="0" {if $feature_wiki_mandatory_category eq 0}selected="selected"{/if}>{tr}All{/tr}</option>
	{section name=ix loop=$catree}
	<option value="{$catree[ix].categId|escape}" {if $catree[ix].categId eq $feature_wiki_mandatory_category}selected="selected"{/if}>{if $catree[ix].categpath}{$catree[ix].categpath}{else}{$catree[ix].name}{/if}</option>
	{/section}
	</select>
</td></tr>
    <tr><td class="form">{tr}Print Page{/tr}:</td><td><input type="checkbox" name="feature_wiki_print" {if $feature_wiki_print eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Search and replace{/tr}:</td><td><input type="checkbox" name="feature_wiki_replace" {if $feature_wiki_replace eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Allow HTML{/tr}:</td><td><input type="checkbox" name="feature_wiki_allowhtml" {if $feature_wiki_allowhtml eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td colspan="2" class="button"><input type="submit" name="wikifeatures" value="{tr}Set features{/tr}" /></td></tr>
    </table>
    </form>
    </div>
  </div>
    
  <div class="cbox">
    <div class="cbox-title">
    {tr}Wiki History{/tr}
    </div>
    <div class="cbox-data">
    <form action="tiki-admin.php?page=wiki" method="post">
    <table class="admin">
    <tr><td class="form">{tr}Maximum number of versions for history{/tr}: </td><td><input size="5" type="text" name="maxVersions" value="{$maxVersions|escape}" /> (0={tr}unlimited{/tr})</td></tr>
    <tr><td class="form">{tr}Never delete versions younger than days{/tr}: </td><td><input size="5" type="text" name="keep_versions" value="{$keep_versions|escape}" /></td></tr>
       <tr><td class="form">{tr}IP not displayed in history{/tr}:</td><td>
	<input type="checkbox" name="feature_wiki_history_ip" {if $feature_wiki_history_ip eq 'n'}checked="checked"{/if}/>
    </td></tr>
     <tr><td class="form">{tr}Diff style{/tr}: </td><td><select name="default_wiki_diff_style">
       <option value="old" {if $default_wiki_diff_style eq 'old'}selected="selected"{/if}>{tr}Only with last version{/tr}</option>
       <option value="minsidediff" {if $default_wiki_diff_style ne 'old'}selected="selected"{/if}>{tr}Any 2 versions{/tr}</option>
    </select></td></tr>
	<tr><td class="form">{tr}History only for data, description, comment change{/tr}</td><td><input type="checkbox" name="feature_wiki_history_full" {if $feature_wiki_history_full eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td colspan="2" class="button"><input type="submit" name="wikisetprefs" value="{tr}Change preferences{/tr}" /></td></tr>
    </table>
    </form>
    </div>
  </div>

  <div class="cbox">
    <div class="cbox-title">
    {tr}Copyright Management{/tr}
    </div>
    <div class="cbox-data">
    <form action="tiki-admin.php?page=wiki" method="post">
    <table class="admin">
    <tr><td class="form">{tr}Enable Feature{/tr}:</td><td><input type="checkbox" name="wiki_feature_copyrights" {if $wiki_feature_copyrights eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}License Page{/tr}: </td><td><input type="text" name="wikiLicensePage" value="{$wikiLicensePage|escape}" /></td></tr>
    <tr><td class="form">{tr}Submit Notice{/tr}: </td><td><input type="text" name="wikiSubmitNotice" value="{$wikiSubmitNotice|escape}" /></td></tr>
    <tr><td colspan="2" class="button"><input type="submit" name="wikisetcopyright" value="{tr}Change preferences{/tr}" /></td></tr>    
    </table>
    </form>
    </div>
  </div>

  <div class="cbox">
    <div class="cbox-title">
    {tr}Wiki Watch{/tr}
    </div>
    <div class="cbox-data">
    <form action="tiki-admin.php?page=wiki" method="post">
    <table class="admin">
    <tr><td class="form">{tr}Create watch for author on page creation{/tr}:</td><td><input type="checkbox" name="wiki_watch_author" {if $wiki_watch_author eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Enable watch events when I am the editor{/tr}:</td><td><input type="checkbox" name="wiki_watch_editor" {if $wiki_watch_editor eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Enable watches on comments{/tr}:</td><td><input type="checkbox" name="wiki_watch_comments" {if $wiki_watch_comments eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td colspan="2" class="button"><input type="submit" name="wikisetwatch" value="{tr}Change preferences{/tr}" /></td></tr>
    </table>
    </form>
    </div>
  </div>

  </td></tr>
  </table>
