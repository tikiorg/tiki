{include file="header.tpl"}{* This must be included as the first thing in a document to be XML compliant *}
{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/simple/tiki.tpl,v 1.11 2005-12-19 17:59:14 sylvieg Exp $ *}
{* Main template for TikiWiki layout *}
{if $feature_bidi eq 'y'}<table dir="rtl" ><tr><td>{/if}
<div id="main">
  {if $feature_ajax eq 'y'}
    <div id="ajaxLoading">{tr}Loading...{/tr}</div>
  {/if}
  {if $feature_top_bar eq 'y'}
  <div id="top">
    {include file="tiki-top_bar.tpl"}
  </div><!-- top -->
  {/if}
  <span class="hidden"><a href="#navigation">{tr}Skip to navigation{/tr}</a></span>
  <div id="middle">    

    {* Calculate size of center div (this is really needed! luci) *}

    {php}
			$wrapper='wrapper';
			$maincontent='maincontent';
			$add_style1=' 75%;';
			$add_style2=' 80%;';
    {/php}
    {if $feature_left_column ne 'n' and $feature_right_column eq 'n'}
      {php}
				$wrapper='leftcolonlywrapper';
				$maincontent='leftcolonlymaincontent';
				$add_style1=' 100%;'
			{/php}
    {/if}
    {if $feature_left_column eq 'n' and $feature_right_column ne 'n'}
      {php}
				$wrapper='rightcolonlywrapper';
				$maincontent='rightcolonlymaincontent';
				$add_style2=' 100%;'
			{/php}
    {/if}
    {if $feature_left_column ne 'n' and count($left_modules) gt 0}
	    {php} $add_style2=' 75%;'; {/php}
    {/if}
    {if $feature_right_column ne 'n' and count($right_modules) gt 0}
	    {php} $add_style1=' 80%;'; {/php}
    {/if}    
    {php}
	  global $smarty;
	  $smarty->assign('add_style1', $add_style1);
    $smarty->assign('add_style2', $add_style2);
    $smarty->assign('wrapper', $wrapper);
    $smarty->assign('maincontent', $maincontent);
    {/php}

    {* Display main content in center column *}    
    <div class="{$wrapper}">
      <div id="maincontent" class="{$maincontent}">
        {$mid_data}
        {if $show_page_bar eq 'y'}
          {include file="tiki-page_bar.tpl"}
        {/if}
      </div><!-- maincontent, used to be center -->
    
    {* Display left modules if available *}
    <a name="navigation"></a>

    {if $feature_left_column ne 'n' and count($left_modules) gt 0}
      <div id="modules1" class="left" style="margin-right:{$add_style2}">
        {section name=homeix loop=$left_modules}
          {$left_modules[homeix].data}
        {/section}
      </div><!-- modules1, used to be left -->
    {/if}
    </div>

    {* Display right modules if available *}

    {if $feature_right_column ne 'n' and count($right_modules) gt 0}
      <div id="modules2" class="right" style="margin-left:{$add_style1}">
        {section name=homeix loop=$right_modules}
          {$right_modules[homeix].data}
        {/section}
      </div><!-- modules2, used to be right -->
    {/if}
    
    {if $feature_bot_bar eq 'y'}
    <div id="bottom">
      <br class="clear" />
      {include file="tiki-bot_bar.tpl"}
    </div><!-- bottom -->
    {/if}
  </div><!-- middle -->

  

</div><!-- main --> 
{if $feature_bidi eq 'y'}</td></tr></table>{/if}
{include file="footer.tpl"}
