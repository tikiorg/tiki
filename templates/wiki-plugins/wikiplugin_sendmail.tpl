<div>
    <p><input name="sendmailload" type="submit" class="btn btn-default" value="{$label_name|escape}" /></p>
    <div style="display: none;">
        <div id="sendmail" style="text-align: center;">
            <div class="sendmail" style="display: inline-block;">
                <h2 style="text-align: left;">{tr}Mail content{/tr}</h2>
                <form action="{$smarty.server.PHP_SELF}?{query}" method="post">
                    <textarea cols="60" rows="15" name="bodycontent"></textarea>
                    <p style="text-align: right;"><input type="submit" name="sendall" class="btn btn-default" value="{$label_name|escape}" /></p>
                </form>
                <p>{tr}You will receive a copy of the email yourself. Please give it a few minutes.{/tr}</p>
            </div>
        </div>
    </div>
</div>
{jq}
$("input[name='sendmailload']").click(function() {
	$.colorbox({overlayClose: false, width:"620px", inline:true, href:"#sendmail"});
	return false;
});

$("input[name='sendall']").click(function(){
	if($("textarea[name='bodycontent']").val()) {
		var mailform = $(this).closest('form');
		mailform.modal("Your email is being sent");
	    var postData = mailform.serializeArray();
	    var formURL = mailform.attr('action');
	    $.ajax({
	        url : formURL,
	        type: "POST",
	        data : postData,
	        success:function(data, textStatus, jqXHR) {
	        	mailform.modal('');
	        	$.colorbox.close();
	        }
	    });
	}
	return false;
});

{/jq}
