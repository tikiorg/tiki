{strip}
<a class="pagetitle" href="tiki-view_tracker_item.php?trackerId={$trackerId}&amp;itemId={$itemId}">
  {tr}Editing tracker item{/tr}
</a>

<br /><br />

<table summary="{tr}Editing tracker item{/tr}">
  <colgroup><col /><col /></colgroup>
  <tbody>
    <tr>
      <td class="button2"><a class="linkbut" href="tiki-list_trackers.php">{tr}List trackers{/tr}</a></td>
      {if $tiki_p_admin_trackers eq 'y'}
      <td class="button2"><a class="linkbut" href="tiki-admin_trackers.php">{tr}Admin trackers{/tr}</a></td>
      <td class="button2"><a class="linkbut" href="tiki-admin_trackers.php?trackerId={$trackerId}" title="{tr}Click here to edit this menu{/tr}"><img alt="{tr}Edit{/tr}" src="img/icons/edit.gif" /></a>
      </td>
      {if $user}
      <td class="button2"><a class="linkbut" href="tiki-view_tracker_item.php?itemId={$itemId}&amp;trackerId={$trackerId}&amp;monitor=1">{tr}{$email_mon}{/tr}</a></td>
      {/if}
      {/if}
      <td class="button2"><a class="linkbut" href="tiki-view_tracker.php?trackerId={$trackerId}">{tr}View this tracker items{/tr}</a></td>
    </tr>
  </tbody>
</table>

<br /><br />

{cycle name=tabs values="1,2,3,4,5" print=false advance=false}
<div class="tabs">
  <span class="tabActive" id="tab{cycle name=tabs}">{tr}View{/tr}</span>
  {if $tracker_info.useComments eq 'y'}
  <span class="tab" id="tab{cycle name=tabs}">{tr}Comments{/tr}</span>
  {/if}
  {if $tracker_info.useAttachments eq 'y'}
  <span class="tab" id="tab{cycle name=tabs}">{tr}Attachements{/tr}</span>
  {/if}
  {if $tiki_p_modify_tracker_items eq 'y'}
  <span class="tab" id="tab{cycle name=tabs}">{tr}Edit{/tr}</span>
  {/if}
</div>

{* <!-- tab with view --> *}
{cycle name=content values="1,2,3,4,5" print=false advance=false}
<div id="content{cycle name=content}" class="content">
  <h3>{tr}View item{/tr}</h3>
  <table summary="">
    <colgroup><col /><col /></colgroup>
    <tbody>
      {section name=ix loop=$ins_fields}
      <tr>
        <td>{$ins_fields[ix].name}</td>
        <td>{$ins_fields[ix].value}</td>
      </tr>
      {/section}
    </tbody>
  </table>
</div>

{* <!-- tab with comments --> *}
{if $tracker_info.useComments eq 'y'}
<div id="content{cycle name=content}" class="content">
  {if $tiki_p_comment_tracker_items eq 'y'}

  <h3>{tr}Add a comment{/tr}</h3>

  <form action="tiki-view_tracker_item.php" method="post">
    <fieldset>
      <legend>{tr}Add a comment{/tr}</legend>
      <input type="hidden" name="trackerId" value="{$trackerId|escape}" />
      <input type="hidden" name="itemId" value="{$itemId|escape}" />
      <input type="hidden" name="commentId" value="{$commentId|escape}" />
      <table summary="">
        <colgroup><col /><col /></colgroup>
        <tbody>
          <tr>
            <td>{tr}Title{/tr}:</td>
            <td><input type="text" name="comment_title" value="{$comment_title|escape}"/></td>
          </tr>
          <tr>
            <td>{tr}Comment{/tr}:</td>
            <td><textarea class="textarea" rows="4" cols="50" name="comment_data">{$comment_data|escape}</textarea></td>
          </tr>
          <tr>
            <td>&nbsp;</td><td><input type="submit" name="save_comment" value="{tr}save{/tr}" /></td>
          </tr>
        </tbody>
      </table>
    </fieldset>
  </form>
  {/if}

  <h3>{tr}Comments{/tr}</h3>

  {section name=ix loop=$comments}
  <strong>{$comments[ix].title}</strong> {if $comments[ix].user}{tr}by{/tr} {$comments[ix].user}{/if}
  {if $tiki_p_admin_trackers eq 'y'}
  [
  <a
    class="link"
    href="tiki-view_tracker_item.php?trackerId={$trackerId}&amp;itemId={$itemId}&amp;commentId={$comments[ix].commentId}"
    title="{tr}Edit{/tr}">
    <img alt="{tr}Edit{/tr}" src="img/icons/edit.gif" />
  </a>
  |
  <a
    class="link" href="tiki-view_tracker_item.php?trackerId={$trackerId}&amp;itemId={$itemId}&amp;remove_comment={$comments[ix].commentId}"
    onclick="return confirmTheLink(this,'{tr}Are you sure you want to delete this comment?{/tr}')"
    title="{tr}Click here to delete this comment{/tr}">
    <img alt="{tr}Remove{/tr}" src="img/icons2/delete.gif" hspace="8" />
  </a>
  ]
  {/if}

  <br />

  <em>{tr}posted on{/tr}: {$comments[ix].posted|tiki_short_datetime}</em><br />
  {$comments[ix].parsed}

  <hr />

  {/section}

