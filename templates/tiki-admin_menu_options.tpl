{* $Id$ *}
{title help="Menus" url="tiki-admin_menu_options.php?menuId=$menuId" admpage="general&amp;cookietab=3"}{tr}Menu{/tr}: {$editable_menu_info.name}{/title}

<div class="t_navbar margin-bottom-md">
	<a class="btn btn-link" href="tiki-admin_menus.php">
		{icon name="list"} {tr}List Menus{/tr}
	</a>
	{if $tiki_p_edit_menu eq 'y'}
		<a class="btn btn-default" href="{bootstrap_modal controller=menu action=manage_menu menuId=$menuId}">
			{icon name="edit"} {tr}Edit This Menu{/tr}
		</a>
		<a class="btn btn-default" href="{bootstrap_modal controller=menu action=export_menu_options menuId=$menuId}"
		   title="{tr}Export menu options{/tr}">
			{icon name="export"} {tr}Export{/tr}
		</a>
		<a class="btn btn-default no-ajax"
		   href="{bootstrap_modal controller=menu action=import_menu_options menuId=$menuId}"
		   title="{tr}Import menu options{/tr}">
			{icon name="import"} {tr}Import{/tr}
		</a>
	{/if}
</div>

{tabset name="admin_menu_options"}
{tab name="{tr}Manage menu{/tr} {$editable_menu_info.name}"}
	<div>
		<a id="options"></a>

		<h2>{tr}Menu options{/tr} <span class="badge">{$cant_pages}</span></h2>
		{if $channels or ($find ne '')}
			{include file='find.tpl' find_show_num_rows='y'}
		{/if}

		{* Use css menus as fallback for item dropdown action menu if javascript is not being used *}
		{if $prefs.javascript_enabled !== 'y'}
			{$js = 'n'}
			{$libeg = '<li>'}
			{$liend = '</li>'}
		{else}
			{$js = 'y'}
			{$libeg = ''}
			{$liend = ''}
		{/if}
		<form method="get" action="tiki-admin_menu_options.php">
			<input type="hidden" name="find" value="{$find|escape}">
			<input type="hidden" name="sort_mode" value="{$sort_mode|escape}">
			<input type="hidden" name="menuId" value="{$menuId}">
			<input type="hidden" name="offset" value="{$offset}">

			<div class="{if $js === 'y'}table-responsive{/if}"> {* table-responsive class cuts off css drop-down menus *}
				<table class="table table-striped table-hover">
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
						<th></th>
					</tr>

					{section name=user loop=$channels}
						<tr>
							<td class="checkbox-cell">
								<input type="checkbox" name="checked[]" value="{$channels[user].optionId|escape}"
									   {if $smarty.request.checked and in_array($channels[user].optionId,$smarty.request.checked)}checked="checked"{/if}>
							</td>
							<td class="id">{$channels[user].optionId}</td>
							<td class="id">{$channels[user].position}</td>
							<td class="text">
								<a
									href="tiki-admin_menu_options.php?menuId={$menuId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;optionId={$channels[user].optionId}&amp;maxRecords={$maxRecords}{if !empty($nbRecords)}&amp;nbRecords={$nbRecords}{/if}"
								   class="tips"
								   title=":{tr}Edit{/tr}">{$channels[user].name|escape}</a>
											<span class="help-block">
												{if $channels[user].url}
													{tr}URL:{/tr}
													<a href="{$channels[user].sefurl|escape}"
													   class="link tips" target="_blank"
													   title=":{$channels[user].canonic|escape}">{$channels[user].canonic|truncate:40:' ...'|escape}</a>
												{/if}
												{if $channels[user].section}
													<br>
													{tr}Sections:{/tr} {$channels[user].section}{/if}
												{if $channels[user].perm}
													<br>
													{tr}Permissions:{/tr} {$channels[user].perm}{/if}
												{if $channels[user].groupname}
													<br>
													{tr}Groups:{/tr} {$channels[user].groupname|escape}{/if}
												{if $channels[user].groupname}
													<br>
													{tr}Class:{/tr} {$channels[user].class|escape}{/if}
											</span>
							</td>
							<td class="text">{$channels[user].type_description}</td>

							{if $prefs.feature_userlevels eq 'y'}
								{assign var=it value=$channels[user].userlevel}
								<td>{$prefs.userlevels.$it}</td>
							{/if}

							<td class="action">
								{capture name=menu_options_actions}
									{strip}
										{if !$smarty.section.user.first}
											{$libeg}<a href="tiki-admin_menu_options.php?menuId={$menuId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;up={$channels[user].optionId}&amp;maxRecords={$maxRecords}{if !empty($nbRecords)}&amp;nbRecords={$nbRecords}{/if}#options">
												{icon name="up" _menu_text='y' _menu_icon='y' alt="{tr}Switch with previous option{/tr}"}
											</a>{$liend}
										{/if}
										{if !$smarty.section.user.last}
											{$libeg}<a href="tiki-admin_menu_options.php?menuId={$menuId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;down={$channels[user].optionId}&amp;maxRecords={$maxRecords}{if !empty($nbRecords)}&amp;nbRecords={$nbRecords}{/if}#options">
												{icon name="down" _menu_text='y' _menu_icon='y' alt="{tr}Switch with next option{/tr}"}
											</a>{$liend}
										{/if}
										{$libeg}<a href="tiki-admin_menu_options.php?menuId={$menuId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;optionId={$channels[user].optionId}&amp;maxRecords={$maxRecords}{if !empty($nbRecords)}&amp;nbRecords={$nbRecords}{/if}#contentadmin_menu_options-2">
											{icon name='edit' _menu_text='y' _menu_icon='y' alt="{tr}Edit{/tr}"}
										</a>{$liend}
										{$libeg}<a href="tiki-admin_menu_options.php?menuId={$menuId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].optionId}&amp;maxRecords={$maxRecords}{if !empty($nbRecords)}&amp;nbRecords={$nbRecords}{/if}">
											{icon name='remove' _menu_text='y' _menu_icon='y' alt="{tr}Remove{/tr}"}
										</a>{$liend}
									{/strip}
								{/capture}
								{if $js === 'n'}<ul class="cssmenu_horiz"><li>{/if}
								<a
									class="tips"
									title="{tr}Actions{/tr}"
									href="#"
									{if $js === 'y'}{popup fullhtml="1" center=true text=$smarty.capture.menu_options_actions|escape:"javascript"|escape:"html"}{/if}
									style="padding:0; margin:0; border:0"
								>
									{icon name='wrench'}
								</a>
								{if $js === 'n'}
									<ul class="dropdown-menu" role="menu">{$smarty.capture.menu_options_actions}</ul></li></ul>
								{/if}
							</td>
						</tr>
						{sectionelse}
						{norecords _colspan=$numbercol}
					{/section}
				</table>
			</div>

			{if $channels}
				<div align="left">
					{tr}Perform action with checked:{/tr}
					<input type="image" name="delsel" src='img/icons/cross.png' alt="{tr}Delete{/tr}"
						   title="{tr}Delete{/tr}">
				</div>
			{/if}
		</form>

		{pagination_links cant=$cant_pages step=$maxRecords offset=$offset}{/pagination_links}


	</div>
{/tab}

