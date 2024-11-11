#!/bin/sh
# wait-for.sh

set -e

host="$1"
shift
until nc -z "$host" 3306; do
  echo "Waiting for MySQL..."
  sleep 1
done

exec "$@"
