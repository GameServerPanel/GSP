#!/bin/bash

for f in *.xml; do
   bakUp=${f%.*}.bak.xml
   mv $f $bakUp
   xmlsort -r 'mods/mod' -k 'name' -i -s $bakUp > $f
   rm $bakUp
done
