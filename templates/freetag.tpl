{if $feature_freetags eq 'y'}

<script type="text/javascript">
{literal}
  function addTag(tag) {
    document.getElementById('tagBox').value = document.getElementById('tagBox').value + ' ' + tag;
  }
{/literal}
</script>
<tr class="formcolor">
<td>{tr}Folksonomy Tags{/tr}</td>
<td{if $cols} colspan="{$cols}"{/if}>
<div id="freetager">
{if $feature_help eq 'y'}
<div class="simplebox">{tr}Put tags separated by spaces. For tags with more than one word, use no spaces and put words together.{/tr}</div>
{/if}

<input type="text" id="tagBox" name="freetag_string" value="{$taglist|escape}" size="60" /><br />
{foreach from=$tag_suggestion item=t}
<a href="javascript:addTag('{$t|escape:'javascript'}');">{$t}</a> 
{/foreach}

  </div>
  </td>
</tr>

{/if}{* $feature_freetags eq 'y' *}
