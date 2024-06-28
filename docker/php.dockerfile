ARG version

FROM wodby/php:${version}

# Download and install Symfony CLI
RUN sudo apk add --no-cache bash
RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.alpine.sh' | sudo -E bash
RUN sudo apk add symfony-cli