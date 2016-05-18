{* $Id$ *}{$prefs.mail_template_custom_text}{tr}Gift Certificate Information{/tr}

{section name=cert loop=$giftcerts}
{tr}Name:{/tr} {$giftcerts[cert].name|escape}
{tr}Description:{/tr} {$giftcerts[cert].longDescription|escape}
{if $giftcerts[cert].isPercentage}
{tr}Percentage Discount{/tr}: {$giftcerts[cert].value|escape}%
{else}
{tr}Value of Gift Certificate:{/tr} ${$giftcerts[cert].value|escape}
{/if}
{tr}Redeem Code:{/tr} {$giftcerts[cert].redeemCode|escape}

{/section}
