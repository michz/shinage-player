#!/usr/bin/env bash

PROJECT_DIR="$( cd "$( dirname $( dirname "${BASH_SOURCE[0]}") )" && pwd )"
PLAYER_RELEASE_URL="https://raw.githubusercontent.com/michz/shinage-static-player/gh-pages/player.html"


(
cd $PROJECT_DIR/public
wget -O ./index.html "$PLAYER_RELEASE_URL"
sed -i 's/urlParamReader.getUrlParam("current_presentation_url","http:\/\/localhost:8080\/test.txt")/urlParamReader.getUrlParam("current_presentation_url","http:\/\/localhost:8000\/current")/g' index.html
)
