#!/bin/bash


if [ -z "$1" ]; then
   for f in *.xml; do
      bakUp=${f%.*}.bak.xml
      echo "Working on.... $f"
      mv $f $bakUp
      xmlsort -r 'mods/mod' -k 'name' -i -s $bakUp > $f
      rm $bakUp
   done
else
   f="$1"
   echo "Working on.... $f"
   bakUp=${f%.*}.bak.xml
   mv $f $bakUp
   xmlsort -r 'mods/mod' -k 'name' -i -s $bakUp > $f
   rm $bakUp
fi

chown www-data:www-data *.xml

