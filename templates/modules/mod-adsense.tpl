{* $Id$ *}
<div style="text-align: center">
{if $client}
{capture name=disparam}
{if $display == '728*90_as'}
    google_ad_width = 728;
    google_ad_height = 90;
    google_ad_format = "728x90_as";
{elseif $display == '468*60_as'}
    google_ad_width = 468;
    google_ad_height = 60;
    google_ad_format = "468x60_as";
{elseif $display == '300*250_as'}
    google_ad_width = 300;
    google_ad_height = 250;
    google_ad_format = "300x250_as";
{elseif $display == '160*600_as'}
    google_ad_width = 160;
    google_ad_height = 600;
    google_ad_format = "160x600_as";
{elseif $display == '120*600_as'}
    google_ad_width = 120;
    google_ad_height = 600;
    google_ad_format = "120x600_as";
{elseif $display == '336*280_as'}
    google_ad_width = 336;
    google_ad_height = 280;
    google_ad_format = "336x280_as";
{elseif $display == '250*250_as'}
    google_ad_width = 250;
    google_ad_height = 250;
    google_ad_format = "250x250_as";
{elseif $display == '234*60_as'}
    google_ad_width = 234;
    google_ad_height = 60;
    google_ad_format = "234x60_as";
{elseif $display == '180*150_as'}
    google_ad_width = 180;
    google_ad_height = 150;
    google_ad_format = "180x150_as";
{elseif $display == '125*125_as'}
    google_ad_width = 125;
    google_ad_height = 125;
    google_ad_format = "125x125_as";
{elseif $display == '120*140_as'}
    google_ad_width = 120;
    google_ad_height = 140;
    google_ad_format = "120x140_as";
{elseif $display == '120*240_as'}
    google_ad_width = 120;
    google_ad_height = 240;
    google_ad_format = "120x240_as";
{elseif $display == '200*200_as'}
    google_ad_width = 200;
    google_ad_height = 200;
    google_ad_format = "200x200_as";
{/if}
{/capture}
{if $smarty.capture.disparam}
<script type="text/javascript">
{$smarty.capture.disparam}
    google_ad_client = "{$client}";
    google_ad_type = "text_image";
    google_ad_channel ="{$ad_channel}";
    google_color_border = "{$color_border}";
    google_color_bg = "{$color_bg}";
    google_color_link = "{$color_link}";
    google_color_url = "{$color_url}";
    google_color_text = "{$color_text}";
</script>
<script type="text/javascript"
  src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
{else}{tr}Display type unknown, you have to enter the banner type{/tr}{/if}
{else}{tr}You forgot your Google ad_client number !{/tr}{/if}
</div>
