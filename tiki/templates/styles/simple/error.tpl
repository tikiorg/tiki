{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/simple/error.tpl,v 1.3 2003-12-15 00:08:07 redflo Exp $ *}
{* Main template for TikiWiki error page layout *}
{include file="header.tpl"}
<div id="main">
  {if $feature_top_bar eq 'y'}
  <div id="top">
    {include file="tiki-top_bar.tpl"}
  </div><!-- top -->
  {/if}
  <span class="hidden"><a href="#content">{tr}Skip to Content{/tr}</a></span>
  <div id="middle">
    {* Display left modules if available *}

    {if $feature_left_column eq 'y' and count($left_modules) gt 0}
      <div id="modules1" class="left">
        {section name=homeix loop=$left_modules}
          {$left_modules[homeix].data}
        {/section}
      </div><!-- used to be left -->
    {/if}
    
    {* Display right modules if available *}

    {if $feature_right_column eq 'y' and count($right_modules) gt 0}
      <div id="modules2" class="right">
        {section name=homeix loop=$right_modules}
          {$right_modules[homeix].data}
        {/section}
      </div><!-- used to be right -->
    {/if}

    {* Calculate size of center div (this is really needed! luci) *}

    {php} $add_style=''; {/php}
    {if $feature_left_column eq 'y' and count($left_modules) gt 0}
	    {php} $add_style.='margin-left: 25%;'; {/php}
    {/if}
    {if $feature_right_column eq 'y' and count($right_modules) gt 0}
	    {php} $add_style.=' margin-right: 25%;'; {/php}
    {/if}
    {php}
	  $add_style = strlen($add_style) ? ' style="'.$add_style.'"' : '';
	  global $smarty;
	  $smarty->assign('add_style', $add_style);
    {/php}

    {* Display main content in center column *}
    <a name="content"></a>
    <div id="maincontent"{$add_style}>
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

    

  </div><!-- div id="tiki-mid" -->

  {if $feature_bot_bar eq 'y'}
    <div id="bottom">
      <br class="clear" />
      {include file="tiki-bot_bar.tpl"}
    </div><!-- bottom -->
  {/if}

</div><!-- tiki-main -->

{* Include debugging console. Note it should be processed as near as possible to the end of file *}

{php}  include_once("tiki-debug_console.php"); {/php}
{include file="tiki-debug_console.tpl"}

{include file="footer.tpl"}
