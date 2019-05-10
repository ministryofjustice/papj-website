FROM mojdigital/wordpress-base:upgrade

# Upgrade nodejs
RUN curl -sL https://deb.nodesource.com/setup_11.x | bash - && \
    DEBIAN_FRONTEND=noninteractive apt-get install -y nodejs && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* /var/tmp/* /init

ADD . /bedrock

WORKDIR /bedrock

ARG COMPOSER_USER
ARG COMPOSER_PASS

# Add custom nginx config and init script
RUN sed -i 's/fastcgi_intercept_errors off;/fastcgi_intercept_errors on;/' /etc/nginx/php-fpm.conf && \
    mv docker/conf/nginx/server.conf /etc/nginx/sites-available/ && \
    mv docker/init/configure-maintenance-mode.sh /etc/my_init.d/ && \
    chmod +x /etc/my_init.d/configure-maintenance-mode.sh

# Set execute bit permissions before running build scripts
RUN chmod +x bin/* && sleep 1 && \
    #make clean && \
    bin/composer-auth.sh && \
    make build && \
    rm -f auth.json
