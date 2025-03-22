# Sử dụng image PHP chính thức
FROM php:8.0-apache

# Cài đặt các extension cần thiết
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copy mã nguồn của bạn vào container
COPY . /var/www/html/

# Thiết lập quyền cho thư mục
RUN chown -R www-data:www-data /var/www/html

# Mở cổng 80
EXPOSE 80