{* $Id$ *}
{title help="Menus" url="tiki-admin_menu_options.php?menuId=$menuId" admpage="general&amp;cookietab=3"}{tr}Admin Menu:{/tr} {$editable_menu_info.name}{/title}

<div class="navbar">
	{button href="tiki-admin_menus.php" _text="{tr}List menus{/tr}"}
	{button href="tiki-admin_menus.php?menuId=$menuId&cookietab=2" _text="{tr}Edit this menu{/tr}"}
	{button href="#export" _text="{tr}Export{/tr}"}
	{button href="#import" _text="{tr}Import{/tr}"}
</div>

{tabset name="mytiki_user_preference"}
{tab name="{tr}Manage menu{/tr} $menuId"}
<table>
	<tr>
		<td valign="top">
			<table class="normal">
				<tr>
					<td valign="top" class="odd" colspan="2">
						<h2>{tr}Edit menu options{/tr}</h2>
						<div style="text-align: right;">
							<a href="#" onclick="toggle('weburls');toggle('urltop');hide('show');show('hide');" id="show" style="display:block;">{tr}Show Quick Urls{/tr}</a>
						</div>
					</td>
					<td valign="top" class="even" id="urltop" style="display:none;">
						<h2>{tr}Some useful URLs{/tr}</h2>
						<div style="text-align: right;">
							<a href="#" onclick="toggle('weburls');toggle('urltop');hide('hide');show('show');" id="hide" style="display:none;">{tr}Hide Quick Urls{/tr}</a>
						</div>
					</td>
				</tr>
				<tr>
					<td valign="top" class="odd" colspan="2">
						<form action="tiki-admin_menu_options.php" method="post">
							<input type="hidden" name="optionId" value="{$optionId|escape}" />
							<input type="hidden" name="menuId" value="{$menuId|escape}" />
							<input type="hidden" name="offset" value="{$offset|escape}" />
							{if !empty($nbRecords)}<input type="hidden" name="nbRecords" value="{$nbRecords|escape}" />{/if}
							<table class="formcolor">
								<tr>
									<td>{tr}Name:{/tr}</td>
									<td colspan="3">
										<input id="menu_name" type="text" name="name" value="{$name|escape}" size="34" />
									</td>
								</tr>
								<tr>
									<td>{tr}URL:{/tr}</td>
									<td colspan="3">
										<input id="menu_url" type="text" name="url" value="{$url|escape}" size="34" />
										<br /><em>{tr}For wiki page, use ((PageName)).{/tr}</em>
									</td>
								</tr>
								<tr>
									<td>{tr}Sections:{/tr}</td>
									<td colspan="3">
										<input id="menu_section" type="text" name="section" value="{$section|escape}" size="34" /><br />
										<em>{tr}Separate multiple sections with a comma ( , ) for an AND or a vertical bar ( | ) for an OR.{/tr}</em>
									</td>
								</tr>
								<tr>
									<td>{tr}Permissions:{/tr}</td>
									<td colspan="3">
										<input id="menu_perm" type="text" name="perm" value="{$perm|escape}" size="34" /><br />
										<em>{tr}Separate multiple permissions with a comma ( , ) for an AND or a vertical bar ( | ) for an OR.{/tr}</em>
									</td>
								</tr>
								<tr>
									<td>{tr}Group:{/tr}</td>
									<td colspan="3">
										<select id="menu_groupname" name="groupname[]" size="4" multiple="multiple">
											<option value="">&nbsp;</option>
											{foreach key=k item=i from=$option_groups}<option value="{$k|escape}" {$i}>{$k|escape}</option>{/foreach}
										</select>
										{if $option_groups|@count ge '2'}
										{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}Use Ctrl+Click to select multiple options{/tr}<br />{tr}Selecting 2 groups means that the option will be seen if the user belongs to the 2 groups. If you want the 2 groups to see the option, create 2 options with one group each.{/tr}<br />{tr}If the url is ((PageName)), you do not need to put the groups, the option will be displayed only if the page can be displayed.{/tr}{/remarksbox}
										{/if}
									</td>
								</tr>
								{if $prefs.feature_userlevels eq 'y'}
								<tr>
									<td>{tr}Level:{/tr}</td>
									<td colspan="3">
										<select name="level">
											<option value="0"{if $level eq 0} selected="selected"{/if}>{tr}All{/tr}</option>
											{foreach key=levn item=lev from=$prefs.userlevels}<option value="{$levn}"{if $userlevel eq $levn} selected="selected"{/if}>{$lev}</option>{/foreach}
										</select>
									</td>
								</tr>
								{/if}
								<tr>
									<td>{tr}Type:{/tr}</td>
									<td>
										<select name="type">
											<option value="o" {if $type eq 'o'}selected="selected"{/if}>{tr}option{/tr}</option>
											<option value="s" {if $type eq 's'}selected="selected"{/if}>{tr}section level 0{/tr}</option>
											<option value='1' {if $type eq '1'}selected="selected"{/if}>{tr}section level 1{/tr}</option>
											<option value='2' {if $type eq '2'}selected="selected"{/if}>{tr}section level 2{/tr}</option>
											<option value='3' {if $type eq '3'}selected="selected"{/if}>{tr}section level 3{/tr}</option>
											<option value="r" {if $type eq 'r'}selected="selected"{/if}>{tr}sorted section level 0{/tr}</option>
											<option value="-" {if $type eq '-'}selected="selected"{/if}>{tr}separator{/tr}</option>
										</select>
									</td>
									<td>{tr}Position:{/tr}</td>
									<td>
										<input type="text" name="position" value="{$position|escape}" size="6" />
									</td>
								</tr>
								{if $prefs.menus_items_icons eq 'y'}
									<tr><td>{tr}Icon:{/tr}</td><td colspan="3"><input type="text" name="icon" value="{$icon|escape}" size="20" /></td></tr>
								{/if}
								<tr>
									<td>&nbsp;</td>
									<td colspan="3">
										<input type="submit" name="save" value="{tr}Save{/tr}" />
									</td>
								</tr>
							</table>
						</form>
					</td>
					<td valign="top" class="even" id="weburls" style="display:none;">
						<table>
							<tr>
								<td>{tr}Home:{/tr} </td>
								<td>
									<select name="wikilinks" onchange="setMenuCon(options[selectedIndex].value);return true;">
										<option value=",,,">{tr}Choose{/tr} ...</option>
										<option value="{$prefs.tikiIndex},{tr}Home Page{/tr}">{tr}Home Page{/tr}</option>
										{if $prefs.home_blog}<option value="{$prefs.home_blog|sefurl:blog},{tr}Home Blog{/tr},feature_blogs,tiki_p_view_blogs">{tr}Home Blog{/tr}</option>{/if}
										{if $prefs.home_gallery}<option value="tiki-browse_gallery.php?galleryId={$prefs.home_gallery},{tr}Home Image Gal{/tr},feature_galleries,tiki_p_view_image_gallery">{tr}Home Image Gallery{/tr}</option>{/if}
										{if $prefs.home_file_gallery}<option value="tiki-list_file_gallery?galleryId={$prefs.home_file_gallery},{tr}Home File Gal{/tr},feature_file_galleries,tiki_p_view_file_gallery">{tr}Home File Gallery{/tr}</option>{/if}]
									</select>
								</td>
							</tr>
							<tr>
								<td>{tr}General:{/tr} </td>
								<td>
									<select name="wikilinks" onchange="setMenuCon(options[selectedIndex].value);return true;">
										<option value=",,,">{tr}Choose{/tr} ...</option>
										{if $prefs.feature_stats eq 'y'}<option value="tiki-stats.php,{tr}Stats{/tr},feature_stats,tiki_p_view_stats">{tr}Stats{/tr}</option>{/if}
										{if $prefs.feature_categories eq 'y'}<option value="tiki-browse_categories.php,{tr}Categories{/tr},feature_categories,tiki_p_view_category">{tr}Categories{/tr}</option>{/if}
										{if $prefs.feature_userPreferences eq 'y'}<option value="tiki-user_preferences.php,{tr}User preferences{/tr}">{tr}User prefs{/tr}</option>{/if}
									</select>
								</td>
							</tr>
							{if $prefs.feature_wiki eq 'y'}
								<tr>
									<td>{tr}Wiki:{/tr} </td>
									<td>
										<select name="wikilinks" onchange="setMenuCon(options[selectedIndex].value);return true;">
											<option value=",,,">{tr}Choose{/tr} ...</option>
											<option value="tiki-index.php,{tr}Wiki Home{/tr},feature_wiki,tiki_p_view">{tr}Wiki Home{/tr}</option>
											<option value="tiki-lastchanges.php,{tr}Latest Changes{/tr},feature_lastChanges,tiki_p_view">{tr}Latest Changes{/tr}</option>
											<option value="tiki-wiki_rankings.php,{tr}Rankings{/tr},feature_wiki_rankings,tiki_p_view">{tr}Rankings{/tr}</option>
											<option value="tiki-listpages.php,{tr}List pages{/tr},feature_listPages,tiki_p_view">{tr}List pages{/tr}</option>
											<option value="tiki-index.php?page=SandBox,{tr}Sandbox{/tr},feature_sandbox,tiki_p_view">{tr}Sandbox{/tr}</option>
										</select>
									</td>
								</tr>
							{/if}
							
							{if $prefs.feature_galleries eq 'y'}
								<tr>
									<td>{tr}Images:{/tr} </td>
									<td>
										<select name="wikilinks" onchange="setMenuCon(options[selectedIndex].value);return true;">
											<option value=",,,">{tr}Choose{/tr} ...</option>
											<option value="tiki-galleries.php,{tr}List galleries{/tr},feature_galleries,tiki_p_view_image_gallery">{tr}List image galleries{/tr}</option>
											<option value="tiki-upload_image.php,{tr}Upload image{/tr},feature_galleries,tiki_p_upload_images">{tr}Upload{/tr}</option>
											<option value="tiki-galleries_rankings.php,{tr}Gallery Rankings{/tr},feature_gal_rankings,tiki_p_view_image_gallery">{tr}Rankings{/tr}</option>
											<option value="tiki-browse_gallery.php?galleryId=,{tr}Browse a gallery{/tr},feature_galleries,tiki_p_view_image_gallery">{tr}Browse a gallery{/tr}</option>
										</select>
									</td>
								</tr>
							{/if}
							
							{if $prefs.feature_articles eq 'y'}
								<tr>
									<td>{tr}Articles:{/tr} </td>
									<td>
										<select name="wikilinks" onchange="setMenuCon(options[selectedIndex].value);return true;">
											<option value=",,,">{tr}Choose{/tr} ...</option>
											<option value="tiki-view_articles.php,{tr}Articles{/tr},feature_articles,tiki_p_read_article">{tr}Articles home{/tr}</option>
											<option value="tiki-list_articles.php,{tr}All articles{/tr},feature_articles,tiki_p_read_article">{tr}List articles{/tr}</option>
											<option value="tiki-cms_rankings.php,{tr}Rankings{/tr},feature_cms_rankings,tiki_p_read_article">{tr}Rankings{/tr}</option>
											<option value="tiki-edit_submission.php,{tr}Submit{/tr},feature_submissions,tiki_p_submit_article">{tr}Submit{/tr}</option>
											<option value="tiki-list_submissions.php,{tr}Submissions{/tr},feature_submissions,tiki_p_approve_submission">{tr}Submissions{/tr}</option>
										</select>
									</td>
								</tr>
							{/if}
							
							{if $prefs.feature_blogs eq 'y'}
								<tr>
									<td>{tr}Blogs:{/tr} </td>
									<td>
										<select name="wikilinks" onchange="setMenuCon(options[selectedIndex].value);return true;">
											<option value=",,,">{tr}Choose{/tr} ...</option>
											<option value="tiki-list_blogs.php,{tr}List Blogs{/tr},feature_blogs,tiki_p_read_blog">{tr}List Blogs{/tr}</option>
											<option value="tiki-blog_rankings.php,{tr}Rankings{/tr},feature_blog_rankings,tiki_p_read_blog">{tr}Rankings{/tr}</option>
											<option value="tiki-edit_blog.php,{tr}Create Blog{/tr},feature_blogs,tiki_p_create_blogs">{tr}Create Blog{/tr}</option>
											<option value="tiki-blog_post.php,{tr}Post{/tr},feature_blogs,tiki_p_blog_post">{tr}Post{/tr}</option>
										</select>
									</td>
								</tr>
							{/if}
								
							{if $prefs.feature_file_galleries eq 'y'}
								<tr>
									<td>{tr}Files:{/tr} </td>
									<td>
										<select name="wikilinks" onchange="setMenuCon(options[selectedIndex].value);return true;">
											<option value=",,,">{tr}Choose{/tr} ...</option>
											<option value="tiki-list_file_gallery.php,{tr}File Galleries{/tr},feature_file_galleries,tiki_p_view_file_gallery">{tr}File Galleries{/tr}</option>
											<option value="tiki-upload_file.php,{tr}Upload file{/tr},feature_file_galleries,tiki_p_upload_files">{tr}Upload file{/tr}</option>
											<option value="tiki-file_galleries_rankings.php,{tr}Rankings{/tr},feature_file_galleries_rankings,tiki_p_view_file_gallery">{tr}Rankings{/tr}</option>
										</select>
									</td>
								</tr>
							{/if}

							{if $prefs.feature_forums eq 'y'}
								<tr>
									<td>{tr}Forums:{/tr} </td>
									<td>
										<select name="wikilinks" onchange="setMenuCon(options[selectedIndex].value);return true;">
											<option value=",,,">{tr}Choose{/tr} ...</option>
											<option value="tiki-forums.php,{tr}Forums{/tr},feature_forums,tiki_p_forum_read">{tr}Forums{/tr}</option>
											<option value="tiki-view_forum.php?forumId=,{tr}View a forum{/tr},feature_forums,tiki_p_forum_read">{tr}View a forum{/tr}</option>
											<option value="tiki-view_forum_thread.php?forumId=&amp;comments_parentId=,{tr}View a thread{/tr},feature_forums,tiki_p_forum_read">{tr}View a thread{/tr}</option>8
										</select>
									</td>
								</tr>
							{/if}
								
							{if $prefs.feature_faqs eq 'y'}
								<tr>
									<td>{tr}FAQs:{/tr} </td>
									<td>
										<select name="wikilinks" onchange="setMenuCon(options[selectedIndex].value);return true;">
											<option value=",,,">{tr}Choose{/tr} ...</option>
											<option value="tiki-list_faqs.php,{tr}FAQs{/tr},feature_faqs,tiki_p_view_faqs">{tr}FAQs{/tr}</option>
											<option value="tiki-view_faq.php?faqId=,{tr}View a FAQ{/tr},feature_faqs,tiki_p_view_faqs">{tr}View a FAQ{/tr}</option>
										</select>
									</td>
								</tr>
							{/if}

							{if $prefs.feature_quizzes eq 'y'}
								<tr>
									<td>{tr}Quizzes:{/tr} </td>
									<td>
										<select name="wikilinks" onchange="setMenuCon(options[selectedIndex].value);return true;">
											<option value=",,,">{tr}Choose{/tr} ...</option>
											<option value="tiki-list_quizzes.php,{tr}Quizzes{/tr},feature_quizzes">{tr}Quizzes{/tr}</option>
											<option value="tiki-take_quiz.php?quizId=,{tr}Take a quiz{/tr},feature_quizzes">{tr}Take a quiz{/tr}</option>
											<option value="tiki-quiz_stats.php,{tr}Quiz stats{/tr},feature_quizzes,tiki_p_view_quiz_stats">{tr}Quiz stats{/tr}</option>
											<option value="tiki-quiz_stats_quiz.php?quizId=,{tr}Stats for a Quiz{/tr},feature_quizzes,tiki_p_view_quiz_stats">{tr}Stats for a Quiz{/tr}</option>
										</select>
									</td>
								</tr>
							{/if}
						</table>
					</td>
				</tr>
			</table>
		</td>
		<td valign="top" rowspan="2">
			<h2>{tr}Preview menu{/tr}</h2>
			<div class="box">
				<div class="box-title">{$editable_menu_info.name|escape}</div>
				<div class="box-data">
					{include file='tiki-user_menu.tpl' menu_channels=$allchannels}
				</div>
			</div>
		</td>
	</tr>
	<tr>
		<td>
			<a name="options"></a>
			<h2>{tr}Menu options{/tr}</h2>
			{if $channels or ($find ne '')}
				{include file='find.tpl' find_show_num_rows='y'}
			{/if}
			
			<form method="get" action="tiki-admin_menu_options.php">
				<input type="hidden" name="find" value="{$find|escape}" />
				<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
				<input type="hidden" name="menuId" value="{$menuId}" />
				<input type="hidden" name="offset" value="{$offset}" />
				<table class="normal">
					{assign var=numbercol value=0}
					<tr>
						<th>
							{assign var=numbercol value=$numbercol+1}
							{if $channels}
								{select_all checkbox_names='checked[]'}
							{/if}
						</th>
						{assign var=numbercol value=$numbercol+1}
						<th>{self_link _sort_arg='sort_mode' _sort_field='optionId'}{tr}ID{/tr}{/self_link}</th>
						{assign var=numbercol value=$numbercol+1}
						<th>{self_link _sort_arg='sort_mode' _sort_field='position'}{tr}Position{/tr}{/self_link}</th>
						{assign var=numbercol value=$numbercol+1}
						<th>{self_link _sort_arg='sort_mode' _sort_field='name'}{tr}Name{/tr}{/self_link}</th>
						{assign var=numbercol value=$numbercol+1}
						<th>{self_link _sort_arg='sort_mode' _sort_field='type'}{tr}Type{/tr}{/self_link}</th>
						{if $prefs.feature_userlevels eq 'y'}
						{assign var=numbercol value=$numbercol+1}
							<th>{self_link _sort_arg='sort_mode' _sort_field='userlevel'}{tr}Level{/tr}{/self_link}</th>
						{/if}
						{assign var=numbercol value=$numbercol+1}
						<th>{tr}Action{/tr}</th>
					</tr>
					{cycle values="odd,even" print=false}
					{section name=user loop=$channels}
						<tr class="{cycle}">
							<td class="checkbox">
								<input type="checkbox" name="checked[]" value="{$channels[user].optionId|escape}"  {if $smarty.request.checked and in_array($channels[user].optionId,$smarty.request.checked)}checked="checked"{/if} />
							</td>
							<td class="id">{$channels[user].optionId}</td>
							<td class="id">{$channels[user].position}</td>
							<td class="text">
								<a href="tiki-admin_menu_options.php?menuId={$menuId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;optionId={$channels[user].optionId}&amp;maxRecords={$maxRecords}{if !empty($nbRecords)}&amp;nbRecords={$nbRecords}{/if}" title="{tr}Edit{/tr}">{if $prefs.menus_item_names_raw eq 'y'}{$channels[user].name|escape}{else}{$channels[user].name}{/if}</a>
								<div style="margin-left:10px;">
									{if $channels[user].url}
										{tr}URL:{/tr} <a href="{if $prefs.menus_item_names_raw eq 'n'}{$channels[user].url|escape}{else}{$channels[user].url}{/if}" class="link" target="_blank" title="{$channels[user].canonic|escape}">{$channels[user].canonic|truncate:40:' ...'|escape}</a>
									{/if}
									{if $channels[user].section}<br />{tr}Sections:{/tr} {$channels[user].section}{/if}
									{if $channels[user].perm}<br />{tr}Permissions:{/tr} {$channels[user].perm}{/if}
									{if $channels[user].groupname}<br />{tr}Groups:{/tr} {$channels[user].groupname|escape}{/if}
								</div>
							</td>
							<td class="text">{$channels[user].type_description}</td>
			
							{if $prefs.feature_userlevels eq 'y'}
								{assign var=it value=$channels[user].userlevel}
								<td>{$prefs.userlevels.$it}</td>
							{/if}
							
							<td class="action">
								<a class="link" href="tiki-admin_menu_options.php?menuId={$menuId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;optionId={$channels[user].optionId}&amp;maxRecords={$maxRecords}{if !empty($nbRecords)}&amp;nbRecords={$nbRecords}{/if}" title="{tr}Edit{/tr}">{icon _id='page_edit'}</a>
								{if !$smarty.section.user.first}
									<a class="link" href="tiki-admin_menu_options.php?menuId={$menuId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;up={$channels[user].optionId}&amp;maxRecords={$maxRecords}{if !empty($nbRecords)}&amp;nbRecords={$nbRecords}{/if}#options" title="{tr}switch with previous option{/tr}">{icon _id='resultset_up'}</a>
								{/if}
								{if !$smarty.section.user.last}
									<a class="link" href="tiki-admin_menu_options.php?menuId={$menuId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;down={$channels[user].optionId}&amp;maxRecords={$maxRecords}{if !empty($nbRecords)}&amp;nbRecords={$nbRecords}{/if}#options" title="{tr}switch with next option{/tr}">{icon _id='resultset_down'}</a>
								{/if}
								<a class="link" href="tiki-admin_menu_options.php?menuId={$menuId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].optionId}&amp;maxRecords={$maxRecords}{if !empty($nbRecords)}&amp;nbRecords={$nbRecords}{/if}" title="{tr}Delete{/tr}">{icon _id='cross' alt="{tr}Delete{/tr}"}</a>
							</td>
						</tr>
					{sectionelse}
						{norecords _colspan=$numbercol}
					{/section}
				</table>
				
				{if $channels}
					<div align="left">
						{tr}Perform action with checked:{/tr}
						<input type="image" name="delsel" src='pics/icons/cross.png' alt="{tr}Delete{/tr}" title="{tr}Delete{/tr}" />
					</div>
				{/if}
			</form>
			
			{pagination_links cant=$cant_pages step=$maxRecords offset=$offset}{/pagination_links}

		</td>
	</tr>
</table>
{/tab}

{tab name="{tr}Import/export menu{/tr}"}
<a name="export"></a>
<h2>{tr}Export CSV data{/tr}</h2>
<form action="tiki-admin_menu_options.php" method="post">
	<input type="hidden" name="menuId" value="{$menuId}" />
	<input type="submit" name="export" value="{tr}Export{/tr}" />
</form>

<br />
<a name="import"></a>
<h2>{tr}Import CSV data{/tr}</h2>
{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}To add new options to the menu set the optionId field to 0. To remove an option set the remove field to 'y'.{/tr}{/remarksbox}
<form action="tiki-admin_menu_options.php" method="post" enctype="multipart/form-data">
	<input type="hidden" name="menuId" value="{$menuId}" />
	{tr}File:{/tr} <input name="csvfile" type="file" />
	<input type="submit" name="import" value="{tr}Import{/tr}" />
</form>
{/tab}
{/tabset}
