#!/usr/bin/env bash
inoticoming --foreground --logfile /tmp/i.log /home/michael/PhpStormProjects/webrepo \
--suffix "-job.json" --stdout-to-log --stderr-to-log \
--chdir /home/michael/PhpStormProjects/webrepo \
php execute.php {} \;