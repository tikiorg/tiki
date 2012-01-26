#!/usr/bin/env python
# (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
# 
# All Rights Reserved. See copyright.txt for details and a complete list of authors.
# Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
# $Id$

# NOTICE:
# 1. You must be root to run this script.
# 2. This script only works on unix/linux systems.
# 3. This script requires python

# PURPOSE:
# set_perms.py - sets the permissions for the tiki directory
#  replacing part of the functionality of the setup.sh script found in
#  Tiki's root directory.

# USAGE:
# cd to the Tiki root directory then
# invoke as             ./doc/devtools/set_perms.py [mask]
# i.e., something like: ./doc/devtools/set_perms.py
# or                    ./doc/devtools/set_perms.py 000
#
# For a Tiki tree where you are doing development, a mask of 000 makes sense.
#   This allows an ordinay user to modify the files and work with cvs.
# For a production tiki, having the files owned by apache and a mask of 477 is
#   the most secure.  This allows apache to read files like the php and tpl
#   and to execute directories.   However, certain directories, like the cache
#   directories, need to be writable by apache.  Create them after you run
#   this script or simply use the less secure 077 mask.
#
# if you don't specify a mask, your umask is used.

# TODO
# convert this to a bash script in integrate it with setup.sh
# generalize so that non-root users can run it

import os
import sys
import re

def what_line_am_i_on():
    try:
        raise "Hack"
    except:
        return sys.exc_info()[2].tb_frame.f_back.f_lineno

def usage():
    print "Usage: ", sys.argv[0], "[umask]"

sTmpOutFileName = "/root/set_perms.tmp-"+str(os.getpid())+".out"  # these files go in /root instead of /tmp
sTmpErrFileName = "/root/set_perms.tmp-"+str(os.getpid())+".err"  #   to keep prying eyes out!

# Don't overwrite existing sTmpOutFileName
status = os.system("ls " + sTmpOutFileName + " > /dev/null 2>&1")
if status == 0:
    print sys.argv[0]+":", "file " + sTmpOutFileName + " already exists.  Exiting."
    sys.exit(status)

# Don't overwrite existing sTmpErrFileName
status = os.system("ls " + sTmpErrFileName + " > /dev/null 2>&1")
if status == 0:
    print sys.argv[0]+":", "file " + sTmpErrFileName + " already exists.  Exiting."
    sys.exit(status)

# A little utility function for handling error conditions.
def cleanup_and_die(status, message):
    os.system("rm -f " + sTmpOutFileName)
    os.system("rm -f " + sTmpErrFileName)
    print sys.argv[0]+":", message
    sys.exit(status)

if len(sys.argv) == 1:
    umask = os.popen("umask").readline()[1:]
elif len(sys.argv) == 2:
    umask = sys.argv[1]
else:
    usage()
    sys.exit(-1);

# This script must be run as root
if os.getuid():
    print sys.argv[0], "must be run as root."
    sys.exit(-1)

# This script must be run on a Linux/unix machine
status = os.system("""uname | grep Linux > /dev/null 2>&1""")
if status:
    cleanup_and_die(status, """uname | grep Linux failed.  You need to modify the script for your OS.  Exiting.""")

# umask has to look like 000 or something
if not re.match(r'^[0-9]{3}$',umask):
    cleanup_and_die(-1, """umask must have three numerals, e.g. 000""")

# # check for the tiki root directory
# status = os.system("ls ../.. > /dev/null 2>&1")
# if status:
#     cleanup_and_die(status, "ls ../.. failed.  Could not locate your tiki root directory.  Exiting.")

# # cd to the tiki root directory
# status = os.chdir("../..")
# if status:
#     cleanup_and_die(status, "cd ../.. failed.  Is there something wrong with your tiki root directory.  Exiting.")

status = os.system("""find . -name "*" -print > """ + sTmpOutFileName +" 2> " + sTmpErrFileName)
nFiles = os.popen("cat " + sTmpOutFileName + " | wc -l ").readline()
nFiles = nFiles.strip()

print "Setting permissions for", nFiles, "files..."

nPermOwner = 7 ^ int(umask[0])
nPermGroup = 7 ^ int(umask[1])
nPermWorld = 7 ^ int(umask[2])
# print nPermOwner
# print nPermGroup
# print nPermWorld

sPerm = str(nPermOwner) + str(nPermGroup) + str(nPermWorld) # something like 755
# print sPerm

# chmod to the most liberal as specified by the umask
status = os.system("chmod -R " + sPerm + " * > /dev/null 2>&1")
if status:
    cleanup_and_die(status, "chmod -R failed.  Exiting.")

# take away exe permission for everything but dirs, .sh, .py,  
nReassure = int(nFiles) / 80
# print nReassure
iReassure = 0
f=open(sTmpOutFileName, 'r')
lines = f.readlines()
f.close()
rsDirectory = re.compile('.+: directory$')
rsFileSuffix = re.compile('^.+(\.sh|\.py):\s.+$')
for sFileName in lines:       # sFileName name e.g. "./setup.sh\n"
    iReassure = iReassure + 1
    if iReassure > nReassure:
        iReassure = 0
        sys.stdout.write(".")
        sys.stdout.flush()
    sFileName = sFileName.strip()[2:] # sFileName name e.g. "setup.sh"
    # print sFileName
    fType = os.popen("file " + sFileName).readline()
    fType = fType.strip()
    if rsDirectory.match(fType):      # e.g. "templates: directory"
        # print fType
        continue
    if rsFileSuffix.match(fType):     # e.g. "setup.sh"
        # print fType
        continue

    # if you have other types that need exe permission
    # test for them here or modify rsFileSuffix regular expression
    #everything else in not executable
    os.system("chmod -x \"" + sFileName + "\"")
    # print fType

sys.stdout.write("\n")
sys.stdout.flush()

# cleanup
os.system("rm -f " + sTmpOutFileName)
os.system("rm -f " + sTmpErrFileName)
