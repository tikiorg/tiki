{include file="header.tpl"}
{if $prefs.feature_ajax eq 'y'}
{include file="tiki-ajax_header.tpl"}
{/if}
<div id="main"{if $prefs.feature_bidi eq 'y'} dir="rtl"{/if}>
{if $prefs.feature_fullscreen != 'y' or $smarty.session.fullscreen != 'y'}
<div id="header">
	{if $prefs.feature_siteidentity eq 'y'}
	{* Site identity header section *}
	<div id="siteheader">
		{include file="tiki-site_header.tpl"}
	</div>
	{/if}
	{if $prefs.feature_top_bar eq 'y'}
	<div id="tiki-top">
		{include file="tiki-top_bar.tpl"}
	</div>
	{/if}
</div>
{/if}
<div id="middle">
	<div class="clearfix {if $prefs.feature_fullscreen != 'n' and $smarty.session.fullscreen != 'n'}fullscreen{/if}{if $prefs.feature_fullscreen != 'y' and $smarty.session.fullscreen !='n'}nofullscreen{/if}" id="c1c2">
		<div id="wrapper">
			<div id="col1" class="{if $prefs.feature_left_column ne 'n'}marginleft{/if}{if $prefs.feature_right_column ne 'n'} marginright{/if}">
				{if $smarty.session.fullscreen neq 'y'}
		{if $prefs.feature_left_column eq 'user' or $prefs.feature_right_column eq 'user'}
			<div class="clearfix" id="showhide_columns">
			{if $prefs.feature_left_column eq 'user' && $left_modules|@count > 0 && $show_columns.left_modules ne 'n'}
				<div style="text-align:left;float:left;"><a class="flip" 
					href="#" onclick="toggleCols('col2','left'); return false">{tr}Show/Hide Left Menus{/tr}</a></div>
    		{/if}
			{if $prefs.feature_right_column eq 'user'&& $right_modules|@count > 0 && $show_columns.right_modules ne 'n'}
				<div class="clearfix" style="text-align:right;float:right"><a class="flip"
					href="#" onclick="toggleCols('col3','right'); return false">{tr}Show/Hide Right Menus{/tr}</a>
				</div>
			{/if}
			<br style="clear:both" />
			</div>
		{/if}
	{/if}
	{if $prefs.feature_tell_a_friend eq 'y' && $tiki_p_tell_a_friend eq 'y' and (!isset($edit_page) or $edit_page ne 'y')}
				<div class="tellafriend"><a href="tiki-tell_a_friend.php?url={$smarty.server.REQUEST_URI|escape:'url'}">{tr}Email this page{/tr}</a>
				</div>
				{/if}
					<div id="tiki-center" {*id needed for ajax editpage link*} class="content">
						{$mid_data}
					</div>
				</div>
			</div>
			{if $prefs.feature_fullscreen != 'y' or $smarty.session.fullscreen != 'y'}
			{if $prefs.feature_left_column ne 'n' && $left_modules|@count > 0 && $show_columns.left_modules ne 'n'}
				<div id="col2">
					<div class="content">
						{section name=homeix loop=$left_modules}
						 	{$left_modules[homeix].data}
						{/section}
    			    </div>
				</div>
			{/if}
			{/if}
			</div>{* -- END of c1c2 -- *}
{if $prefs.feature_fullscreen != 'y' or $smarty.session.fullscreen != 'y'}
	{if $prefs.feature_right_column ne 'n' && $right_modules|@count > 0 && $show_columns.right_modules ne 'n'}
		<div class="clearfix" id="col3" 
		{if $prefs.feature_right_column eq 'user'} 
		style="display:{if isset($cookie.show_rightcolumn) and $cookie.show_rightcolumn ne 'y'}none{else}table-cell;_display:block{/if};"
		{/if}>
		<div class="content">
			{section name=homeix loop=$right_modules}
				{$right_modules[homeix].data}
			{/section}
        </div>
	</div><br style="clear:both" />
	{/if}
{/if}
</div></div>{* -- END of middle part wrapper -- *}
{if $prefs.feature_fullscreen != 'y' or $smarty.session.fullscreen != 'y'}
{if $prefs.feature_bot_bar eq 'y'}
<div id="footer">
	<div class="footerbgtrap">
		<div class="content">
   			{include file="tiki-bot_bar.tpl"}
		</div>
	</div>
</div>
{/if}
{/if}
{include file="footer.tpl"}