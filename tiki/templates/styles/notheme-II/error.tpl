{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/notheme-II/error.tpl,v 1.1 2003-08-09 18:54:41 zaufi Exp $ *}
{* Index we display a wiki page here *}

{include file="header.tpl"}
<div id="tiki-main">
  {if $feature_top_bar eq 'y'}
  <div id="tiki-top">
    {include file="tiki-top_bar.tpl"}
  </div>
  {/if}
  <div id="tiki-mid">
    {if $feature_left_column eq 'y' and count($left_modules) gt 0}
      <div id="leftcolumn" style="top: 5px;">
      {section name=homeix loop=$left_modules}
      {$left_modules[homeix].data}
      {/section}
      </div>
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
          <a href="{$tikiIndex}" class="linkmenu">{tr}Return to home page{/tr}</a>
        </div>
      </div>
    </div>
   {if $feature_right_column eq 'y' and count($right_modules) gt 0}
      <div id="rightcolumn" style="top: 5px;">
        {section name=homeix loop=$right_modules}
          {$right_modules[homeix].data}
        {/section}
      </div>
    {/if}
  </div>
  {if $feature_bot_bar eq 'y'}
  <div id="tiki-bot">
    {include file="tiki-bot_bar.tpl"}
  </div>
  {/if}
</div>
{include file="footer.tpl"}
