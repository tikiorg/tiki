{if $nlArticleClipTitle}<h3><a href="{$base_url}{$nlArticleClipId|sefurl:'article'}">{$nlArticleClipTitle|escape}</a></h3>{/if}
{if $nlArticleClipSubtitle}<h5>{$nlArticleClipSubtitle|escape}</h5>{/if}
{if $nlArticleClipPublishDate}<p>{tr}Published:{/tr} {$nlArticleClipPublishDate|tiki_short_datetime}</p>{/if}
{if $nlArticleClipAuthorName}<p>{tr}By:{/tr} {$nlArticleClipAuthorName|username}</p>{/if}
{if $nlArticleClipParsedheading}<div>{$nlArticleClipParsedheading}</div>{/if}