FROM bitnami/php-fpm:7.0

RUN apt-get update && apt-get install -y supervisor vim libxrender1 libfontconfig1 \
    libxext-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

COPY ./aws /root/.aws
COPY ./aws /usr/sbin/.aws

COPY ./auth.json /root/.composer/
