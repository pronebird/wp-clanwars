#!/bin/sh

cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd
cd ..

# http://docs.transifex.com/client/pull/
tx pull --all
