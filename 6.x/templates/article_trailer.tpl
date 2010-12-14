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
			<a class="icon" href="tiki-print_article.php?articleId={$articleId}">{icon _id='printer' alt="{tr}Print{/tr}"}</a>
		{/if}
		{if $prefs.feature_share eq 'y' && $tiki_p_share eq 'y'}
			<a title="{tr}Share page{/tr}" href="tiki-share.php?url={$smarty.server.REQUEST_URI|escape:'url'}">{icon _id='share_link' alt="{tr}Share this page{/tr}"}</a>
		{/if}
		{if $prefs.feature_cms_sharethis eq "y"}
			{capture name=shared_title}{tr}Share This{/tr}{/capture}
			{capture name=shared_link_title}{tr}ShareThis via AIM, social bookmarking and networking sites, etc.{/tr}{/capture}
			{literal}<script language="javascript" type="text/javascript">
				//Create your sharelet with desired properties and set button element to false
				var object{/literal}{$articleId}{literal} = SHARETHIS.addEntry({
					title:'{/literal}{$smarty.capture.shared_title|replace:'\'':'\\\''}{literal}'
				},
				{button:false});
				//Output your customized button
				document.write('<span class="share" id="share{/literal}{$articleId}{literal}"><a title="{/literal}{$smarty.capture.shared_link_title|replace:'\'':'\\\''}{literal}" href="javascript:void(0);"><img src="http://w.sharethis.com/images/share-icon-16x16.png?CXNID=1000014.0NXC" /></a></span>');
				//Tie customized button to ShareThis button functionality.
				var element{/literal}{$articleId}{literal} = document.getElementById("share{/literal}{$articleId}{literal}"); object{/literal}{$articleId}{literal}.attachButton(element{/literal}{$articleId}{literal}); </script>{/literal}
		{/if}
		{if $prefs.feature_tell_a_friend eq 'y' && $tiki_p_tell_a_friend eq 'y'}
			<a title="{tr}Send a link{/tr}" href="tiki-tell_a_friend.php?url={$smarty.server.REQUEST_URI|escape:'url'}">{icon _id='email_link' alt="{tr}Send a link{/tr}"}</a>
		{/if}
		{if $prefs.feature_multilingual eq 'y' and $tiki_p_edit_article eq 'y'}
			<a class="icon" href="tiki-edit_translation.php?id={$articleId}&amp;type=article">{icon _id='world' alt="{tr}Translation{/tr}"}</a>
		{/if}
		{if $tiki_p_remove_article eq 'y'}
			<a class="icon" href="tiki-list_articles.php?remove={$articleId}">{icon _id='cross' alt="{tr}Remove{/tr}"}</a>
		{/if}
		</div>
	</div>
	<br class="clear" />