{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}To configure your trackers, look for "Admin trackers" under "Trackers" on the application menu, or{/tr} <a class="rbox-link" href="tiki-admin_trackers.php">{tr}Click Here{/tr}</a>.{/remarksbox}

<div class="cbox">
  <div class="cbox-title">{tr}Trackers attachments preferences{/tr}</div>

  <div class="cbox-data">
    <form action="tiki-admin.php?page=trackers" method="post">
      <table class="admin">
        <tr>
          <td class="form">
            {tr}Use database to store files{/tr}:
          </td>
          <td>
            <input type="radio" name="t_use_db" value="y" {if $prefs.t_use_db eq 'y'}checked="checked"{/if}/>
          </td>
        </tr>

        <tr>
          <td class="form">
            {tr}Use a directory to store files{/tr}:</td>
          <td>
            <input type="radio" name="t_use_db" value="n" {if $prefs.t_use_db eq 'n'}checked="checked"{/if}/> {tr}Path{/tr}:
            <br />
            <input type="text" name="t_use_dir" value="{$prefs.t_use_dir|escape}" size="50" /> 
          </td>
        </tr>

        <tr>
          <td colspan="2" class="button">
            <input type="submit" name="trkset" value="{tr}Change preferences{/tr}" />
          </td>
        </tr>    
      </table>
    </form>
  </div>
</div>

<div class="cbox">
  <div class="cbox-title">{tr}Trackers with mirror tables{/tr}</div>
  <div class="cbox-data">
    <form action="tiki-admin.php?page=trackers" method="post">
      <table class="admin">
        <tr>
          <td class="form">{tr}Use trackers with mirror tables{/tr}:</td>
          <td>
            <input type="checkbox" name="trk_with_mirror_tables" {if $prefs.trk_with_mirror_tables eq 'y'}checked="checked"{/if}/>
          </td>
        </tr>
        
        <tr>
          <td class="form">{tr}Values are stored in a dedicated table for each tracker, then you can easily manipulate them outside Tikiwiki{/tr}</td>
          <td class="form">{tr}WARNING : Old and New trackers cannot coexist. Trackers created with new library will not work with old library and conversely{/tr}.</td>
        </tr>

        <tr>
          <td colspan="2" class="button">
            <input type="submit" name="trkMirrorTables" value="{tr}Validate choice{/tr}" />
          </td>
        </tr>    
      </table>
    </form>
  </div>
</div>

<div class="cbox">
  <div class="cbox-title">{tr}Trackers attachments{/tr}</div>
  <div class="cbox-data">
    <div class="admin">
{if $attachments}
      <form action="tiki-admin.php?page=trackers" method="post">
        <input type="text" name="find" value="{$find|escape}" />
        <input type="submit" name="action" value="{tr}Find{/tr}" />
      </form>
{/if}
      {cycle values="odd,even" print=false}
      <table class="normal">
        <tr>
          <td class="heading">
            <a href="tiki-admin.php?page=trackers&amp;sort_mode=user_{if $sort_mode eq 'user'}asc{else}desc{/if}" class="heading">{tr}User{/tr}</a>
          </td>
          <td class="heading">
            <a href="tiki-admin.php?page=trackers&amp;sort_mode=filename_{if $sort_mode eq 'filename'}asc{else}desc{/if}" class="heading">{tr}Name{/tr}</a>
          </td>
          <td class="heading">
            <a href="tiki-admin.php?page=trackers&amp;sort_mode=filesize_{if $sort_mode eq 'filesize'}asc{else}desc{/if}" class="heading">{tr}Size{/tr}</a>
          </td>
          <td class="heading">
            <a href="tiki-admin.php?page=trackers&amp;sort_mode=filetype_{if $sort_mode eq 'filetype'}asc{else}desc{/if}" class="heading">{tr}Type{/tr}</a>
          </td>
          <td class="heading">
            <a href="tiki-admin.php?page=trackers&amp;sort_mode=hits_{if $sort_mode eq 'hits'}asc{else}desc{/if}" class="heading">{tr}dls{/tr}</a>
          </td>
          <td class="heading">
            <a href="tiki-admin.php?page=trackers&amp;sort_mode=itemId_{if $sort_mode eq 'itemId'}asc{else}desc{/if}" class="heading">{tr}Item{/tr}</a>
          </td>
          <td class="heading">
            <a href="tiki-admin.php?page=trackers&amp;sort_mode=path_{if $sort_mode eq 'path'}asc{else}desc{/if}" class="heading">{tr}Storage{/tr}</a>
          </td>
          <td class="heading">
            <a href="tiki-admin.php?page=trackers&amp;sort_mode=created_{if $sort_mode eq 'created'}asc{else}desc{/if}" class="heading">{tr}Created{/tr}</a>
          </td>
          <td class="heading">&nbsp;</td>
        </tr>
        
        {section name=x loop=$attachements}
        <tr class={cycle}>
          <td>{$attachements[x].user}</td>
          <td>{$attachements[x].filename}</td>
          <td>{$attachements[x].filesize|kbsize}</td>
          <td>{$attachements[x].filetype}</td>
          <td>{$attachements[x].hits}</td>
          <td>{$attachements[x].itemId}</td>
          <td>{if $attachements[x].path}file{else}db{/if}</td>
          <td>{$attachements[x].created|tiki_short_date}</td>
          <td>
            <a href="tiki-admin.php?page=trackers&amp;attId={$attachements[x].attId}&amp;action={if $attachements[x].path}move2db{else}move2file{/if}">{tr}Change{/tr}</a>
          </td>
        </tr>
{sectionelse}
	<tr class="odd"><td colspan="9">{tr}No records{/tr}</td></tr>
        {/section}
      </table>
      
    {include file=tiki-pagination.tpl}
    </div>
{if $attachments}    
    <table>
      <tr>
        <td>
          <form action="tiki-admin.php?page=trackers" method="post">
            <input type="hidden" name="all2db" value="1" />
            <input type="submit" name="action" value="{tr}Change all to db{/tr}" />
          </form>
        </td>
        <td>
          <form action="tiki-admin.php?page=trackers" method="post">
            <input type="hidden" name="all2file" value="1" />
            <input type="submit" name="action" value="{tr}Change all to file{/tr}" />
          </form>
        </td>
      </tr>
    </table>
{/if}
<br />
  </div>
</div>
