{include file="header.tpl"}{* This must be included as the first thing in a document to be XML compliant *}

{* $Header: /cvsroot/tikiwiki/_mods/themes/matrix/templates/error.tpl,v 1.1 2004-10-29 19:14:22 damosoft Exp $ *}
{* Index we display a wiki page here *}

<div id="tiki-main">
  {if $feature_top_bar eq 'y'}
  <div id="tiki-top">
    {include file="tiki-top_bar.tpl"}
  </div><!-- top -->
  {/if}

  <div id="tiki-mid">

    {* Display left modules if available *}

    {if $feature_left_column eq 'y' and count($left_modules) gt 0}
      <div id="leftcolumn">
        {section name=homeix loop=$left_modules}
          {$left_modules[homeix].data}
        {/section}
      </div><!-- left -->
    {/if}

    {* Calculate size of center div *}

    {php} $add_style=''; {/php}
    {if $feature_left_column eq 'y' and count($left_modules) gt 0}
	{php} $add_style.='margin-left: 190px;'; {/php}
    {/if}
    {if $feature_right_column eq 'y' and count($right_modules) gt 0}
	{php} $add_style.='margin-right: 190px;'; {/php}
    {/if}
    {php}
	$add_style = strlen($add_style) ? 'style="'.$add_style.'"' : ''; 
	global $smarty;
	$smarty->assign('add_style', $add_style);
    {/php}

    {* Display center column *}

    <div id="tiki-center" {$add_style}>
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

    {* Display right modules if available *}

    {if $feature_right_column eq 'y' and count($right_modules) gt 0}
      <div id="rightcolumn">
        {section name=homeix loop=$right_modules}
          {$right_modules[homeix].data}
        {/section}
      </div><!-- right -->
    {/if}

  </div><!-- div id="tiki-mid" -->

  {if $feature_bot_bar eq 'y'}
    <div id="tiki-bot">
      {include file="tiki-bot_bar.tpl"}
    </div><!-- bottom -->
  {/if}

</div><!-- tiki-main -->

{* Include debugging console. Note it shoudl be processed as near as possible to the end of file *}

{php}  include_once("tiki-debug_console.php"); {/php}
{include file="tiki-debug_console.tpl"}

{include file="footer.tpl"}
