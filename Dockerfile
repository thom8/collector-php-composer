FROM composer:1.4

# add a non-root user and give them ownership
RUN adduser -D -u 9000 app && \
    # repo
    mkdir /repo && \
    chown -R app:app /repo && \
    # collector code
    mkdir /usr/src/collector && \
    chown -R app:app /usr/src/collector && \
    # composer cache
    chown -R app:app /composer/cache

# run everything from here on as non-root
USER app

ADD entrypoint.php /usr/src/collector

WORKDIR /repo

ENTRYPOINT ["php", "/usr/src/collector/entrypoint.php"]