</div>
{/if}

{* <!-- tab with attachements --> *}
{if $tracker_info.useAttachments eq 'y'}
<div class="content" id="content{cycle name=content}">
  {if $tiki_p_attach_trackers eq 'y'}
  <h3>{tr}Attach a file to this item{/tr}</h3>
  <form enctype="multipart/form-data" action="tiki-view_tracker_item.php" method="post">
    <fieldset>
      <legend></legend>
      <input type="hidden" name="trackerId" value="{$trackerId|escape}" />
      <input type="hidden" name="itemId" value="{$itemId|escape}" />
      <input type="hidden" name="commentId" value="{$commentId|escape}" />
      <table summary="{tr}Attach a file to this item{/tr}">
        <colgroup><col /></colgroup>
        <tbody>
          <tr>
            <td>
              {tr}Upload file{/tr}:
              <input type="hidden" name="MAX_FILE_SIZE" value="1000000000" />
              <input name="userfile1" type="file" />
              {tr}comment{/tr}:
              <input type="text" name="attach_comment" maxlength="250" />
              <input type="submit" name="attach" value="{tr}attach{/tr}" />
            </td>
          </tr>
        </tbody>
      </table>
    </fieldset>
  </form>
{/if}

<h3>{tr}Attachments{/tr}</h3>
  <table summary="{tr}Attachments{/tr}">
    <colgroup><col /><col /><col /><col /><col /></colgroup>
    <tbody>
      <tr>
        <th>{tr}name{/tr}</th>
        <th>{tr}uploaded{/tr}</th>
        <th>{tr}size{/tr}</th>
        <th>{tr}dls{/tr}</th>
        <th>{tr}desc{/tr}</th>
      </tr>
      {cycle values="odd,even" print=false}
      {section name=ix loop=$atts}
      <tr>
        <td class="{cycle advance=false}">
          {$atts[ix].filename|iconify}
          <a class="tablename" href="tiki-download_item_attachment.php?attId={$atts[ix].attId}">{$atts[ix].filename}</a>
          {if $tiki_p_wiki_admin_attachments eq 'y' or ($user and ($atts[ix].user eq $user))}
          <a class="link" href="tiki-view_tracker_item.php?trackerId={$trackerId}&amp;itemId={$itemId}&amp;removeattach={$atts[ix].attId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}">[x]</a>
          {/if}
        </td>
        <td class="{cycle advance=false}">{$atts[ix].created|tiki_short_datetime}{if $atts[ix].user} {tr}by{/tr} {$atts[ix].user}{/if}</td>
        <td class="{cycle advance=false}">{$atts[ix].filesize}</td>
        <td class="{cycle advance=false}">{$atts[ix].downloads}</td>
        <td class="{cycle}">{$atts[ix].comment}</td>
      </tr>
      {sectionelse}
      <tr>
        <td colspan="5">{tr}No attachments for this item{/tr}</td>
      </tr>
      {/section}
    </tbody>
  </table>
