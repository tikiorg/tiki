{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/simple/tiki.tpl,v 1.5 2003-12-21 17:47:35 mose Exp $ *}
{* Main template for TikiWiki layout *}
{include file="header.tpl"}
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
        {include file=$mid}
        {if $show_page_bar eq 'y'}
          {include file="tiki-page_bar.tpl"}
        {/if}
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
{include file="footer.tpl"}
