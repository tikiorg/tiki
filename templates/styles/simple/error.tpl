{include file="header.tpl"}{* This must be included as the first thing in a document to be XML compliant *}
{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/simple/error.tpl,v 1.5 2004-02-23 21:35:09 techtonik Exp $ *}
{* Main template for TikiWiki error page layout *}
<div id="main">
  {if $feature_top_bar eq 'y'}
  <div id="top">
    {include file="tiki-top_bar.tpl"}
  </div><!-- top -->
  {/if}
  <span class="hidden"><a href="#navigation">{tr}Skip to navigation{/tr}</a></span>
  <div id="middle">    

    {* Calculate size of center div (this is really needed! luci) *}

    {php}
     $add_style=' 100%;';
     $add_style1=' 75%;';
     $add_style2=' 80%;';
     $float='none';
     $float1='left';
     $float2='right';
    {/php}
    {if $feature_left_column eq 'y' and $feature_right_column eq 'n'}
      {php} $float1='none'; $float2='right'; $add_style1=' 100%;';{/php}
    {/if}
    {if $feature_left_column eq 'n' and $feature_right_column eq 'y'}
      {php} $float1='left'; $float2='none'; $add_style2=' 100%;' {/php}
    {/if}
    {if $feature_left_column eq 'y' and count($left_modules) gt 0}
	    {php} $add_style2=' 75%;'; {/php}
    {/if}
    {if $feature_right_column eq 'y' and count($right_modules) gt 0}
	    {php} $add_style1=' 80%;'; {/php}
    {/if}    
    {php}
	  {*$add_style = strlen($add_style1) ? ' style="'.$add_style1.'"' : '';*}
    {*$add_style2 = strlen($add_style2) ? ' style="'.$add_style2.'"' : '';*}
	  global $smarty;
	  $smarty->assign('add_style1', $add_style1);
    $smarty->assign('add_style2', $add_style2);
    $smarty->assign('float1', $float1);
    $smarty->assign('float2', $float2);
    {/php}

    {* Display main content in center column *}    
    <div class="float-wrapper" style="float: {$float1}; width:{$add_style1}">
      <div id="maincontent" style="float: {$float2}; width:{$add_style2}">
        <div class="cbox" id="error-box">
          <div class="cbox-title" id="error-caption">
            {tr}Error{/tr}
          </div>
          <div class="cbox-data" id="error-data">
            {$msg}<br /><br />
            {if $page and ($tiki_p_admin eq 'y' or  $tiki_p_admin_wiki eq 'y')}<a href="tiki-editpage.php?page={$page}" class="linkmenu">{tr}Create this page{/tr}</a><br /><br />{/if}
            <a href="{$tikiIndex}" class="linkmenu">{tr}Return to home page{/tr}</a>
          </div>
        </div>
      </div><!-- center -->
    
    {* Display left modules if available *}
    <a name="navigation"></a>

    {if $feature_left_column eq 'y' and count($left_modules) gt 0}
      <div id="modules1" class="left" style="margin-right:{$add_style2}">
        {section name=homeix loop=$left_modules}
          {$left_modules[homeix].data}
        {/section}
      </div><!-- used to be left -->
    {/if}
    </div>

    {* Display right modules if available *}

    {if $feature_right_column eq 'y' and count($right_modules) gt 0}
      <div id="modules2" class="right" style="margin-left:{$add_style1}">
        {section name=homeix loop=$right_modules}
          {$right_modules[homeix].data}
        {/section}
      </div><!-- used to be right -->
    {/if}
    
    {if $feature_bot_bar eq 'y'}
    <div id="bottom">
      <br class="clear" />
      {include file="tiki-bot_bar.tpl"}
    </div><!-- bottom -->
    {/if}
  </div><!-- div id="tiki-mid" -->

</div><!-- tiki-main -->

{* Include debugging console. Note it should be processed as near as possible to the end of file *}

{php}  include_once("tiki-debug_console.php"); {/php}
{include file="tiki-debug_console.tpl"}

{include file="footer.tpl"}
