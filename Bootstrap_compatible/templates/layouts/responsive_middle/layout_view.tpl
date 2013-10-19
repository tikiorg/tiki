{* $Id$ *}<!DOCTYPE html>
<html lang="{if !empty($pageLang)}{$pageLang}{else}{$prefs.language}{/if}"{if !empty($page_id)} id="page_{$page_id}"{/if}>
<head>
    {include file='header.tpl'}
</head>
<body{html_body_attributes}>

<ul class="jumplinks" style="position:absolute;top:-9000px;left:-9000px;z-index:9;">
    <li><a href="#tiki-center" title="{tr}Jump to Content{/tr}">{tr}Jump to Content{/tr}</a></li>
</ul>
{$cookie_consent_html}
{* ===Fullscreen button removed === *}

{* TikiTest ToolBar *}
{if $prefs.feature_tikitests eq 'y' and !empty($tikitest_state) and $tikitest_state neq 0}
    {include file='tiki-tests_topbar.tpl'}
{/if}

{if $prefs.feature_ajax eq 'y'}
    {include file='tiki-ajax_header.tpl'}
{/if}
    <div class="main container"> {* fixed-width div id removed *}
        {if ($prefs.feature_fullscreen != 'y' or $smarty.session.fullscreen != 'y') and ($prefs.layout_section ne 'y' or $prefs.feature_top_bar ne 'n')}
            {if $prefs.module_zones_top eq 'fixed' or ($prefs.module_zones_top ne 'n' && $top_modules|@count > 0)}
                <header class="header" {if $prefs.feature_bidi eq 'y'} dir="rtl"{/if}>
                    {modulelist zone=top}
                </header>
            {/if}
        {/if}
		<div class="middle">
      	    <div class="topbar">
         	    {modulelist zone=topbar}
        	</div>
            <div class="c1c2 row">
                <div class="col-lg-8 col-lg-push-2">
                   	        {if $prefs.module_zones_pagetop eq 'fixed' or ($prefs.module_zones_pagetop ne 'n' && $pagetop_modules|@count > 0)}
                           	    {modulelist zone=pagetop}
                           	{/if}
                           	{if (isset($section) && $section neq 'share') && $prefs.feature_share eq 'y' && $tiki_p_share eq 'y' and (!isset($edit_page) or $edit_page ne 'y' and $prefs.feature_site_send_link ne 'y')}
                           	    <div class="share">
                           	        <a title="{tr}Share this page{/tr}" href="tiki-share.php?url={$smarty.server.REQUEST_URI|escape:'url'}">{tr}Share this page{/tr}</a>
                           	    </div>
                           	{/if}
                           	{if $prefs.feature_tell_a_friend eq 'y' && $tiki_p_tell_a_friend eq 'y' and (!isset($edit_page) or $edit_page ne 'y' and $prefs.feature_site_send_link ne 'y')}
                           	    <div class="tellafriend">
                           	        <a title="{tr}Email this page{/tr}" href="tiki-tell_a_friend.php?url={$smarty.server.REQUEST_URI|escape:'url'}">{tr}Email this page{/tr}</a>
                           	    </div>
                           	{/if}
                           	{error_report}
                           	{if $display_msg}
                           	    {remarksbox type="note" title="{tr}Notice{/tr}"}{$display_msg|escape}{/remarksbox}
                           	{/if}
                           	<div id="role_main">
                                {block name=title}{/block}
                                {block name=content}{/block}
                                {block name=show_content}{/block}{* Help separate the page content from the whole page. Must be defined at root to work. AB *}
                           	</div>
                           	{if $prefs.module_zones_pagebottom eq 'fixed' or ($prefs.module_zones_pagebottom ne 'n' && $pagebottom_modules|@count > 0)}
                           	    {modulelist zone=pagebottom}
                           	{/if}
                           	    {show_help}
                </div>
                <div class="col-lg-2 col-lg-pull-8" id="col2">
                    {modulelist zone=left class="content modules"}
                </div>
                <div class="col-lg-2" id="col3">
                            {modulelist zone=right class="content modules"}
                  	        {if $module_pref_errors}
                  	            <div class="content modules">
                  	                {remarksbox type="warning" title="{tr}Module errors{/tr}"}
                  	                    {tr}The following modules could not be loaded{/tr}
                  	                    <form method="post" action="tiki-admin.php">
                  	                        {foreach from=$module_pref_errors key=index item=pref_error}
                  	                            <p>{$pref_error.mod_name}:</p>
                  	                            {preference name=$pref_error.pref_name}
                  	                        {/foreach}
                  	                        <div class="submit">
                  	                            <input type="submit" class="btn btn-default" value="{tr}Change{/tr}"/>
                  	                        </div>
                  	                    </form>
                  	                {/remarksbox}
                  	            </div>
                  	        {/if}
                </div>
        </div><!-- end container -->
  {*  {if ($prefs.feature_fullscreen != 'y' or $smarty.session.fullscreen != 'y') and ($prefs.layout_section ne 'y' or $prefs.feature_bot_bar ne 'n')}
        {if $prefs.module_zones_bottom eq 'fixed' or ($prefs.module_zones_bottom ne 'n' && $bottom_modules|@count > 0)}
*}       <footer class="footer" id="footer">
                <div class="footer_liner">
                    <div class="fixedwidth footerbgtrap">
                        {modulelist zone=bottom class="content modules" bidi=y}
                    </div>
                </div>
            </footer>{* -- END of footer -- *}
{*        {/if}
   {/if} *}

{include file='footer.tpl'}
{if isset($prefs.socialnetworks_user_firstlogin) && $prefs.socialnetworks_user_firstlogin == 'y'}
    {include file='tiki-socialnetworks_firstlogin_launcher.tpl'}
{/if}

{if $prefs.site_google_analytics_account}
    {wikiplugin _name=googleanalytics account=$prefs.site_google_analytics_account}{/wikiplugin}
{/if}
{if $prefs.feature_endbody_code}
    {eval var=$prefs.feature_endbody_code}
{/if}
{interactivetranslation}
<!-- Put JS at the end -->
{if $headerlib}
    {$headerlib->output_js_config()}
    {$headerlib->output_js_files()}
    {$headerlib->output_js()}
{/if}

{* === From Bootstrap ===
<!-- Le javascript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<!-- <script src="fluid_files/jquery.js"></script> -->
<script src="fluid_files/bootstrap-transition.js"></script>
<script src="fluid_files/bootstrap-alert.js"></script>
<script src="fluid_files/bootstrap-modal.js"></script>
<script src="fluid_files/bootstrap-dropdown.js"></script>
<script src="fluid_files/bootstrap-scrollspy.js"></script>
<script src="fluid_files/bootstrap-tab.js"></script>
<script src="fluid_files/bootstrap-tooltip.js"></script>
<script src="fluid_files/bootstrap-popover.js"></script>
<script src="fluid_files/bootstrap-button.js"></script>
<script src="fluid_files/bootstrap-collapse.js"></script>
<script src="fluid_files/bootstrap-carousel.js"></script>
<script src="fluid_files/bootstrap-typeahead.js"></script>
*}
</body>
</html>
{if !empty($smarty.request.show_smarty_debug)}
    {debug}
{/if}
