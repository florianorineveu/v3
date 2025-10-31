FROM nginx:1.27-alpine

ARG NGINX_ENV=default

COPY docker/nginx/${NGINX_ENV}.conf /etc/nginx/conf.d/default.conf

RUN mkdir -p /var/log/nginx && \
    chown -R nginx:nginx /var/log/nginx

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
