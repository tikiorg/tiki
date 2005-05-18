#!/usr/bin/perl

# $Header: /cvsroot/tikiwiki/tiki/doc/devtools/findstyledef.pl,v 1.1 2005/03/13 23:38:07 ohertel Exp $
# finds all the style and class definitions in tpl and php files
#
# calles by findstyledef.sh
#
# ohertel@tw.o

use strict;
use warnings;
use Cwd;

use File::Find;

my $root=$ARGV[0];
my $file_pattern = "^(.*)\.(tpl|php)\$";

find(\&d, $root);

sub d {

  my $file = $File::Find::name;

  $file =~ s#/#\\#g;

  return unless -f $file;
  return unless $file =~ /$file_pattern/;

#  print $file."\n";

  open F, $file or print "couldn't open $file\n" && return;
  while (<F>) {
    if (my ($found) = m/\b((class|style)\s*=\w*[\\]*[\"]{1}.*?[\\]*[\"]{1})/oi) {
#      print "$file: $found\n";
      print "$found\n";
    }
    if (my ($found) = m/\b((class|style)\s*=\w*[\\]*[']{1}.*?[\\]*[']{1})/oi) {
#      print "$file: $found\n";
      print "$found\n";
    }
  }

  close F;
}
