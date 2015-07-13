#!/bin/sh

$(cd -P -- "$(dirname -- "$0")" && pwd -P)

cp -R tools ../../tools
cp pot.sh ../../

cd ../../
ln -s ../../ src
