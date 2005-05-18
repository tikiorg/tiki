{include file="header.tpl"}

{if $feature_bidi eq 'y'}<table dir="rtl" ><tr><td>{/if}

{* Index we display a wiki page here *}

 {if $feature_top_bar eq 'y'} 
 <div class="tiki-top">  <div class="title">{include file="tiki-top_bar.tpl"} 
<br />
  <a href="tiki-index.php" class="titlefont">
  {$siteTitle}
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

      <div class="content">{include file=$mid}
      {if $show_page_bar eq 'y'}
      {include file="tiki-page_bar.tpl"}

	{/if}
      </div>

      {if $feature_left_column eq 'y'}
      <div id="tiki-left">
      {section name=homeix loop=$left_modules}
      {$left_modules[homeix].data}
      {/section}
      </div>
      {/if}

      {if $feature_right_column eq 'y'}
      <div id="tiki-right">
      {section name=homeix loop=$right_modules}
      {$right_modules[homeix].data}
      {/section}      
      </div>
      {/if}


  {if $feature_bot_bar eq 'y'}
  <div id="tiki-bot">
    {include file="tiki-bot_bar.tpl"}
  </div>
  {/if}

{if $feature_bidi eq 'y'}</td></tr></table>{/if}

{include file="footer.tpl"}
