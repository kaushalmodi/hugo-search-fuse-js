#!/usr/bin/env bash
# Usage:
#   ./srv.sh      # Serves on port 2323 by default
#   ./srv.sh 1337 # Serves on port 1337

port="${1:-2323}" # default

hugo server \
     --buildDrafts \
     --navigateToChanged \
     --port "${port}"
