<comment>
	<title><![CDATA[{$comment.title}]]></title>
	<user><![CDATA[{$comment.userName}]]></user>
	<date>{$comment.commentDate}</date>
	<data><![CDATA[{$comment.data}]]></data>
	{foreach from=$comment.replies_info.replies item=comment}
		{include file='tiki-export_comment_xml.tpl'}
	{/foreach}
</comment>
