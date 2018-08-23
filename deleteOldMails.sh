#!/bin/bash

# Trash - default
find /home/*/mail/*/*/.Trash/cur -type f -mtime +30 -delete

# Deleted Items - client ?
find /home/*/mail/*/*/.Deleted\ Items/cur -type f -mtime +30 -delete

# Deleted Messages - Roundcube
find /home/*/mail/*/*/.Deleted\ Messages/cur -type f -mtime +30 -delete

# Deleted Messages - Spam
find /home/*/mail/*/*/.Spam/cur -type f -mtime +30 -delete

# Deleted Messages - Junk
find /home/*/mail/*/*/.Junk/cur -type f -mtime +30 -delete

