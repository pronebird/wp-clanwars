#!/bin/sh

cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd

cp -R tools ../../tools
cp pot.sh ../../

cd ../../
ln -s ../../ src
