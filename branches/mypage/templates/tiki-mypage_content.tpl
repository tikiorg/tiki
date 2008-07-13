<div style='width: {$mypage_width}; height: {$mypage_height};'>
 <div id='mypage' style='position: absolute; width: {$mypage_width}; height: {$mypage_height}; background: {$mypage_bgcolor}; overflow: hidden;'>
  {if $editit}{include file='tiki-mypage_sidebar.tpl'}{/if}
 </div>
</div>

<span style="float: right">
{if !$editit}
<a href="tiki-mypage.php?mypage={$pagename|escape:url}&edit=1">Edit</a>
{else}
<a href="tiki-mypage.php?mypage={$pagename|escape:url}">View</a>
{/if}
{if $tiki_p_admin eq 'y'} | <a href="tiki-mypage_types.php">Admin Types</a>{/if}
</span>
