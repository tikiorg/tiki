{* $Id$ *}
<div class="t_navbar">
	{if $tiki_p_edit_article eq 'y' or $tiki_p_admin eq 'y' or $tiki_p_admin_cms eq 'y'}
		{button href="tiki-edit_article.php" _icon_name="create" _text="{tr}New Article{/tr}"}
	{/if}
	{if $prefs.feature_submissions == 'y' && $tiki_p_edit_submission == "y" && $tiki_p_edit_article neq 'y' && $tiki_p_admin neq 'y' && $tiki_p_admin_cms neq 'y'}
		{button href="tiki-edit_submission.php" _icon_name="create" _text="{tr}New Submission{/tr}"}
	{/if}
	{if $tiki_p_read_article eq 'y' or $tiki_p_articles_read_heading eq 'y' or $tiki_p_admin eq 'y' or $tiki_p_admin_cms eq 'y'}
	{button href="tiki-view_articles.php" _icon_name="articles" _text="{tr}View Articles{/tr}"}
	{/if}
	{if $prefs.feature_submissions == 'y' && ($tiki_p_approve_submission == "y" || $tiki_p_remove_submission == "y" || $tiki_p_edit_submission == "y")}
		{button href="tiki-list_submissions.php" _icon_name="view" _text="{tr}View Submissions{/tr}"}
	{/if}
</div>
{if $ispublished eq 'n' && $tiki_p_edit_article eq 'y'}
	{remarksbox type='warning' title="{tr}Not Published{/tr}"}
	{tr}This article is currently unpublished and visible only to editors.{/tr}
	{/remarksbox}
{/if}

{if $is_categorized eq 'y' and $prefs.feature_categories eq 'y' and $prefs.feature_categorypath eq 'y'}
	<div align="right">{$display_catpath}</div>
{/if}

{if $prefs.feature_freetags eq 'y' and $tiki_p_view_freetags eq 'y' and isset($freetags.data[0]) and $prefs.freetags_show_middle eq 'y'}
	{include file='freetag_list.tpl'}
{/if}

{include file="{custom_template basetpl='article.tpl' modifiers=[$type,$articleId]}"}

{if $prefs.feature_article_comments == 'y' &&
		($tiki_p_read_comments == 'y' || $tiki_p_post_comments == 'y' || $tiki_p_edit_comments == 'y')}

	<div id="comment-container" data-target="{service controller=comment action=list type=article objectId=$articleId}"></div>
	{jq}
		var id = '#comment-container';
		$(id).comment_load($(id).data('target'));
		$(document).ajaxComplete(function(){$(id).tiki_popover();});
	{/jq}
{/if}

{if $is_categorized eq 'y' and $prefs.feature_categories eq 'y' and $prefs.feature_categoryobjects eq 'y'}
	{$display_catobjects}
{/if}
{if $is_categorized eq 'y' and $prefs.feature_categories eq 'y' and $prefs.category_morelikethis_algorithm ne ''}
	{include file='category_related_objects.tpl'}
{/if}
