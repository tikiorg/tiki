{* $Id$ *}

{title help="Articles" admpage="articles"}{tr}Articles{/tr}{/title}

<div class="t_navbar margin-bottom-md">
	{if $tiki_p_edit_article eq 'y' or $tiki_p_admin eq 'y' or $tiki_p_admin_cms eq 'y'}
		{button href="tiki-edit_article.php" _type="text" class="btn btn-link" _icon_name="create" _text="{tr}New Article{/tr}"}
	{/if}
	{if $prefs.feature_submissions == 'y' && $tiki_p_edit_submission == "y" && $tiki_p_edit_article neq 'y' && $tiki_p_admin neq 'y' && $tiki_p_admin_cms neq 'y'}
		{button href="tiki-edit_submission.php"  _type="text" class="btn btn-link" _icon_name="create" _text="{tr}New Submission{/tr}"}
	{/if}
	{if $tiki_p_read_article eq 'y' or $tiki_p_articles_read_heading eq 'y' or $tiki_p_admin eq 'y' or $tiki_p_admin_cms eq 'y'}
		{button href="tiki-view_articles.php" _type="text" class="btn btn-link" _icon_name="articles" _text="{tr}View Articles{/tr}"}
	{/if}
	{if $prefs.feature_submissions == 'y' && ($tiki_p_approve_submission == "y" || $tiki_p_remove_submission == "y" || $tiki_p_edit_submission == "y")}
		{button href="tiki-list_submissions.php" _type="text" class="btn btn-link" _icon_name="view" _text="{tr}View Submissions{/tr}"}
	{/if}
	{if $tiki_p_admin eq 'y' or $tiki_p_admin_cms eq 'y'}
		{button href="tiki-admin_topics.php"  _type="text" class="btn btn-link" _icon_name="structure" _text="{tr}Article Topics{/tr}"}
		{button href="tiki-article_types.php"  _type="text" class="btn btn-link" _icon_name="structure" _text="{tr}Article Types{/tr}"}
	{/if}
</div>

{if $listpages or ($find ne '') or ($types ne '') or ($topics ne '') or ($lang ne '') or ($categId ne '')}
    <div class="row row-sidemargins-zero">
        <div class="col-md-6">
	        {include file='find.tpl' find_show_languages='y' find_show_categories_multi='y' find_show_num_rows='y' find_show_date_range='y'}
        </div>
    </div>
{/if}

{if $mapview}
	{wikiplugin _name="map" scope=".listarticlesmap .geolocated" width="400" height="400"}{/wikiplugin}
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

