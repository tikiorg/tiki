{include file="header.tpl"}{* This must be included as the first thing in a document to be XML compliant *}
{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/notheme/tiki.tpl,v 1.8 2005-12-19 17:59:20 sylvieg Exp $ *}
{* Index we display a wiki page here *}

{if $feature_bidi eq 'y'}<table dir="rtl" ><tr><td>{/if}

<div id="tiki-main">
{if $feature_ajax eq 'y'}
  <div id="ajaxLoading">{tr}Loading...{/tr}</div>
{/if}
  {if $feature_top_bar eq 'y'}
  <div id="tiki-top">
    {include file="tiki-top_bar.tpl"}
  </div><!-- top -->
  {/if}

  <div id="tiki-mid">

    {* Display left modules if available *}

    {if $feature_left_column ne 'n' and count($left_modules) gt 0}
      <div id="leftcolumn">
        {section name=homeix loop=$left_modules}
          {$left_modules[homeix].data}
        {/section}
      </div><!-- left -->
    {/if}

    {* Calculate size of center div *}

    {php} $add_style=''; {/php}
    {if $feature_left_column ne 'n' and count($left_modules) gt 0}
	{php} $add_style.='margin-left: 190px;'; {/php}
    {/if}
    {if $feature_right_column ne 'n' and count($right_modules) gt 0}
	{php} $add_style.='margin-right: 190px;'; {/php}
    {/if}
    {php}
    global $style;
	$add_style = strlen($add_style) && !strstr($style, "II") ? 'style="'.$add_style.'"' : ''; 
	global $smarty;
	$smarty->assign('add_style', $add_style);
    {/php}

    {* Display center column *}

    <div id="tiki-center" {$add_style}>
      {$mid_data}
      {if $show_page_bar eq 'y'}
        {include file="tiki-page_bar.tpl"}
      {/if}
      {if $feature_bot_bar eq 'y' and strstr($style, "II") ne ''}
        <div id="tiki-bot">
          {include file="tiki-bot_bar.tpl"}
        </div><!-- bottom -->
      {/if}
    </div><!-- center -->

    {* Display right modules if available *}

    {if $feature_right_column ne 'n' and count($right_modules) gt 0}
      <div id="rightcolumn">
        {section name=homeix loop=$right_modules}
          {$right_modules[homeix].data}
        {/section}
      </div><!-- right -->
    {/if}

  </div><!-- div id="tiki-mid" -->

  {if $feature_bot_bar eq 'y' and strstr($style, "II") eq ''}
    <div id="tiki-bot" {$add_style}>
      {include file="tiki-bot_bar.tpl"}
    </div><!-- bottom -->
  {/if}


</div><!-- tiki-main -->

{if $feature_bidi eq 'y'}</td></tr></table>{/if}

{include file="footer.tpl"}
