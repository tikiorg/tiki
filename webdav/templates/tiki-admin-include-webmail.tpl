<form action="tiki-admin.php?page=webmail" method="post">
	<div class="heading input_submit_container" style="text-align: right">
		<input type="submit" name="webmail" value="{tr}Change preferences{/tr}" />
	</div>
		<fieldset class="admin">
			<legend>{tr}Settings{/tr}</legend>
      <table class="admin">
        <tr>
          <td class="form">{tr}Allow viewing HTML mails?{/tr}</td>
          <td>
            <input type="checkbox" name="webmail_view_html" {if $prefs.webmail_view_html eq 'y'}checked="checked"{/if} />
          </td>
        </tr>
      
        <tr>
          <td class="form">{tr}Maximum size for each attachment{/tr}:</td>
          <td>
            <select name="webmail_max_attachment">
              <option value="500000" {if $prefs.webmail_max_attachment eq 500000}selected="selected"{/if}>500Kb</option>
              <option value="1000000" {if $prefs.webmail_max_attachment eq 1000000}selected="selected"{/if}>1Mb</option>
              <option value="1500000" {if $prefs.webmail_max_attachment eq 1500000}selected="selected"{/if}>1.5Mb</option>
              <option value="2000000" {if $prefs.webmail_max_attachment eq 2000000}selected="selected"{/if}>2Mb</option>
              <option value="2500000" {if $prefs.webmail_max_attachment eq 2500000}selected="selected"{/if}>2.5Mb</option>
              <option value="3000000" {if $prefs.webmail_max_attachment eq 3000000}selected="selected"{/if}>3Mb</option>
              <option value="100000000" {if $prefs.webmail_max_attachment eq 100000000}selected="selected"{/if}>{tr}Unlimited{/tr}</option>
            </select>
          </td>
        </tr>
   
        <tr>
          <td class="form">{tr}Include a flag by each e-mail to quickly flag/un-flag them?{/tr}</td>
          <td>
            <input type="checkbox" name="webmail_quick_flags" {if $prefs.webmail_quick_flags eq 'y'}checked="checked"{/if} />
          </td>
        </tr>
      </table>
		</fieldset>
	<div class="heading input_submit_container" style="text-align: center">
		<input type="submit" name="webmail" value="{tr}Change preferences{/tr}" />
	</div>
</form>
