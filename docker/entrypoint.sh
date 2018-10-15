#!/bin/sh

# If GOSU_CHOWN environment variable set, recursively chown all specified directories
# to match the user:group set in GOSU_USER environment variable.
if [ -n "$GOSU_CHOWN" ]; then
    for DIR in $GOSU_CHOWN
    do
        chown -R $GOSU_USER $DIR
    done
fi

# If GOSU_USER environment variable set to something other than 0:0 (root:root),
# become user:group set within and exec command passed in args
if [ "$GOSU_USER" != "0:0" ]; then
    chown -R $GOSU_USER /slimer
    cp -r /slimer/app/Static /slimer_static/app
    exec /usr/local/bin/gosu $GOSU_USER "$@"
else
    # If GOSU_USER was 0:0 exec command passed in args without gosu (assume already root)
    chown -R ${slimer_USER}:9999 /slimer/logs
    chown -R ${slimer_USER}:9999 /slimer/session
    chown -R ${slimer_USER}:9999 /slimer/app
    chown -R ${slimer_USER}:9999 /slimer/Data
    chown -R ${slimer_USER}:9999 /slimer/.ssh
    chmod go-rw /slimer/Data/gerrit_id_rsa
    cp -r /slimer/app/Static /slimer_static/app
    exec "$@"
fi

