<?php

// Copyright (c) 2002-2008, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Function created 2008-07-14 SEWilco (scot@wilcoxon.org)
// 2009-01-12 SEWilco (scot@wilcoxon.org) Modified for feature_sefurl_filter.
function filter_out_sefurl($tpl_output, &$smarty)
{

  // Notes:
  // When testing for a quote in a URL also test for an apostrophe,
  // because TikiWiki sometimes uses one and sometimes the other.

  $pc = 0;    // pattern counter, for easier editing of following

  // Direct one-word access
  $patterns[$pc] = '/href=("|\')tiki-calendar.php("|\')/';
  $replacements[$pc++] = 'href=\1calendar\2';
  $patterns[$pc] = '/href=("|\')tiki-view_articles.php("|\')/';
  $replacements[$pc++] = 'href=\1articles\2';
  $patterns[$pc] = '/href=("|\')tiki-list_blogs.php("|\')/';
  $replacements[$pc++] = 'href=\1blogs\2';
  $patterns[$pc] = '/href=("|\')tiki-browse_categories.php("|\')/';
  $replacements[$pc++] = 'href=\1categories\2';
  $patterns[$pc] = '/href=("|\')tiki-list_charts.php("|\')/';
  $replacements[$pc++] = 'href=\1charts\2';
  $patterns[$pc] = '/href=("|\')tiki-chat.php("|\')/';
  $replacements[$pc++] = 'href=\1chat\2';
  $patterns[$pc] = '/href=("|\')tiki-contact.php("|\')/';
  $replacements[$pc++] = 'href=\1contact\2';
  $patterns[$pc] = '/href=("|\')tiki-directory_browse.php("|\')/';
  $replacements[$pc++] = 'href=\1directories\2';
  $patterns[$pc] = '/href=("|\')tiki-eph.php("|\')/';
  $replacements[$pc++] = 'href=\1eph\2';
  $patterns[$pc] = '/href=("|\')tiki-list_faqs.php("|\')/';
  $replacements[$pc++] = 'href=\1faqs\2';
  $patterns[$pc] = '/href=("|\')tiki-file_galleries.php("|\')/';
  $replacements[$pc++] = 'href=\1files\2';
  $patterns[$pc] = '/href=("|\')tiki-forums.php("|\')/';
  $replacements[$pc++] = 'href=\1forums\2';
  $patterns[$pc] = '/href=("|\')tiki-galleries.php("|\')/';
  $replacements[$pc++] = 'href=\1galleries\2';
  $patterns[$pc] = '/href=("|\')tiki-list_games.php("|\')/';
  $replacements[$pc++] = 'href=\1games\2';
  $patterns[$pc] = '/href=("|\')tiki-login_scr.php("|\')/';
  $replacements[$pc++] = 'href=\1login\2';
  $patterns[$pc] = '/href=("|\')tiki-my_tiki.php("|\')/';
  $replacements[$pc++] = 'href=\1my\2';
  $patterns[$pc] = '/href=("|\')tiki-newsletters.php("|\')/';
  $replacements[$pc++] = 'href=\1newsletters\2';
  $patterns[$pc] = '/href=("|\')tiki-list_quizzes.php("|\')/';
  $replacements[$pc++] = 'href=\1quizzes\2';
  $patterns[$pc] = '/href=("|\')tiki-stats.php("|\')/';
  $replacements[$pc++] = 'href=\1stats\2';
  $patterns[$pc] = '/href=("|\')tiki-list_surveys.php("|\')/';
  $replacements[$pc++] = 'href=\1surveys\2';
  $patterns[$pc] = '/href=("|\')tiki-list_trackers.php("|\')/';
  $replacements[$pc++] = 'href=\1trackers\2';
  $patterns[$pc] = '/href=("|\')tiki-irc_logs.php("|\')/';
  $replacements[$pc++] = 'href=\1irc\2';
  $patterns[$pc] = '/href=("|\')tiki-mobile.php("|\')/';
  $replacements[$pc++] = 'href=\1mobile\2';
  $patterns[$pc] = '/href=("|\')tiki-sheets.php("|\')/';
  $replacements[$pc++] = 'href=\1sheets\2';

  // Wiki pages
  $patterns[$pc] = '/href=("|\')tiki-index.php\?page=(.+)("|\')/';
  $replacements[$pc++] = 'href=\1\2\3';

  // Other kinds of pages
  $patterns[$pc] = '/href=("|\')tiki-read_article.php\?articleId=(\d+)("|\')/';
  $replacements[$pc++] = 'href=\1article\2\3';
  $patterns[$pc] = '/href=("|\')tiki-browse_categories.php\?parentId=(\d+)("|\')/';
  $replacements[$pc++] = 'href=\1cat\2\3';
  $patterns[$pc] = '/href=("|\')tiki-view_blog.php\?blogId=(\d+)("|\')/';
  $replacements[$pc++] = 'href=\1blog\2\3';
  $patterns[$pc] = '/href=("|\')tiki-view_blog_post.php\?postId=(\d+)("|\')/';
  $replacements[$pc++] = 'href=\1blogpost\2\3';
  $patterns[$pc] = '/href=("|\')tiki-browse_image.php\?imageId=(\d+)("|\')/';
  $replacements[$pc++] = 'href=\1browseimage\2\3';
  $patterns[$pc] = '/href=("|\')tiki-view_chart.php\?chartId=(\d+)("|\')/';
  $replacements[$pc++] = 'href=\1chart\2\3';
  $patterns[$pc] = '/href=("|\')tiki-directory_browse.php\?parent=(\d+)("|\')/';
  $replacements[$pc++] = 'href=\1directory\2\3';
  $patterns[$pc] = '/href=("|\')tiki-view_eph.php\?ephId=(\d+)("|\')/';
  $replacements[$pc++] = 'href=\1eph\2\3';
  $patterns[$pc] = '/href=("|\')tiki-view_faq.php\?faqId=(\d+)("|\')/';
  $replacements[$pc++] = 'href=\1faq\2\3';
  $patterns[$pc] = '/href=("|\')tiki-list_file_gallery.php\?galleryId=(\d+)("|\')/';
  $replacements[$pc++] = 'href=\1file\2\3';
  $patterns[$pc] = '/href=("|\')tiki-download_file.php\?fileId=(\d+)("|\')/';
  $replacements[$pc++] = 'href=\1dl\2\3';
  $patterns[$pc] = '/href=("|\')tiki-view_forum.php\?forumId=(\d+)("|\')/';
  $replacements[$pc++] = 'href=\1forum\2\3';
  $patterns[$pc] = '/href=("|\')tiki-browse_gallery.php\?galleryId=(\d+)("|\')/';
  $replacements[$pc++] = 'href=\1gallery\2\3';
  $patterns[$pc] = '/href=("|\')show_image.php\?id=(\d+)("|\')/';
  $replacements[$pc++] = 'href=\1image\2\3';
  $patterns[$pc] = '/href=("|\')show_image.php\?id=(\d+)&scalesize=(\d+)("|\')/';
  $replacements[$pc++] = 'href=\1imagescale\2/\3\4';
  $patterns[$pc] = '/href=("|\')tiki-newsletters.php\?nlId=(\d+)("|\')/';
  $replacements[$pc++] = 'href=\1newsletter\2\3';
  $patterns[$pc] = '/href=("|\')tiki-take_quiz.php\?quizId=(\d+)("|\')/';
  $replacements[$pc++] = 'href=\1quiz\2\3';
  $patterns[$pc] = '/href=("|\')tiki-take_survey.php\?surveyId=(\d+)("|\')/';
  $replacements[$pc++] = 'href=\1survey\2\3';
  $patterns[$pc] = '/href=("|\')tiki-view_tracker.php\?trackerId=(\d+)("|\')/';
  $replacements[$pc++] = 'href=\1tracker\2\3';
  $patterns[$pc] = '/href=("|\')tiki-index.php\?page=(\d+)(\d+)("|\')/';
  $replacements[$pc++] = 'href=\1wiki\2\2\3';
  $patterns[$pc] = '/href=("|\')tiki-irc_logs.php\?focus=(\d+)("|\')/';
  $replacements[$pc++] = 'href=\1irc\2\3';
  $patterns[$pc] = '/href=("|\')tiki-integrator.php\?repID=(\d+)("|\')/';
  $replacements[$pc++] = 'href=\1int\2\3';
  $patterns[$pc] = '/href=("|\')tiki-view_sheets.php\?sheetId=(\d+)("|\')/';
  $replacements[$pc++] = 'href=\1sheet\2\3';
  $patterns[$pc] = '/href=("|\')tiki-directory_redirect.php\?siteId=(\d+)("|\')/';
  $replacements[$pc++] = 'href=\1dirlink\2\3';
  $patterns[$pc] = '/href=("|\')tiki-slideshow.php\?page=(~?)([-_\+A-Za-z0-9]+)("|\')/';
  $replacements[$pc++] = 'href=\1show:\2\3\4';

  // The following supports up to 7 joined calendars
  $patterns[$pc] = '/href=("|\')tiki-calendar.php\?calIds\[\]=(\d+)("|\')/';
  $replacements[$pc++] = 'href=\1cal\2\3';    // 1
  $patterns[$pc] = '/href=("|\')tiki-calendar.php\?calIds\[\]=(\d+)&calIds\[\]=(\d+)("|\')/';
  $replacements[$pc++] = 'href=\1cal\2,\3\4'; // 2
  $patterns[$pc] = '/href=("|\')tiki-calendar.php\?calIds\[\]=(\d+)\&calIds\[\]=(\d+)\&callIds\[\](\d+)("|\')/';
  $replacements[$pc++] = 'href=\1cal\2,\3,\4\5';  // 3
  $patterns[$pc] = '/href=("|\')tiki-calendar.php\?calIds\[\]=(\d+)\&calIds\[\]=(\d+)\&callIds\[\](\d+)\&callIds\[\](\d+)("|\')/';
  $replacements[$pc++] = 'href=\1cal\2,\3,\4,\5\6';  // 4
  $patterns[$pc] = '/href=("|\')tiki-calendar.php\?calIds\[\]=(\d+)\&calIds\[\]=(\d+)\&callIds\[\](\d+)\&callIds\[\](\d+)\&callIds\[\](\d+)("|\')/';
  $replacements[$pc++] = 'href=\1cal\2,\3,\4,\5,\6\7';  // 5
  $patterns[$pc] = '/href=("|\')tiki-calendar.php\?calIds\[\]=(\d+)\&calIds\[\]=(\d+)\&callIds\[\](\d+)\&callIds\[\](\d+)\&callIds\[\](\d+)\&callIds\[\](\d+)("|\')/';
  $replacements[$pc++] = 'href=\1cal\2,\3,\4,\5,\6,\7\8';  // 6
  $patterns[$pc] = '/href=("|\')tiki-calendar.php\?calIds\[\]=(\d+)\&calIds\[\]=(\d+)\&callIds\[\](\d+)\&callIds\[\](\d+)\&callIds\[\](\d+)\&callIds\[\](\d+)\&callIds\[\](\d+)("|\')/';
  $replacements[$pc++] = 'href=\1cal\2,\3,\4,\5,\6,\7,\8\9';  // 7

  // Sort both so they will be processed as pairs by preg_replace.
  ksort($patterns);
  ksort($replacements);

  $tpl_output = preg_replace( $patterns, $replacements, $tpl_output );

  return $tpl_output;
}

?>
