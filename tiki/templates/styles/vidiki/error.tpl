{include file="header.tpl"}


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

      <div class="content">

        <div class="cbox">
        <div class="cbox-title">
        {$errortitle|default:"{tr}Error{/tr}"}
        </div>
        <div class="cbox-data">
        <br />
        {if ($errortype eq "404")}
          Perhaps you were looking for:
          <ul>
          {section name=back loop=$likepages}
          <li><a  href="tiki-index.php?page={$likepages[back]|escape:"url"}" class="wiki">{$likepages[back]}</a></li>
          {sectionelse}
          <li>{tr}There are no wiki pages similar to '{$page}'{/tr}</li>
          {/section}
          </ul>
          <br />
          {* include file="tiki-include-horizsearch.tpl" *}
        {else}
        {$msg}
        <br /><br />
        {/if}
        {if $page and !$nocreate and ($tiki_p_admin eq 'y' or $tiki_p_admin_wiki eq 'y')}<a href="tiki-editpage.php?page={$page}" class="linkmenu">{tr}Click here to create it{/tr}</a><br />{/if}
        <a href="javascript:history.back()" class="linkmenu">{tr}Go back{/tr}</a><br />
        <a href="{$tikiIndex}" class="linkmenu">{tr}Return to home page{/tr}</a>
        </div>
        </div>
      </div>

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

{include file="footer.tpl"}
