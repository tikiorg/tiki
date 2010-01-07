{if $prefs.feature_freetags eq 'y' && $tiki_p_freetags_tag eq 'y'}
	<tr class="formcolor">
		<td><label for="tagBox">{tr}Folksonomy Tags{/tr}</label></td>
		<td>
			<script type="text/javascript">
				<!--//--><![CDATA[//><!--
				{literal}
					function addTag(tag) {
						document.getElementById('tagBox').value = document.getElementById('tagBox').value + ' ' + tag;
					}
				{/literal}
				//--><!]]>
			</script>
			<div id="freetager">
				{if $prefs.feature_help eq 'y'}
					{tr}Put tags separated by spaces. For tags with more than one word, use no spaces and put words together or enclose them with double quotes.{/tr}
				{/if}

				<input type="text" id="tagBox" name="freetag_string" value="{$taglist|escape}" style="width:98%" />
				<br />
				{foreach from=$tag_suggestion item=t}
					{capture name=tagurl}{if (strstr($t, ' '))}"{$t}"{else}{$t}{/if}{/capture}
					<a href="javascript:addTag('{$smarty.capture.tagurl|escape:'javascript'|escape}');" onclick="javascript:needToConfirm=false">{$t|escape}</a> 
				{/foreach}
			</div>
		</td>
	</tr>
{/if}
