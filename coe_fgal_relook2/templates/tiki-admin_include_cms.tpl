{* $Id$ *}

{remarksbox type="tip" title="{tr}Tip{/tr}"}
	{tr}Look under "Articles" on the application menu for links to{/tr} "<a class="rbox-link" href="tiki-admin_topics.php">{tr}Admin topics{/tr}</a>" {tr}and{/tr} "<a class="rbox-link" href="tiki-article_types.php">{tr}Admin types{/tr}</a>".
{/remarksbox}

{if !empty($msgs)}
	<div class="simplebox highlight">
	{foreach from=$msgs item=msg}
	{$msg}			 
	{/foreach}
	</div>
{/if}

<form method="post" action="tiki-admin.php?page=cms">
	<div class="input_submit_container clear" style="text-align: right;">
		<input type="submit" value="{tr}Change preferences{/tr}" />
	</div>

	{tabset name="admin_cms"}
		{tab name="{tr}General Settings{/tr}"}
			<input type="hidden" name="cmsprefs" />

			{preference name=art_home_title}
			{preference name=maxArticles}

			<fieldset>
				<legend>
					{tr}Features{/tr}{help url="Articles"}
				</legend>

				{preference name=feature_submissions}
				{preference name=feature_cms_rankings}
				{preference name=article_user_rating}
				<div class="adminoptionboxchild" id="article_user_rating_childcontainer">
					{preference name=article_user_rating_options}
				</div>

				{preference name=feature_article_comments}
				<div class="adminoptionboxchild" id="feature_article_comments_childcontainer">
					{preference name=article_comments_per_page}
					{preference name=article_comments_default_ordering}
				</div>

				{preference name=feature_cms_templates}
				{preference name=feature_cms_print}
				{preference name=feature_cms_emails}

				{preference name=article_paginate}
				{preference name=article_custom_attributes}

				<input type="hidden" name="cmsfeatures" />
			</fieldset>
			
			<fieldset>
				<legend>
					{tr}Article properties{/tr}
				</legend>
				{remarksbox type="tip" title="{tr}Tip{/tr}"}
					{tr}Give only one value (width or height) to keep the image proportions{/tr}
				{/remarksbox}

				{preference name=article_image_size_x}
				{preference name=article_image_size_y}
			</fieldset>

			<fieldset>
				<legend>
					{tr}Sharing on social networks{/tr}{help url="Social+Networks#Using+ShareThis"}
				</legend>
				{preference name=feature_cms_sharethis}
				<div class="adminoptionboxchild" id="feature_cms_sharethis_childcontainer">
					{preference name=article_sharethis_publisher}
				</div>
			</fieldset>

			<fieldset>
				<legend>{tr}Import CSV file{/tr}</legend>
				<div class="adminoptionbox">
					<div class="adminoptionlabel">
						<label for="csvlist">{tr}Batch upload (CSV file):{/tr}</label>
						<input type="file" name="csvlist" id="csvlist" /> 
						<br />
						<em>{tr}File format: title,authorName,heading,body,lang,user{/tr}....</em>
						<div align="center">
							<input type="submit" name="import" value="{tr}Import{/tr}" />
						</div>
					</div>
				</div>
			</fieldset>
		{/tab}

		{tab name="{tr}Articles Listing{/tr}"}
			<fieldset>
				<legend>{tr}List Articles{/tr}</legend>
				<div class="adminoptionbox">
					{tr}Select which items to display when listing articles:{/tr} 	  
					<a class="rbox-link" href="tiki-list_articles.php">tiki-list_articles.php</a>
				</div>
				<input type="hidden" name="artlist" />

				{preference name=art_sort_mode}

				{preference name=art_list_title}
				<div class="adminoptionboxchild" id="art_list_title_childcontainer">
					{preference name=art_list_title_len}
				</div>
				{preference name=art_list_id}
				{preference name=art_list_type}
				{preference name=art_list_topic}
				{preference name=art_list_date}
				{preference name=art_list_expire}
				{preference name=art_list_visible}
				{preference name=art_list_lang}
				{preference name=art_list_author}
				{preference name=art_list_rating}
				{preference name=art_list_reads}
				{preference name=art_list_size}
				{preference name=art_list_img}
				
				{preference name=gmap_article_list}
			</fieldset>
			<fieldset>
				<legend>{tr}Article View{/tr}</legend>
				{preference name=art_trailer_pos}
				{preference name=art_header_text_pos}
			</fieldset>
		{/tab}
	{/tabset}
	<div class="input_submit_container clear" style="text-align: center;">
		<input type="submit" value="{tr}Change preferences{/tr}" />
	</div>
</form>

