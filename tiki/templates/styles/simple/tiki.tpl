{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/simple/tiki.tpl,v 1.3 2003-10-16 15:42:45 luciash Exp $ *}{include file="header.tpl"}
{* Main template for TikiWiki layout *}
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
      {include file=$mid}
      {if $show_page_bar eq 'y'}
        {include file="tiki-page_bar.tpl"}
      {/if}
    </div><!-- center -->
  </div><!-- div id="tiki-mid" -->

  {if $feature_bot_bar eq 'y'}
    <div id="bottom" {$add_style}>
      {include file="tiki-bot_bar.tpl"}
    </div><!-- bottom -->
  {/if}

</div><!-- tiki-main -->
{include file="footer.tpl"}
