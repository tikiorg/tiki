#!/usr/bin/perl

$entrada="mod-application_menu.tpl";

open ENTRADA,"<$entrada";
foreach $linea (<ENTRADA>) {
	chomp($linea);
	if ($linea=~/javascript\:toggle\(\'(\w+)\'/) {
		$nommenu=$1;
	}
	if ((($linea=~/^\s*\{if \$feature_/) or ($linea=~/^\s*\{if \$tiki_p_/)) and (!($linea=~/feature_menusfolderstyle/))) {
		$linea=~s/eq/\=\=/g;
		$linea=~s/{//g;$linea=~s/}//g;
		$linea=~s/if \$/if \(\$/g;$linea.=") {";
		print "$linea\n";
	}
	if (($linea=~/href\=\"(\S+)\".*\{tr\}(.*)\{\/tr\}/) and (!($linea=~/icntoggle/))) {
		print "\$appmenu[]=array(\"link\"=>\"$1\",\"text\"=>tra(\"$2\"),\"menu\"=>\"$nommenu\");\n";
	}
}
close ENTRADA;


