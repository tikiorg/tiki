#!/bin/bash
#
# Script for providing tarballs of tiki releases (snapshots)
#
# You have to configure paths and svn user in the marked section below.
# Your svn login data must be availabe in ~/.subversion, see svn docs. 
#
# Usage: $0 <branch, eg trunk or 6 or 7 or 11 ... >
# Example: $0 trunk

VERSION=$1

if [ "${VERSION}" = "" ]; then
  echo "ERROR: version parameter missing (eg 'trunk', '9', '11')"
  exit
fi
BRANCH=${VERSION}
if [ ! "${VERSION}" = "trunk" ]; then
    BRANCH=BRANCH-${VERSION}-x
fi

# --- configuration --- edit here ---
SVNUSER=ohertel
LOGFILE="/var/log/tiki/svnlog_${BRANCH}"
TARGET="/var/www/tikiwiki.org/tar/"
BASE=/opt
# --- configuration --- edit here ---

OLDIR=`pwd`
PACKDIR=${BASE}/data/packaging

mkdir -p ${BASE}/data/arc  ${PACKDIR}  ${TARGET}

# start new logfile with timestamp as first entry
date > ${LOGFILE}

# always rebuild trunk from scratch (for now!)
if [ "${BRANCH}" = "trunk" ]; then
    rm -rf ${BASE}/data/${BRANCH}
fi

if [ ! -d "${BASE}/data/${BRANCH}" ]; then
    echo "Folder doesn't exist yet, checking out project ..."

    cd ${BASE}/data
    if [ "${BRANCH}" = "trunk" ]; then
        svn checkout --username ${SVNUSER} https://svn.code.sf.net/p/tikiwiki/code/${BRANCH} ${BRANCH} 2>&1 >> ${LOGFILE}
    else
        svn checkout --username ${SVNUSER} https://svn.code.sf.net/p/tikiwiki/code/branches/${VERSION}.x ${BRANCH} 2>&1 >> ${LOGFILE}
    fi

    LINE=`tail -1 ${LOGFILE}`
    RC=`echo "${LINE}" | grep "Checked out revision"`
    if [ "${RC}" = "" ]; then
        echo "ERROR: could not check out project: ${LINE}"
        exit
    fi
else
    echo "Folder exists, updating project ..."
    cd ${BASE}/data/${BRANCH}
    svn update 2>&1 >> ${LOGFILE}
    LINE=`tail -1 ${LOGFILE}`
    RC=`echo "${LINE}" | grep "At revision"`
    if [ "${RC}" = "" ]; then
        RC=`echo "${LINE}" | grep "Updated to revision"`
        if [ "${RC}" = "" ]; then
            echo "ERROR: could not update project: ${LINE}"
            exit
        fi
    else
        echo "INFO: no need to update: ${LINE}"
        exit
    fi
fi

echo "Cleanup packaging folder and rebuild"
rm -rf ${PACKDIR}/${BRANCH}
cp -r ${BASE}/data/${BRANCH} ${PACKDIR}

# cleanup
if [ -d "${PACKDIR}/${BRANCH}" ]; then
    cd ${PACKDIR}/${BRANCH}
    echo "Current folder: `pwd`"

    echo "Removing temp files and cache files, if any"
    # no tests needed and no temporary files:
    rm -rf tests *.core *.tmp *.bak *.tpl.php temp/cache
    mkdir -p temp/cache

    echo "Hopefully no one has checked in his password file, we better remove it now"
    rm -f db/local.php

    echo "Chmodding setup.sh"
    chmod u+x setup.sh

    echo "Executing setup.sh"
    ./setup.sh -u www-data -g www-data -n fix

    echo "Removing svn config files"
    find . -name ".svn" | xargs -n 1 rm -rf

    echo "Adding timestamp file to archive"
    echo "Last update: " > .timestamp
    echo `date` >> .timestamp

    cd ${PACKDIR}

    echo "Creating tar.bz2 file"
    rm -f ${BASE}/data/arc/lastiki_${BRANCH}.tar.bz2_previous
    mv ${BASE}/data/arc/lastiki_${BRANCH}.tar.bz2 ${BASE}/data/arc/lastiki_${BRANCH}.tar.bz2_previous
    tar -cjf ${BASE}/data/arc/lastiki_${BRANCH}.tar.bz2 ${BRANCH}

    echo "Createing zip file"
    rm -f ${BASE}/data/arc/lastiki_${BRANCH}.zip_previous
    mv ${BASE}/data/arc/lastiki_${BRANCH}.zip ${BASE}/data/arc/lastiki_${BRANCH}.zip_previous
    zip -rq ${BASE}/data/arc/lastiki_${BRANCH}.zip ${BRANCH}
    chown www-data:www-data ${BASE}/data/arc/lastiki_${BRANCH}.zip
    chmod a+r ${BASE}/data/arc/lastiki_${BRANCH}.zip

    echo "Copying archives to docroot folder"
    cp ${BASE}/data/arc/lastiki_${BRANCH}.tar.bz2_previous ${TARGET}
    cp ${BASE}/data/arc/lastiki_${BRANCH}.tar.bz2 ${TARGET}
    cp ${BASE}/data/arc/lastiki_${BRANCH}.zip_previous ${TARGET}
    cp ${BASE}/data/arc/lastiki_${BRANCH}.zip ${TARGET}

    echo "Creating md5 files"
    cd ${TARGET}
    FILES=`/bin/ls * | grep -v md5 | grep -v grep`
    for FILE in ${FILES}
    do
      md5sum ${FILE} > ${FILE}.md5
      chmod a+r *
    done
else
    echo "ERROR: Folder not found after svn checkout."
    exit
fi

cd ${OLDIR}

echo "Finish"

# eof
