<h1>{tr}Banner stats{/tr}</h1>
<a class="linkbut" href="tiki-list_banners.php">{tr}List banners{/tr}</a>
{if $tiki_p_admin_banners eq 'y'}
<a class="linkbut" href="tiki-edit_banner.php?bannerId={$bannerId}">{tr}Edit{/tr}</a>
<a class="linkbut" href="tiki-edit_banner.php">{tr}Create new banner{/tr}</a>
{/if}
<h2>{tr}Banner Information{/tr}</h2>
<div class="simplebox">
<table>
<tr>
  <td>{tr}Client{/tr}:</td>
  <td>{$client}</td>
</tr>
<tr>
  <td>{tr}URL{/tr}:</td>
  <td>{$url}</td>
</tr>
<tr>
  <td>{tr}Zone{/tr}:</td>
  <td>{$zone}</td>
</tr>
<tr>
  <td>{tr}Created{/tr}:</td>
  <td>{$created|tiki_short_date}</td>
</tr>
<tr>
  <td>{tr}Max Impressions{/tr}:</td>
  <td>{$maxImpressions}</td>
</tr>    
<tr>
  <td>{tr}Impressions{/tr}:</td>
  <td>{$impressions}</td>
</tr>
<tr>
  <td>{tr}Clicks{/tr}:</td>
  <td>{$clicks}</td>
</tr>
<tr>
  <td>{tr}Click ratio{/tr}:</td>
  <td>{$ctr}</td>
</tr>
<tr>
  <td>{tr}Method{/tr}:</td>
  <td>{$use}</td>
</tr>
{if $useDates eq 'y'}
<tr>
  <td>{tr}Use dates{/tr}:</td>
  <td>{tr}From{/tr}: {$fromDate|tiki_short_date} {tr}to{/tr}: {$toDate|tiki_short_date}
  </td>
</tr>
{/if}
<tr>
  <td>{tr}Hours{/tr}:</td>
  <td>{tr}From{/tr}: {$fromTime_h}:{$fromTime_m} {tr}to{/tr}: {$toTime_h}:{$toTime_m}</td>
</tr>
<tr>
  <td>{tr}Weekdays{/tr}:</td>
  <td>
    {if $Dmon eq 'y'} {tr}mon{/tr} {/if}
    {if $Dtue eq 'y'} {tr}tue{/tr} {/if}
    {if $Dwed eq 'y'} {tr}wed{/tr} {/if}
    {if $Dthu eq 'y'} {tr}thu{/tr} {/if}
    {if $Dfri eq 'y'} {tr}fri{/tr} {/if}
    {if $Dsat eq 'y'} {tr}sat{/tr} {/if}
    {if $Dsun eq 'y'} {tr}sun{/tr} {/if}
  </td>
</tr>
</table>
</div>
<h2>{tr}Banner raw data{/tr}</h2>
<div class="simplebox">
<div align="center">
{$raw}
</div>
</div>
