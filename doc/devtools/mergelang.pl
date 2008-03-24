#!/usr/bin/perl
# A problem with having to maintain translations of several versions of tiki is that the same strings
# have the same translation, but is not desirable to change twice the same strings
# This utility takes the translations for the old version, and if the string is in the new translation file
# it puts the old translation there.


if ($#ARGV==1) {
	$fileold=$ARGV[0];
	$filenew=$ARGV[1];

	open OLD, "<$fileold";
	while ($line=<OLD>) {
		if ($line=~/^\s*\"(.*)\"\s*\=\>\s*\"(.*)\"\s*\,\s*$/) {
			$orig=$1;
			$trad=$2;
			if ($orig ne $trad) {
				$trads{$orig}=$trad;
			}
		}
	}
	close OLD;
	
	open CURRENT,"<$filenew";
	open NEW,">$filenew.new";
	while ($line=<CURRENT>) {
		if ($line=~/^\s*\"(.*)\"\s*\=\>\s*\"(.*)\"\s*\,\s*$/) {
			$orig=$1;
			$trad=$2;
			if ($trads{$orig}) {
				print NEW "\"$orig\" => \"" . $trads{$orig} . "\",\n";
			} else {
				print NEW "\"$orig\" => \"$trad\",\n";
			}
		} else {
			print NEW $line;
		}
	}
	close CURRENT;
	close NEW;
} else {
	print $#ARGV . "\n";
	print "Usage: mergelang.pl oldlang.php currentlang.php\n";
	print "This will generate a currentlang.php.new file with the content of the currentlang.php file but with the old translations for the matching strings from oldlang.php\n";
}
