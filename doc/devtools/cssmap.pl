#!/usr/bin/perl
# (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
# 
# All Rights Reserved. See copyright.txt for details and a complete list of authors.
# Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
# $Id$

# red : element not exists in css coloumn
# green : element exists in css column
# yellow : base markup
# #0ff (aqua): css column title
# #f70 (orange): element not found in script (may be in smarty variable ?)
# #7f7 (light green): element found in script

use Getopt::Long;
use File::Spec::Functions;
use File::Find;

# base directory of tikiwiki
my $root = 'E:\Eclipse\3.2.1\tiki';
# file to generate
my $html = "mapcss.html";
# nb rows between titles
my $row = 30;
# nb columns between elements
my $col = 17;
# synopsys : extract_selector.pl [-root <path>] [-html <path>] [-row y] [-col x] 
GetOptions(	"root=s"	=> \$root,
			"html=s"	=> \$html,
			"row=i"		=> \$row,
			"col=i"		=> \$col);
my $style = catdir($root,'styles');

my %markup = map { $_ => 1 } ('*','a','abbr','acronym','address','applet','area',
	'b','base','basefont','bdo','big','blogquote','body','br','button','caption','center',
	'cite','code','col','colgroup','dd','del','dfn','dir','div','dl','dt','em','fieldset','font',
	'form','frame','frameset','h1','h2','h3','h4','h5','h6','head','hr','html','i','iframe','img',
	'input','ins','isindex','kbd','label','legend','li','link','map','menu','meta','noframes',
	'noscript','object','ol','optgroup','option','p','param','pre','q','s','samp','script',
	'select','small','span','strike','strong','style','sub','sup','table','tbody','td',
	'textarea','tfoot','th','thead','title','tr','tt','u','ul','var');

###################################
print "Looking for main css ...\n";
###################################
opendir(REP, $style) or die "Impossible to open directory : $style\n";
my @css = map { s/\.css// ; $_ } grep { /\.css$/ } readdir REP;
closedir REP;

###################################
print "Looking for elements ...\n";
###################################
my %list;
my %recap;
foreach my $css (@css)
{
	my $file = catfile($style, "${css}.css");
	open(FILE, "<$file") or die "Impossible to open file : $file\n";
	while($ligne = <FILE>)
	{
		next if $ligne =~ /\/\*/ .. $ligne =~ /\*\//;
		if ($ligne =~ /^(\S.*?)\s*\{/)
		{
			my $list = $1;
			foreach my $slist (split /[,>\s]/, $list)
			{
				$list{$css}{$slist}++;
				$recap{$slist}{n}++;
				($slistdp = $slist) =~ s/:.*$//;
				$recap{$slist}{h}++, next if exists $markup{lc($slistdp)};
				($slistpp = $slistdp) =~ s/\[.*\]$//;
				$recap{$slist}{h}++ if exists $markup{lc($slistpp)};
			}
		}
	}
	close FILE;
}
my $nbel = keys %recap;

###################################
print "Looking for use case ...\n";
###################################
my %ref;
find(\&analyze, ($root));
sub analyze
{
	next unless /\.(tpl|php)$/;
	if (open(FILE, $File::Find::name))
	{
		my $file = $_;
		while (my $line = <FILE>)
		{
			while ($line =~ /(class|id)="(.*?)"/ig)
			{
				if (uc($1) eq 'CLASS')
				{
					$ref{".$2"}{$file}++;
				}
				else
				{
					$ref{"#$2"}{$file}++;
				}
			}
		}
		close FILE;
	}
}

#################################################
print "Associating use case with elements ...\n";
#################################################
foreach my $use (keys %ref)
{
	foreach my $el (keys %recap)
	{
		if ($el =~ /\Q$use\E(\s|:|$)/)
		{
			$recap{$el}{f} = join "\n", sort keys %{$ref{$use}}, ($recap{$el}{f} ? $recap{$el}{f} : ());
		}
	}
}
%ref = undef;

#################################
print "Building html page ...\n";
#################################
open(FILE,">$html") or die "Impossible to open file : $html\n";
print FILE "<P>Map of non existent elements in all main css : $root</P>";
print FILE "<STYLE type=\"text/css\">td.r {background-color: red;}\n" .
	"td.g {background-color: green;}\n" .
	"td.o {background-color: #7f7;}\n" .
	"td.e {background-color: #f70;}\n" .
	"td.y {background-color: yellow;}\n" .
	"td.t {text-align: center;}\n" .
	"td.c {background-color: #0ff;}</STYLE>\n";
print FILE "<TABLE border=\"1px\">\n";
my $cssname = "<tr>";
my $nbcss = 0;
my %count;
foreach my $css (sort keys %list)
{
	$cssname .= "<td></td>" if $nbcss % $col == 0;
	$cssname .= "<td class=\"c\">$css</td>";
	$nbcss++;
	$count{$css} = 0;
}
$cssname .= "</tr>\n"; 

my $count = 0;
foreach my $el (sort keys %recap)
{
	my $ccol = 0;
	my $flag = 0;
	my $coul = exists $recap{$el}{h} ? 'y' : (exists $recap{$el}{f} ? 'o' : 'e');
	my $ligne = "<tr title=\"" . ($coul eq 'y' ? "HTML" : ($coul eq 'o' ? $recap{$el}{f} : "not found")) . "\">";
	foreach my $css (sort keys %list)
	{
		$ligne .= "<td class=\"${coul}\">$el</td>" if $ccol % $col == 0;
		$ccol++;
		if (exists $list{$css}{$el})
		{
			$ligne .= "<td class=\"g\"></td>";
			$count{$css}++;
		}
		else
		{
			$ligne .= "<td class=\"r\"></td>";
			$flag++;
		}
	}
	$ligne .= "</tr>\n";
	if ($flag)
	{
		print FILE $cssname if $count % $row == 0;
		print FILE $ligne;
		$count++;
	}
}

print FILE "<tr>";
$nbcss = 0;
foreach my $css (sort keys %list)
{
	print FILE "<td></td>" if $nbcss % $col == 0;
	print FILE "<td class=\"t\">$count{$css}</td>";
	$nbcss++;
}
print FILE "</tr>\n"; 

print FILE "</TABLE>\n";
print FILE "<P>$count/$nbel elements to complete in $nbcss css</P>";
close FILE;
print "Terminated.\n";