<form name="checkform" method="get">
	<input type="hidden" name="maxRecords" value="{$maxRecords|escape}">
	{assign var=numbercol value=1}
	<div class="{if $js === 'y'}table-responsive{/if}"> {*the table-responsive class cuts off dropdown menus *}
		<table class="table">
			<tr>
				{if $listpages and $tiki_p_remove_article eq 'y'}
					<th class="auto">
						{select_all checkbox_names='checked[]'}
					</th>
				{/if}
				{if $prefs.art_list_title eq 'y'}
					{assign var=numbercol value=$numbercol+1}
					<th>{self_link _sort_arg='sort_mode' _sort_field='title'}{tr}Title{/tr}{/self_link}</th>
				{/if}
				{if $prefs.art_list_id eq 'y'}
					{assign var=numbercol value=$numbercol+1}
					<th>{self_link _sort_arg='sort_mode' _sort_field='articleId'}{tr}Id{/tr}{/self_link}</th>
				{/if}
				{if $prefs.art_list_type eq 'y'}
					{assign var=numbercol value=$numbercol+1}
					<th>{self_link _sort_arg='sort_mode' _sort_field='type'}{tr}Type{/tr}{/self_link}</th>
				{/if}
				{if $prefs.art_list_topic eq 'y'}
					{assign var=numbercol value=$numbercol+1}
					<th>{self_link _sort_arg='sort_mode' _sort_field='topicName'}{tr}Topic{/tr}{/self_link}</th>
				{/if}
				{if $prefs.art_list_date eq 'y'}
					{assign var=numbercol value=$numbercol+1}
					<th>{self_link _sort_arg='sort_mode' _sort_field='publishDate'}{tr}Publish Date{/tr}{/self_link}</th>
				{/if}
				{if $prefs.art_list_expire eq 'y'}
					{assign var=numbercol value=$numbercol+1}
					<th>{self_link _sort_arg='sort_mode' _sort_field='expireDate'}{tr}Expiry Date{/tr}{/self_link}</th>
				{/if}
				{if $prefs.art_list_visible eq 'y'}
					{assign var=numbercol value=$numbercol+1}
					<th><span>{tr}Visible{/tr}</span></th>
				{/if}
				{if $prefs.art_list_lang eq 'y'}
					{assign var=numbercol value=$numbercol+1}
					<th>{self_link _sort_arg='sort_mode' _sort_field='lang'}{tr}Language{/tr}{/self_link}</th>
				{/if}
				{if $prefs.art_list_author eq 'y'}
					{assign var=numbercol value=$numbercol+1}
					<th>{self_link _sort_arg='sort_mode' _sort_field='author'}{tr}User{/tr}{/self_link}</th>
				{/if}
				{if $prefs.art_list_authorName eq 'y'}
					{assign var=numbercol value=$numbercol+1}
					<th>{self_link _sort_arg='sort_mode' _sort_field='authorName'}{tr}Author{/tr}{/self_link}</th>
				{/if}
				{if $prefs.art_list_rating eq 'y'}
					{assign var=numbercol value=$numbercol+1}
					<th class="text-right">
						{self_link _sort_arg='sort_mode' _sort_field='rating'}{tr}Rating{/tr}{/self_link}
					</th>
				{/if}
				{if $prefs.art_list_usersRating eq 'y'}
					{assign var=numbercol value=$numbercol+1}
					<th class="text-right">
						{self_link _sort_arg='sort_mode' _sort_field='usersRating'}{tr}Users rating{/tr}{/self_link}
					</th>
				{/if}
				{if $prefs.art_list_reads eq 'y'}
					{assign var=numbercol value=$numbercol+1}
					<th class="text-right">
						{self_link _sort_arg='sort_mode' _sort_field='nbreads'}{tr}Reads{/tr}{/self_link}
					</th>
				{/if}
				{if $prefs.art_list_size eq 'y'}
					{assign var=numbercol value=$numbercol+1}
					<th class="text-right">{self_link _sort_arg='sort_mode' _sort_field='size'}{tr}Size{/tr}{/self_link}</th>
				{/if}
				{if $prefs.art_list_img eq 'y'}
					{assign var=numbercol value=$numbercol+1}
					<th>{tr}Image{/tr}</th>
				{/if}
				{if $prefs.art_list_ispublished eq 'y' and $tiki_p_edit_article eq 'y'}
					<th>{self_link _sort_arg='sort_mode' _sort_field='ispublished'}{tr}Published{/tr}{/self_link}</th>
				{/if}
				{if $tiki_p_edit_article eq 'y' or $tiki_p_remove_article eq 'y' or isset($oneEditPage) or $tiki_p_read_article}
					{assign var=numbercol value=$numbercol+1}
					<th></th>
				{/if}
			</tr>

			{section name=changes loop=$listpages}

				{if isset($mapview) and $mapview}
					<div class="listarticlesmap" style="display:none;">{object_link type="article" id="`$listpages[changes].articleId|escape`"}</div>
				{/if}

				<tr>
					{if $tiki_p_remove_article eq 'y'}
						<td class="checkbox-cell">
							<input type="checkbox" name="checked[]" value="{$listpages[changes].articleId|escape}" {if $listpages[changes].checked eq 'y'}checked="checked" {/if}>
						</td>
					{/if}
					{if $prefs.art_list_title eq 'y'}
						<td class="text">
							{if $tiki_p_read_article eq 'y'}
								{object_link type=article id=$listpages[changes].articleId title=$listpages[changes].title|truncate:$prefs.art_list_title_len:"...":true}
							{else}
								{$listpages[changes].title|truncate:$prefs.art_list_title_len:"...":true|escape}
							{/if}
						</td>
					{/if}
					{if $prefs.art_list_id eq 'y'}
						<td class="integer">{$listpages[changes].articleId}</td>
					{/if}
					{if $prefs.art_list_type eq 'y'}
						<td class="text">{tr}{$listpages[changes].type|escape}{/tr}</td>
					{/if}
					{if $prefs.art_list_topic eq 'y'}
						<td class="text">{$listpages[changes].topicName|escape}</td>
					{/if}
					{if $prefs.art_list_date eq 'y'}
						<td class="date" title="{$listpages[changes].publishDate|tiki_short_datetime}">{$listpages[changes].publishDate|tiki_short_date}</td>
					{/if}
					{if $prefs.art_list_expire eq 'y'}
						<td class="date" title="{$listpages[changes].expireDate|tiki_short_datetime}">{$listpages[changes].expireDate|tiki_short_date}</td>
					{/if}
					{if $prefs.art_list_visible eq 'y'}
						<td class="text">{tr}{$listpages[changes].disp_article}{/tr}</td>
					{/if}
					{if $prefs.art_list_lang eq 'y'}
						<td class="text">{tr}{$listpages[changes].lang}{/tr}</td>
					{/if}
					{if $prefs.art_list_author eq 'y'}
						<td class="text">{$listpages[changes].author|escape}</td>
					{/if}
					{if $prefs.art_list_authorName eq 'y'}
						<td class="text">{$listpages[changes].authorName|escape}</td>
					{/if}
					{if $prefs.art_list_rating eq 'y'}
						<td class="integer">{$listpages[changes].rating}</td>
					{/if}
					{if $prefs.art_list_usersRating eq 'y'}
						<td class="integer">{rating_result_avg id=$listpages[changes].articleId type=article}</td>
					{/if}
					{if $prefs.art_list_reads eq 'y'}
						<td class="integer">{$listpages[changes].nbreads}</td>
					{/if}
					{if $prefs.art_list_size eq 'y'}
						<td class="integer">{$listpages[changes].size|kbsize}</td>
					{/if}
					{if $prefs.art_list_img eq 'y'}
						<td class="text">{tr}{$listpages[changes].hasImage}{/tr}/{tr}{$listpages[changes].useImage}{/tr}</td>
					{/if}
					{if $prefs.art_list_ispublished eq 'y' and $tiki_p_edit_article eq 'y'}
						<td style="text-align:center;">{$listpages[changes].ispublished}</td>
					{/if}
					<td class="action">
						{capture name=articles_actions}
							{strip}
								{if $tiki_p_read_article eq 'y'}
									{$libeg}<a href="{$listpages[changes].articleId|sefurl:article}">
										{icon name="view" _menu_text='y' _menu_icon='y' alt="{tr}View{/tr}"}
									</a>{$liend}
								{/if}
								{if $tiki_p_edit_article eq 'y' or (!empty($user) and $listpages[changes].author eq $user and $listpages[changes].creator_edit eq 'y')}
									{$libeg}<a href="tiki-edit_article.php?articleId={$listpages[changes].articleId}">
										{icon name="edit" _menu_text='y' _menu_icon='y' alt="{tr}Edit{/tr}"}
									</a>{$liend}
								{/if}
								{if $tiki_p_admin_cms eq 'y' or $tiki_p_assign_perm_cms eq 'y'}
									{$libeg}{permission_link mode=text type=article permType=articles id=$listpages[changes].articleId}{$liend}
								{/if}
								{if $tiki_p_remove_article eq 'y'}
									{$libeg}{self_link _menu_text='y' _menu_icon='y' remove=$listpages[changes].articleId _icon_name="remove"}
										{tr}Remove{/tr}
									{/self_link}{$liend}
								{/if}
							{/strip}
						{/capture}
						{if $js === 'n'}<ul class="cssmenu_horiz"><li>{/if}
						<a
							class="tips"
							title="{tr}Actions{/tr}"
							href="#"
							{if $js === 'y'}{popup delay="0|2000" fullhtml="1" center=true text=$smarty.capture.articles_actions|escape:"javascript"|escape:"html"}{/if}
							style="padding:0; margin:0; border:0"
						>
							{icon name='wrench'}
						</a>
						{if $js === 'n'}
							<ul class="dropdown-menu" role="menu">{$smarty.capture.articles_actions}</ul></li></ul>
						{/if}
					</td>
				</tr>
			{sectionelse}
				{norecords _colspan=$numbercol}
			{/section}
		</table>
	</div>
	{if $listpages and $tiki_p_remove_article eq 'y'}
		<div>
			{button _text="{tr}Select Duplicates{/tr}" _onclick="checkDuplicateRows(this,'td:not(:eq(2))'); return false;"}
			<br><br>
			<div class="col-lg-9 input-group">
				<label for="submit_mult" class="col-lg"></label>
					<select name="submit_mult" class="form-control">
						<option value="">{tr}Select action to perform with checked...{/tr}</option>
						<option value="remove_articles">{tr}Remove{/tr}</option>
					</select>
				</label>
				<span class="input-group-btn">
				<input type="submit" class="btn btn-primary" value="{tr}OK{/tr}">
				</span>
			</div>
		</div>
	{/if}

	{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}
</form>