</div>
{/if}

{* --- tab with edit --- *}
{if $tiki_p_modify_tracker_items eq 'y'}
<div class="content" id="content{cycle name=content}">
  <h3>{tr}Edit item{/tr}</h3>
  <form action="tiki-view_tracker_item.php" method="post">
    <fieldset>
      <legend>{tr}Edit item{/tr}</legend>
      <input type="hidden" name="trackerId" value="{$trackerId|escape}" />
      <input type="hidden" name="itemId" value="{$itemId|escape}" />
      {section name=ix loop=$fields}
      <input type="hidden" name="{$fields[ix].name|escape}" value="{$fields[ix].value|escape}" />
      {/section}
      <table summary="{tr}Edit item{/tr}">
        <colgroup><col /><col /></colgroup>
        <tbody>
          <tr>
            <td>{tr}Status{/tr}</td>
            <td>
              <select name="status">
                <option value="o" {if $item_info.status eq 'o'}selected="selected"{/if}>{tr}open{/tr}</option>
                <option value="c" {if $item_info.status eq 'c'}selected="selected"{/if}>{tr}closed{/tr}</option>
              </select>
            </td>
          </tr>
          {section name=ix loop=$ins_fields}
          <tr>
            <td>{$ins_fields[ix].name}</td>
            <td>
              {if $ins_fields[ix].type eq 'u'}
              <select name="ins_{$ins_fields[ix].name}">
                <option value="">{tr}None{/tr}</option>
                {section name=ux loop=$users}
                <option value="{$users[ux]|escape}" {if $ins_fields[ix].value eq $users[ux]}selected="selected"{/if}>{$users[ux]}</option>
                {/section}
              </select>
              {/if}
              {if $ins_fields[ix].type eq 'g'}
              <select name="ins_{$ins_fields[ix].name}">
                <option value="">{tr}None{/tr}</option>
                {section name=ux loop=$groups}
                <option value="{$groups[ux].groupName|escape}" {if $ins_fields[ix].value eq $groups[ux].groupName}selected="selected"{/if}>{$groups[ux].groupName}</option>
                {/section}
              </select>
              {/if}
              {if $ins_fields[ix].type eq 't'}
              <input class="text" type="text" name="ins_{$ins_fields[ix].name}" value="{$ins_fields[ix].value|escape}" />
              {/if}
              {if $ins_fields[ix].type eq 'a'}
              <textarea class="textarea" name="ins_{$ins_fields[ix].name}" rows="4" cols="50">{$ins_fields[ix].value|escape}</textarea>
              {/if}
              {if $ins_fields[ix].type eq 'f'}
              {html_select_date prefix=$ins_fields[ix].ins_name time=$ins_fields[ix].value end_year="+1"} at {html_select_time prefix=$ins_fields[ix].ins_name time=$ins_fields[ix].value display_seconds=false}
              {/if}
              {if $ins_fields[ix].type eq 'd'}
              <select name="ins_{$ins_fields[ix].name}">
              {section name=jx loop=$ins_fields[ix].options_array}
                <option value="{$ins_fields[ix].options_array[jx]|escape}" {if $ins_fields[ix].value eq $ins_fields[ix].options_array[jx]}selected="selected"{/if}>{$fields[ix].options_array[jx]}</option>
              {/section}
              </select>
              {/if}
              {if $ins_fields[ix].type eq 'c'}
              <input type="checkbox" name="ins_{$ins_fields[ix].name}" {if $ins_fields[ix].value eq 'y'}checked="checked"{/if}/>
              {/if}
            </td>
          </tr>
          {/section}
          <tr>
            <td>&nbsp;</td>
            <td>
              <input type="submit" name="save" value="{tr}save{/tr}" />
            </td>
          </tr>
        </tbody>
      </table>
    </fieldset>
  </form>
</div>
{/if}

<br /><br />
{/strip}