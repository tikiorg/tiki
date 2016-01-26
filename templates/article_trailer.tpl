{if !isset($preview)}
	{* Use css menus as fallback for item dropdown action menu if javascript is not being used *}
	{if $prefs.javascript_enabled != 'y'}
		{$js = 'n'}
	{else}
		{$js = 'y'}
	{/if}
	<div class="clearfix articletrailer">
		<span>
			{if $show_size eq 'y'}
				({$size} {tr}bytes{/tr})
			{/if}
		</span>
		<div class="actions hidden-print pull-right">
			<div class="btn-group">
				{if $prefs.feature_multilingual eq 'y' and $lang and $prefs.show_available_translations eq 'y'}
					{include file='translated-lang.tpl' object_type='article'}
				{/if}
				{if $js === 'n'}<ul class="cssmenu_horiz"><li>{/if}
				<a class="btn btn-link" data-toggle="dropdown" data-hover="dropdown" href="#">
					{icon name='menu-extra'}
				</a>
				<ul class="dropdown-menu dropdown-menu-right">
					<li class="dropdown-title">
						{tr}Article actions{/tr}
					</li>
					<li class="divider"></li>
					{if $tiki_p_edit_article eq 'y'}
						<li>
							<a href="tiki-edit_article.php?articleId={$articleId}">
								{icon name='edit'} {tr}Edit{/tr}
							</a>
						</li>
					{/if}
					{if $prefs.feature_cms_print eq 'y'}
						<li>
							<a href="tiki-print_article.php?articleId={$articleId}">
								{icon name='print'} {tr}Print{/tr}
							</a>
						</li>
					{/if}
					{if $prefs.user_favorites eq 'y'}
						<li>
							{favorite type="article" object=$articleId button_classes="icon"}
						</li>
					{/if}
					{if $prefs.feature_tell_a_friend eq 'y' && $tiki_p_tell_a_friend eq 'y'}
						<li>
							<a href="tiki-tell_a_friend.php?url={$smarty.server.REQUEST_URI|escape:'url'}">
								{icon name='envelope'} {tr}Send a link{/tr}
							</a>
						</li>
					{/if}
					{if $prefs.feature_share eq 'y' && $tiki_p_share eq 'y'}
						<li>
							<a class="tips" href="tiki-share.php?url={$smarty.server.REQUEST_URI|escape:'url'}">
								{icon name='share'} {tr}Share{/tr}
							</a>
						</li>
					{/if}
					{if $prefs.feature_cms_sharethis eq "y"}
						<li>
							{capture name=shared_title}
								{tr}ShareThis{/tr}
							{/capture}
							{literal}<script type="text/javascript">
								//Create your sharelet with desired properties and set button element to false
								var object{/literal}{$articleId}{literal} = SHARETHIS.addEntry({},
										{button:false});
								//Output your customized button
								document.write('<a id="share{/literal}{$articleId}{literal}" href="javascript:void(0);">{/literal}{icon name="sharethis"} {tr}ShareThis{/tr}{literal}</a>');
								//Tie customized button to ShareThis button functionality.
								var element{/literal}{$articleId}{literal} = document.getElementById("share{/literal}{$articleId}{literal}"); object{/literal}{$articleId}{literal}.attachButton(element{/literal}{$articleId}{literal}); </script>{/literal}
						</li>
					{/if}
					<li>
						{if $tiki_p_remove_article eq 'y'}
							<a href="tiki-list_articles.php?remove={$articleId}">
								{icon name='remove'} {tr}Remove{/tr}
							</a>
						{/if}
					</li>
				</ul>
				{if $js === 'n'}</li></ul>{/if}
			</div>
		</div>
	</div>
{/if}
