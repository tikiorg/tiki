{section name=user loop=$items}
From: {$items[user].user_from}
To: {$items[user].user_to}
Cc: {$items[user].user_cc}
Bcc: {$items[user].user_bcc}
Subject: {$items[user].subject}
Date: {$items[user].date|tiki_short_datetime:'':"n"}
Message-ID: <{$items[user].hash}-{$items[user].date}>
X-Priority: {$items[user].priority}
X-Mailer: Tikiwiki
Content-Type: text/plain; charset="UTF-8"
Content-Transfer-Encoding: 8bit
Size: {$items[user].len}
{$items[user].parsed}
{sectionelse}
{tr}No messages to download{/tr}
{/section}