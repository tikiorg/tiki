{* $Id$ *}
{strip}

{assign var=thispage value=$page|escape:"url"}

<div class="clearfix" id="page-bar">
	{if $edit_page eq 'y'}
		{if $wysiwyg ne 'y' or ($wysiwyg eq 'y' and ($prefs.wysiwyg_wiki_parsed eq 'y' or $prefs.wysiwyg_wiki_semi_parsed eq 'y'))} {* Show this button only in wiki parsing mode *}
{*
			{button href="#edithelp" _onclick="javascript:show('edithelpzone');hide('wikiplhelp-tab');show('wikihelp-tab'); return true;" name="edithelp" _text="{tr}Wiki Help{/tr}"}
			{button href="#edithelp" _onclick="javascript:show('edithelpzone');hide('wikihelp-tab');show('wikiplhelp-tab'); return true;" name="edithelp" _text="{tr}Plugin Help{/tr}"}
*}
		{/if}
	{else}
		{if $show_page == 'y'} {* Show this buttons only if page view mode *}

			{* don't show comments if feature disabled or not enough rights *}
			{if $prefs.feature_wiki_comments == 'y'
				&& $tiki_p_wiki_view_comments == 'y'
				&& (($tiki_p_read_comments == 'y'
				&& $comments_cant != 0)
				|| $tiki_p_post_comments == 'y'
				||$tiki_p_edit_comments == 'y')}
				{assign var=pagemd5 value=$page|@md5}
				{include file=comments_button.tpl}
			{/if}

			{* don't show attachments button if feature disabled or no corresponding rights or no attached files and r/o*}

			{if $prefs.feature_wiki_attachments == 'y'
				&& (
					$tiki_p_wiki_view_attachments == 'y'
					&& count($atts) > 0
					|| $tiki_p_wiki_attach_files == 'y'
					|| $tiki_p_wiki_admin_attachments == 'y')
			}
				{if $atts|@count gt 0}
					{assign var=thisbuttonclass value='highlight'}
				{else}
					{assign var=thisbuttonclass value=''}
				{/if}
				{capture assign=thistext}{strip}
					{if $atts|@count == 0 || $tiki_p_wiki_attach_files == 'y' && $tiki_p_wiki_view_attachments == 'n' && $tiki_p_wiki_admin_attachments == 'n'}
						{tr}Attach File{/tr}
					{elseif $atts|@count == 1}
						{tr}1 File Attached{/tr}
					{else}
						{tr}{$atts|@count} files attached{/tr}
					{/if}
				{/strip}{/capture}
				{button href="#attachments" _flip_id="attzone$pagemd5" _class=$thisbuttonclass _text=$thistext _flip_default_open=$prefs.w_displayed_default}
			{/if}{* attachments *}

			{if $prefs.feature_multilingual eq 'y' and $tiki_p_edit eq 'y' and !$lock}
				{if $beingStaged == 'y'}
					{* TODO : What about staging? *}
					{button href="tiki-edit_translation.php?page=$thisapprovedPageName" _text="{tr}Translate{/tr}"}
				{else}
					{button href="tiki-edit_translation.php?page=$thispage" _text="{tr}Translate{/tr}"}
				{/if}
			{/if}
		{/if}
	{/if}
</div>

{if $wiki_extras eq 'y' && $prefs.feature_wiki_attachments eq 'y' and $tiki_p_wiki_view_attachments eq 'y'}
	<a name="attachments"></a>
	{include file=attachments.tpl}
{/if}

{if $prefs.feature_wiki_comments eq 'y' and $tiki_p_wiki_view_comments == 'y' and $edit_page ne 'y'}
	<a name="comments"></a>
	{include file=comments.tpl}
{/if}

{/strip}
