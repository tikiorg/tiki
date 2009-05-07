{if !empty($comment)}
{$comment}
{else}
{tr}Look at this link:{/tr}
{/if}
{$prefix}{$url|replace:' ':'+'}

{$name}
