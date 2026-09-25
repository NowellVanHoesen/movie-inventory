#!/bin/sh

# Deploys the release tag passed as the first argument (deploy.yml passes the
# published release's tag name).
#
# Everything runs inside main() so the shell parses the whole script before any
# of it executes: the checkout below rewrites this very file, and sh reads
# scripts incrementally, so without the wrapper it could run a mix of old and
# new lines.

main() {
    RELEASE_TAG="$1"

    if [ -z "$RELEASE_TAG" ]; then
        echo "Usage: deploy.sh <release-tag>"
        exit 1
    fi

    echo "Deployment of $RELEASE_TAG started..."

    # change to the project dir
    cd ~/www/movies.nvweb.dev/public_html || exit 1

    # fetch the release before going down, so a bad tag never leaves the site in maintenance mode
    git fetch --tags --force origin

    if ! git rev-parse --quiet --verify "refs/tags/$RELEASE_TAG^{commit}" > /dev/null; then
        echo "Tag $RELEASE_TAG not found on origin; nothing deployed."
        exit 1
    fi

    # enter maintenance mode
    (php84 artisan down) || true

    # check out the release (detached HEAD at the tag)
    git checkout --force "refs/tags/$RELEASE_TAG"

    # install composer dependencies
    composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

    # install npm packages
    npm install --omit=dev

    # run migrations
    php84 artisan migrate --force --env=production

    # clear caches
    php84 artisan optimize:clear --env=production

    # recreate up caches
    php84 artisan optimize --env=production

    # exit maintenance mode
    php84 artisan up

    echo "Deployment of $RELEASE_TAG finished"
}

main "$@"
exit
