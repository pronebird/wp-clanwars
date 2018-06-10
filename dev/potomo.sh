#!/bin/sh

cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd
cd ..

# Create .mo files from .po files.
for file in `find ./langs -name "*.po"` ; do echo "Converting $file to MO file..." && msgfmt -o ${file/.po/.mo} $file ; done
