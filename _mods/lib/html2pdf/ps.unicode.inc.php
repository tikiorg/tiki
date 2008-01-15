<?php
// $Header: /cvsroot/tikiwiki/_mods/lib/html2pdf/ps.unicode.inc.php,v 1.1 2008-01-15 09:21:09 mose Exp $

// TODO: make encodings-related stuff more transparent
function &find_vector_by_ps_name($psname) {
  global $g_utf8_converters;

  foreach ($g_utf8_converters as $key => $value) {
    if ($value[1] == $psname) {
      return $value[0];
    };
  };

  return 0;
};

$g_utf8_converters = array(
                           'iso-8859-1'   => array($g_iso_8859_1,"ISO-8859-1-Encoding"),
                           'iso-8859-2'   => array($g_iso_8859_2,"ISO-8859-2-Encoding"),
                           'iso-8859-3'   => array($g_iso_8859_3,"ISO-8859-3-Encoding"),
                           'iso-8859-4'   => array($g_iso_8859_4,"ISO-8859-4-Encoding"),
                           'iso-8859-5'   => array($g_iso_8859_5,"ISO-8859-5-Encoding"),
                           'iso-8859-7'   => array($g_iso_8859_7,"ISO-8859-7-Encoding"),
                           'iso-8859-9'   => array($g_iso_8859_9,"ISO-8859-9-Encoding"),
                           'iso-8859-10'  => array($g_iso_8859_10,"ISO-8859-10-Encoding"),
                           'iso-8859-11'  => array($g_iso_8859_11,"ISO-8859-11-Encoding"),
                           //                           'iso-8859-13'  => array($g_iso_8859_13,"ISO-8859-13-Encoding"),
                           'iso-8859-14'  => array($g_iso_8859_14,"ISO-8859-14-Encoding"),
                           'iso-8859-15'  => array($g_iso_8859_15,"ISO-8859-15-Encoding"),
                           'dingbats'     => array($g_dingbats,"Dingbats-Encoding"),
                           'symbol'       => array($g_symbol,"Symbol-Encoding"),
                           'koi8-r'       => array($g_koi8_r,"KOI8-R-Encoding"),
                           'cp1250'       => array($g_windows_1250,"Windows-1250-Encoding"),
                           'cp1251'       => array($g_windows_1251,"Windows-1251-Encoding"),
                           'windows-1250' => array($g_windows_1250,"Windows-1250-Encoding"),
                           'windows-1251' => array($g_windows_1251,"Windows-1251-Encoding"),
                           'windows-1252' => array($g_windows_1251,"Windows-1252-Encoding")
                           // 'cp866'       => array($g_cp866,"")
                           );
?>