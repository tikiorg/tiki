{if !empty($comment)}
{$comment}
{else}
{tr}Look at this link:{/tr}
{/if}
{$url_for_friend|replace:' ':'+'}
-
{$name|username}
{$email}
