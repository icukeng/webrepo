#!/usr/bin/env bash
while inotifywait -e close_write job.json
do php ./execute.php
done