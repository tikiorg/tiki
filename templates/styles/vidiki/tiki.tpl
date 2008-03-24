{include file="header.tpl"}

{if $prefs.feature_bidi eq 'y'}<table dir="rtl" ><tr><td>{/if}
{if $prefs.feature_fullscreen != 'y' or $smarty.session.fullscreen != 'y'}

{* Index we display a wiki page here *}

 {if $prefs.feature_top_bar eq 'y'} 
 <div class="tiki-top">  <div class="title">{include file="tiki-top_bar.tpl"} 
<br />
  <a href="tiki-index.php" class="titlefont">
  {$prefs.siteTitle}
  {if $headtitle} : {$headtitle}
  {elseif $page ne ''} : {$page|escape} {* add $description|escape if you want to put the description *}
  {elseif $arttitle ne ''} : {$arttitle}
  {elseif $title ne ''} : {$title}
  {elseif $thread_info.title ne ''} : {$thread_info.title}
  {elseif $post_info.title ne ''} : {$post_info.title}
  {elseif $forum_info.name ne ''} : {$forum_info.name}
  {elseif $categ_info.name ne ''} : {$categ_info.name}
  {elseif $userinfo.login ne ''} : {$userinfo.login}
  {/if}
  </a>
  </div>
  
 
 <div id="shadow"></div></div> 
 {/if}
{/if}
      <div class="content">{$mid_data}</div>
{if $prefs.feature_fullscreen != 'y' or $smarty.session.fullscreen != 'y'}

      {if $prefs.feature_left_column eq 'y'}
      <div id="tiki-left">
      {section name=homeix loop=$left_modules}
      {$left_modules[homeix].data}
      {/section}
      </div>
      {/if}

      {if $prefs.feature_right_column eq 'y'}
      <div id="tiki-right">
      {section name=homeix loop=$right_modules}
      {$right_modules[homeix].data}
      {/section}      
      </div>
      {/if}


  {if $prefs.feature_bot_bar eq 'y'}
  <div id="tiki-bot">
    {include file="tiki-bot_bar.tpl"}
  </div>
  {/if}

{/if}
{if $prefs.feature_bidi eq 'y'}</td></tr></table>{/if}

{include file="footer.tpl"}
