#!/usr/bin/python
# (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
#
# All Rights Reserved. See copyright.txt for details and a complete list of authors.
# Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
# $Id: release.php 64614 2017-11-17 23:30:13Z rjsmelo $

# ** This script compares two changelogs, extracts comments marked with [TYPE] and generates
# a reference commit log to create a release changelog.
#
# Example:
#
# svn checkout https://svn.code.sf.net/p/tikiwiki/code/branches/17.x tiki17
# cd tiki17
# svn log > ../tiki17.log
# cd ..
# svn checkout https://svn.code.sf.net/p/tikiwiki/code/trunk tikitrunk
# cd tikitrunk
# svn log > ../tikitrunk.log
# ./doc/devtools/generate_release_changelog.py ../tiki17/tiki17.log tikitrunk.log
#

import argparse
from collections import OrderedDict

import argparse

parser = argparse.ArgumentParser(description='Generate changelog for a branch, by comparing latest svn log with one of previous release branch')
parser.add_argument('previous_svnlog', metavar='N', nargs='+',
                    help='Previous release branch SVN log')
parser.add_argument('latest_svnlog', metavar='N', nargs='+',
                    help='Previous release branch SVN log')

args = parser.parse_args()

previous_svnlog = args.previous_svnlog[0]
latest_svnlog = args.latest_svnlog[0]

previous = {}
latest = {}

def extract_changelog(svnlog):
    changelog = OrderedDict()
    commit = None
    for line in open(svnlog):
        line = line.strip()
        if len(line) > 0 and line == '-' * len(line):
            commit = None
        elif commit is None:
            commit = line
        elif line.startswith('['):
            typ = line[1:].split(']')[0].upper()
            if typ not in ('MRG', 'REF'):
                changelog[line] = commit
    return changelog

previous = extract_changelog(previous_svnlog)
latest = extract_changelog(latest_svnlog)

changes = []
for change in latest.keys():
    if previous.get(change) is None:
        changes.append(change)

for change in reversed(changes):
    print(latest[change])
    print(change)
    print('')
