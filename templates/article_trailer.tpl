	<div class="articletrailer">
		<span>
			{if $show_size eq 'y'}
				({$size} {tr}bytes{/tr})
			{/if}
		</span>
		<div class="actions">
		{if $prefs.feature_multilingual eq 'y' and $show_lang eq 'y' and $lang and $prefs.show_available_translations eq 'y'}
			{include file='translated-lang.tpl' td='y' type='article'}
		{/if}
		{if $tiki_p_edit_article eq 'y'}
			<a class="icon" href="tiki-edit_article.php?articleId={$articleId}">{icon _id='page_edit'}</a>
		{/if}
		{if $prefs.feature_cms_print eq 'y'}
			<a class="icon" href="tiki-print_article.php?articleId={$articleId}">{icon _id='printer' alt='{tr}Print{/tr}'}</a>
		{/if}
		{if $prefs.feature_share eq 'y' && $tiki_p_share eq 'y'}
			<a title="{tr}Share page{/tr}" href="tiki-share.php?url={$smarty.server.REQUEST_URI|escape:'url'}">{icon _id='share_link' alt="{tr}Share this page{/tr}"}</a>
		{/if}
		{if $prefs.feature_tell_a_friend eq 'y' && $tiki_p_tell_a_friend eq 'y'}
			<a title="{tr}Send a link{/tr}" href="tiki-tell_a_friend.php?url={$smarty.server.REQUEST_URI|escape:'url'}">{icon _id='email_link' alt="{tr}Send a link{/tr}"}</a>
		{/if}
		{if $prefs.feature_multilingual eq 'y' and $tiki_p_edit_article eq 'y'}
			<a class="icon" href="tiki-edit_translation.php?id={$articleId}&amp;type=article">{icon _id='world' alt='{tr}Translation{/tr}'}</a>
		{/if}
		{if $tiki_p_remove_article eq 'y'}
			<a class="icon" href="tiki-list_articles.php?remove={$articleId}">{icon _id='cross' alt='{tr}Remove{/tr}'}</a>
		{/if}
		</div>
	</div>
	<br class="clear" />