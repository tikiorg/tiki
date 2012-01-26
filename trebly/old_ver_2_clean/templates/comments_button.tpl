{* $Id$ *}
{if $comments_cant gt 0}
	{assign var=thisbuttonclass value='highlight'}
{else}
	{assign var=thisbuttonclass value=''}
{/if}
{if $comments_cant == 0 or ($tiki_p_read_comments == 'n' and $tiki_p_post_comments == 'y')}
	{assign var=thistext value="{tr}Add Comment{/tr}"}
{elseif $comments_cant == 1}
	{assign var=thistext value="{tr}1 comment{/tr}"}
{else}
	{assign var=thistext value="$comments_cant&nbsp;{tr}Comments{/tr}"}
{/if}
{if isset($pagemd5)}
	{assign var=thisflipid value="comzone$pagemd5"}
{else}
	{assign var=thisflipid value="comzone"}
{/if}
{if $comments_show eq 'y' or $show_comzone eq 'y'}
	{assign var=flip_open value='y'}
<noscript>
	{button comzone="hide" _anchor="comments" _auto_args="comzone,*" _class=$thisbuttonclass _text=$thistext _flip_hide_text='y' _flip_default_open=$flip_open}
</noscript>
{elseif $comments_show eq 'n'}
	{assign var=flip_open value='n'}
<noscript>
	{button comzone="show" _anchor="comments" _auto_args="comzone,*" _class=$thisbuttonclass _text=$thistext _flip_hide_text='n' _flip_default_open=$flip_open}
</noscript>
{else}
	{assign var=flip_open value=$prefs.wiki_comments_displayed_default}
<noscript>
	{button comzone="show" _anchor="comments" _auto_args="comzone,*" _class=$thisbuttonclass _text=$thistext _flip_hide_text='n' _flip_default_open=$flip_open}
</noscript>
{/if}
{if $prefs.javascript_enabled eq "y"}
	{button href="#comments" _auto_args="*" _flip_id=$thisflipid _class=$thisbuttonclass _text=$thistext _flip_default_open=$flip_open}
{else}
	{button href="#comments" _auto_args="*" _class=$thisbuttonclass _text=$thistext}
{/if}
