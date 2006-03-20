#!/usr/bin/perl -i


$file = $ARGV;
$file =~ s/\.tpl$/\.php/;
$file =~ s|^.+/||;
$template = 'tiki-admin_quicktags_content.tpl';
$htmlelement = 'quicktags-content';

while (<>) {
    s|href="($file[^\"]+)"|\{ajax_href template="$template" htmlelement="$htmlelement"\}$1\{/ajax_href\}}|g;
    print;
}
