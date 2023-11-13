FROM jwilder/nginx-proxy

RUN apt-get update
RUN apt-get install vim -y
RUN apt-get install openssl
COPY . /usr/share/nginx/html
COPY app.conf /etc/nginx/conf.d/app.conf
COPY nginx.conf /etc/nginx/nginx.conf

RUN echo "upstream php-upstream { server php:9000; }" > /etc/nginx/conf.d/upstream.conf

CMD ["nginx", "-g", "daemon off;"]
