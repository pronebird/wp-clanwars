#!/bin/sh

cd "$(dirname "$0")"

cp -R tools ../../tools
cp pot.sh ../../

cd ../../
ln -s ../../ src