{if empty($optionId)}
	{$editname = "{tr}Create menu option{/tr}"}
{else}
	{$editname = "{tr}Edit menu option{/tr}"}
{/if}
{tab name=$editname}
	<div>
		<h2>{$editname}</h2>

		<div style="text-align:right;position:relative;">
			<div id="weburlslink">
				<a href="#" onclick="flip('weburls');return false;">{tr}Show Quick URLs{/tr}</a>
			</div>
			<div id="weburls" style="display:none;position:absolute;right:-10px;top:-50px;z-index:1;"
				 class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title" id="urltop">{tr}Some useful URLs{/tr}</h3>
					<div style="text-align: right;">
						<a href="#" class="hide_weburls" style="color:inherit; font-size: 85%" onclick="flip('weburls');return false;">{tr}Hide Quick URLs{/tr}</a>
					</div>
				</div>
				<div class="panel-body">
					<div class="form-horizontal">
						<div class="form-group">
							<label class="control-label col-md-5" for="wikilinks1">{tr}Home:{/tr}</label>

							<div class="col-md-7">
								<select class="form-control" name="wikilinks" id="wikilinks1"
										onchange="setMenuCon(options[selectedIndex].value);return true;">
									<option value=",,,">{tr}Choose{/tr} ...</option>
									<option value="{$prefs.tikiIndex},{tr}Home Page{/tr}">{tr}Home Page{/tr}</option>
									{if $prefs.home_blog}
										<option
										value="{$prefs.home_blog|sefurl:blog},{tr}Home Blog{/tr},feature_blogs,tiki_p_view_blogs">{tr}Home Blog{/tr}</option>{/if}
									{if $prefs.home_gallery}
										<option
										value="tiki-browse_gallery.php?galleryId={$prefs.home_gallery},{tr}Home Image Gal{/tr},feature_galleries,tiki_p_view_image_gallery">{tr}Home Image Gallery{/tr}</option>{/if}
									{if $prefs.home_file_gallery}
									<option
									value="tiki-list_file_gallery?galleryId={$prefs.home_file_gallery},{tr}Home File Gal{/tr},feature_file_galleries,tiki_p_view_file_gallery">{tr}Home File Gallery{/tr}</option>{/if}
									]
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-5" for="wikilinks2">{tr}General:{/tr} </label>

							<div class="col-md-7">
								<select class="form-control" name="wikilinks" id="wikilinks2"
										onchange="setMenuCon(options[selectedIndex].value);return true;">
									<option value=",,,">{tr}Choose{/tr} ...</option>
									{if $prefs.feature_stats eq 'y'}
										<option
										value="tiki-stats.php,{tr}Stats{/tr},feature_stats,tiki_p_view_stats">{tr}Stats{/tr}</option>{/if}
									{if $prefs.feature_categories eq 'y'}
										<option
										value="tiki-browse_categories.php,{tr}Categories{/tr},feature_categories,tiki_p_view_category">{tr}Categories{/tr}</option>{/if}
									{if $prefs.feature_userPreferences eq 'y'}
										<option
										value="tiki-user_preferences.php,{tr}User preferences{/tr}">{tr}User prefs{/tr}</option>{/if}
								</select>
							</div>
						</div>
						{if $prefs.feature_wiki eq 'y'}
							<div class="form-group">
								<label class="control-label col-md-5" for="wikilinks3">{tr}Wiki:{/tr} </label>

								<div class="col-md-7">
									<select class="form-control" name="wikilinks" id="wikilinks3"
											onchange="setMenuCon(options[selectedIndex].value);return true;">
										<option value=",,,">{tr}Choose{/tr} ...</option>
										<option value="tiki-index.php,{tr}Wiki Home{/tr},feature_wiki,tiki_p_view">{tr}Wiki Home{/tr}</option>
										<option value="tiki-lastchanges.php,{tr}Latest Changes{/tr},feature_lastChanges,tiki_p_view">{tr}Latest Changes{/tr}</option>
										<option value="tiki-wiki_rankings.php,{tr}Rankings{/tr},feature_wiki_rankings,tiki_p_view">{tr}Rankings{/tr}</option>
										<option value="tiki-listpages.php,{tr}List pages{/tr},feature_listPages,tiki_p_view">{tr}List pages{/tr}</option>
										<option value="tiki-index.php?page=SandBox,{tr}Sandbox{/tr},feature_sandbox,tiki_p_view">{tr}Sandbox{/tr}</option>
									</select>
								</div>
							</div>
						{/if}

						{if $prefs.feature_galleries eq 'y'}
							<div class="form-group">
								<label class="control-label col-md-5" for="wikilinks4">{tr}Images:{/tr} </label>

								<div class="col-md-7">
									<select name="wikilinks" id="wikilinks4" class="form-control"
											onchange="setMenuCon(options[selectedIndex].value);return true;">
										<option value=",,,">{tr}Choose{/tr} ...</option>
										<option value="tiki-galleries.php,{tr}List galleries{/tr},feature_galleries,tiki_p_view_image_gallery">{tr}List image galleries{/tr}</option>
										<option value="tiki-upload_image.php,{tr}Upload image{/tr},feature_galleries,tiki_p_upload_images">{tr}Upload{/tr}</option>
										<option value="tiki-galleries_rankings.php,{tr}Gallery Rankings{/tr},feature_gal_rankings,tiki_p_view_image_gallery">{tr}Rankings{/tr}</option>
										<option value="tiki-browse_gallery.php?galleryId=,{tr}Browse a gallery{/tr},feature_galleries,tiki_p_view_image_gallery">{tr}Browse a gallery{/tr}</option>
									</select>
								</div>
							</div>
						{/if}

						{if $prefs.feature_articles eq 'y'}
							<div class="form-group">
								<label class="control-label col-md-5" for="wikilinks5">{tr}Articles:{/tr} </label>

								<div class="col-md-7">
									<select name="wikilinks" id="wikilinks5" class="form-control"
											onchange="setMenuCon(options[selectedIndex].value);return true;">
										<option value=",,,">{tr}Choose{/tr} ...</option>
										<option value="tiki-view_articles.php,{tr}Articles{/tr},feature_articles,tiki_p_read_article">{tr}Articles home{/tr}</option>
										<option value="tiki-list_articles.php,{tr}All articles{/tr},feature_articles,tiki_p_read_article">{tr}List articles{/tr}</option>
										<option value="tiki-cms_rankings.php,{tr}Rankings{/tr},feature_cms_rankings,tiki_p_read_article">{tr}Rankings{/tr}</option>
										<option value="tiki-edit_submission.php,{tr}Submit{/tr},feature_submissions,tiki_p_submit_article">{tr}Submit{/tr}</option>
										<option value="tiki-list_submissions.php,{tr}Submissions{/tr},feature_submissions,tiki_p_approve_submission">{tr}Submissions{/tr}</option>
									</select>
								</div>
							</div>
						{/if}

						{if $prefs.feature_blogs eq 'y'}
							<div class="form-group">
								<label class="control-label col-md-5" for="wikilinks6">{tr}Blogs:{/tr}</label>

								<div class="col-md-7">
									<select name="wikilinks" id="wikilinks6" class="form-control"
											onchange="setMenuCon(options[selectedIndex].value);return true;">
										<option value=",,,">{tr}Choose{/tr} ...</option>
										<option value="tiki-list_blogs.php,{tr}List Blogs{/tr},feature_blogs,tiki_p_read_blog">{tr}List Blogs{/tr}</option>
										<option value="tiki-blog_rankings.php,{tr}Rankings{/tr},feature_blog_rankings,tiki_p_read_blog">{tr}Rankings{/tr}</option>
										<option value="tiki-edit_blog.php,{tr}Create Blog{/tr},feature_blogs,tiki_p_create_blogs">{tr}Create Blog{/tr}</option>
										<option value="tiki-blog_post.php,{tr}Post{/tr},feature_blogs,tiki_p_blog_post">{tr}Post{/tr}</option>
									</select>
								</div>
							</div>
						{/if}

						{if $prefs.feature_file_galleries eq 'y'}
							<div class="form-group">
								<label class="control-label col-md-5" for="wikilinks7">{tr}Files:{/tr} </label>

								<div class="col-md-7">
									<select id="wikilinks7" class="form-control" name="wikilinks"
											onchange="setMenuCon(options[selectedIndex].value);return true;">
										<option value=",,,">{tr}Choose{/tr} ...</option>
										<option value="tiki-list_file_gallery.php,{tr}File Galleries{/tr},feature_file_galleries,tiki_p_view_file_gallery">{tr}File Galleries{/tr}</option>
										<option value="tiki-upload_file.php,{tr}Upload file{/tr},feature_file_galleries,tiki_p_upload_files">{tr}Upload file{/tr}</option>
										<option value="tiki-file_galleries_rankings.php,{tr}Rankings{/tr},feature_file_galleries_rankings,tiki_p_view_file_gallery">{tr}Rankings{/tr}</option>
									</select>
								</div>
							</div>
						{/if}

						{if $prefs.feature_forums eq 'y'}
							<div class="form-group">
								<label class="control-label col-md-5" for="wikilinks8">{tr}Forums:{/tr} </label>

								<div class="col-md-7">
									<select name="wikilinks" id="wikilinks8" class="form-control"
											onchange="setMenuCon(options[selectedIndex].value);return true;">
										<option value=",,,">{tr}Choose{/tr} ...</option>
										<option value="tiki-forums.php,{tr}Forums{/tr},feature_forums,tiki_p_forum_read">{tr}Forums{/tr}</option>
										<option value="tiki-view_forum.php?forumId=,{tr}View a forum{/tr},feature_forums,tiki_p_forum_read">{tr}View a forum{/tr}</option>
										<option value="tiki-view_forum_thread.php?forumId=&amp;comments_parentId=,{tr}View a thread{/tr},feature_forums,tiki_p_forum_read">{tr}View a thread{/tr}</option>
										8
									</select>
								</div>
							</div>
						{/if}

						{if $prefs.feature_faqs eq 'y'}
							<div class="form-group">
								<label class="control-label col-md-5" for="wikilinks9">{tr}FAQs:{/tr} </label>

								<div class="col-md-7">
									<select name="wikilinks" id="wikilinks9" class="form-control"
											onchange="setMenuCon(options[selectedIndex].value);return true;">
										<option value=",,,">{tr}Choose{/tr} ...</option>
										<option value="tiki-list_faqs.php,{tr}FAQs{/tr},feature_faqs,tiki_p_view_faqs">{tr}FAQs{/tr}</option>
										<option value="tiki-view_faq.php?faqId=,{tr}View a FAQ{/tr},feature_faqs,tiki_p_view_faqs">{tr}View a FAQ{/tr}</option>
									</select>
								</div>
							</div>
						{/if}

						{if $prefs.feature_quizzes eq 'y'}
							<div class="form-group">
								<label class="control-label col-md-5" for="wikilinks10">{tr}Quizzes:{/tr} </label>

								<div class="col-md-7">
									<select name="wikilinks" id="wikilinks10" class="form-control"
											onchange="setMenuCon(options[selectedIndex].value);return true;">
										<option value=",,,">{tr}Choose{/tr} ...</option>
										<option value="tiki-list_quizzes.php,{tr}Quizzes{/tr},feature_quizzes">{tr}Quizzes{/tr}</option>
										<option value="tiki-take_quiz.php?quizId=,{tr}Take a quiz{/tr},feature_quizzes">{tr}Take a quiz{/tr}</option>
										<option value="tiki-quiz_stats.php,{tr}Quiz stats{/tr},feature_quizzes,tiki_p_view_quiz_stats">{tr}Quiz stats{/tr}</option>
										<option value="tiki-quiz_stats_quiz.php?quizId=,{tr}Stats for a Quiz{/tr},feature_quizzes,tiki_p_view_quiz_stats">{tr}Stats for a Quiz{/tr}</option>
									</select>
								</div>
							</div>
						{/if}

					</div>
				</div>
			</div>
		</div>

		{*<tr>
			<td valign="top" class="odd">*}
		<form action="tiki-admin_menu_options.php" method="post">
			<input type="hidden" name="optionId" value="{$optionId|escape}">
			<input type="hidden" name="menuId" value="{$menuId|escape}">
			<input type="hidden" name="offset" value="{$offset|escape}">
			{if !empty($nbRecords)}<input type="hidden" name="nbRecords"
										  value="{$nbRecords|escape}">{/if}
			<div class="form form-horizontal">
				<div class="form-group">
					<label class="control-label col-md-3" for="menu_name">{tr}Name:{/tr}</label>

					<div class="col-md-9">
						<input id="menu_name" class="form-control" type="text" name="name" value="{$name|escape}">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-3" for="menu_url">{tr}URL:{/tr}</label>

					<div class="col-md-9">
						{capture name='options'}select:function(event,ui){ldelim}ui.item.value='(('+ui.item.value+'))';{rdelim}{/capture}
						{autocomplete element="#menu_url" type='pagename' options=$smarty.capture.options}
						<input id="menu_url" type="text" name="url" value="{$url|escape}" class="form-control">

						<div class="help-block">{tr}For wiki page, use ((PageName)).{/tr}</div>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-3" for="type">{tr}Type:{/tr}</label>

					<div class="col-md-9">
						<select name="type" class="form-control">
							<option value="o" {if $type eq 'o'}selected="selected"{/if}>{tr}option{/tr}</option>
							<option value="s"
									{if $type eq 's'}selected="selected"{/if}>{tr}section level 0{/tr}</option>
							<option value='1'
									{if $type eq '1'}selected="selected"{/if}>{tr}section level 1{/tr}</option>
							<option value='2'
									{if $type eq '2'}selected="selected"{/if}>{tr}section level 2{/tr}</option>
							<option value='3'
									{if $type eq '3'}selected="selected"{/if}>{tr}section level 3{/tr}</option>
							<option value="r"
									{if $type eq 'r'}selected="selected"{/if}>{tr}sorted section level 0{/tr}</option>
							<option value="-" {if $type eq '-'}selected="selected"{/if}>{tr}separator{/tr}</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-3" for="position">{tr}Position:{/tr}</label>

					<div class="col-md-9">
						<input type="text" name="position" id="position" value="{$position|escape}"
							   class="form-control">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-3" for="menu_groupname">{tr}Group:{/tr}</label>

					<div class="col-md-9">
						<select id="menu_groupname" name="groupname[]" class="form-control margin-bottom-md" multiple="multiple">
							<option value="">&nbsp;</option>
							{foreach key=k item=i from=$option_groups}
								<option value="{$k|escape}" {$i}>{$k|escape}</option>{/foreach}
						</select>
						{if $option_groups|@count ge '2'}
							{if $prefs.jquery_ui_chosen neq 'y'}{$ctrlMsg="{tr}Use Ctrl+Click to select multiple options{/tr}<br>"}{/if}
							{remarksbox type="tip" title="{tr}Tip{/tr}"}{$ctrlMsg}{tr}Selecting 2 groups means that the option will be seen if the user belongs to the 2 groups. If you want the 2 groups to see the option, create 2 options with one group each.{/tr}
								<br>
							{tr}If the url is ((PageName)), you do not need to put the groups, the option will be displayed only if the page can be displayed.{/tr}{/remarksbox}
						{/if}
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-3" for="menu_section">{tr}Sections:{/tr}</label>

					<div class="col-md-9">
						<input id="menu_section" type="text" name="section" value="{$section|escape}"
							   class="form-control"><br>
						{autocomplete element="#menu_section" type="array" options="source:prefNames,multiple:true,multipleSeparator:','"}{* note, multiple doesn't work in jquery-ui 1.8 *}
						<div class="help-block">{tr}Separate multiple feature/preferences with a comma ( , ) for an AND or a vertical bar ( | ) for an OR.{/tr}</div>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-3" for="menu_perm">{tr}Permissions:{/tr}</label>

					<div class="col-md-9">
						<input id="menu_perm" type="text" name="perm" value="{$perm|escape}" class="form-control"><br>
						{autocomplete element="#menu_perm" type="array" options="source:permNames,multiple:true,multipleSeparator:','"}{* note, multiple doesn't work in jquery-ui 1.8 *}
						<div class="help-block">{tr}Separate multiple permissions with a comma ( , ) for an AND or a vertical bar ( | ) for an OR.{/tr}</div>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-3" for="menu_class">{tr}Class:{/tr}</label>

					<div class="col-md-9">
						<input id="menu_class" type="text" name="class" value="{$class|escape}"
							   class="form-control"><br>

						<div class="help-block">{tr}Input an HTML class value for the menu option. Separate with a space for multiple classes.{/tr}</div>
					</div>
				</div>
				{if $prefs.feature_userlevels eq 'y'}
					<div class="form-group">
						<label class="control-label col-md-3" for="level">{tr}Level:{/tr}</label>

						<div class="col-md-9">
							<select name="level" id="level">
								<option value="0"{if $level eq 0} selected="selected"{/if}>{tr}All{/tr}</option>
								{foreach key=levn item=lev from=$prefs.userlevels}
									<option value="{$levn}"{if $userlevel eq $levn} selected="selected"{/if}>{$lev}</option>{/foreach}
							</select>
						</div>
					</div>
				{/if}
				{if $prefs.menus_items_icons eq 'y'}
					<div class="form-group">
						<label class="control-label col-md-3" for="icon">{tr}Icon:{/tr}</label>

						<div class="col-md-9">
							<input type="text" name="icon" value="{$icon|escape}" class="form-control">
						</div>
					</div>
				{/if}
				<div class="form-group text-center">
					<input type="submit" class="btn btn-primary btn-sm" name="save" value="{tr}Save{/tr}">
				</div>
			</div>
		</form>

	</div>
{/tab}

{tab name="{tr}Preview{/tr}"}
	<h2>{tr}Preview menu{/tr}</h2>
	<form action="tiki-admin_menu_options.php" class="form-inline">
		<input type="hidden" name="menuId" value="{$menuId}">
		<div class="form-group">
			<label for="preview_type" class="control-label">Type:</label>
			<select id="preview_type" class="form-control" name="preview_type" onchange="this.form.submit()">
				<option value="vert"{if $preview_type eq 'vert'} selected{/if}>{tr}Vertical{/tr}</option>
				<option value="horiz"{if $preview_type eq 'horiz'} selected{/if}>{tr}Horizontal{/tr}</option>
			</select>
		</div>
		<div class="checkbox">
			<label for="preview_css">
			<input type="checkbox" id="preview_css" name="preview_css"
			   onchange="this.form.submit()"{if $preview_css eq 'y'} checked="checked"{/if}>
				CSS</label>
		</div>
	</form>

	<h2>Smarty Code</h2>
	<pre id="preview_code">
	{ldelim}menu id={$menuId} css={$preview_css} type={$preview_type}{rdelim
					}</pre>{* <pre> cannot have extra spaces for indenting *}
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">{$editable_menu_info.name|escape}</h3>
		</div>
		<div class="panel-body clearfix">
			{menu id=$menuId css=$preview_css type=$preview_type}
		</div>
	</div>

{/tab}
{/tabset}
