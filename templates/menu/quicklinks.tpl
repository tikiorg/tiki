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
