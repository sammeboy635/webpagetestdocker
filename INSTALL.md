# Setup System use Ubuntu 14.04

# Install dependencies

apt install \
    git \
    nginx \
    php7.0-fpm \
    php7.0-mysql php7.0-zip php7.0-curl php7.0-gd php7.0-intl php-pear php-imagick php7.0-imap php7.0-mcrypt \
    php-memcache  php7.0-pspell   php7.0-recode php7.0-sqlite3 php7.0-tidy php7.0-xmlrpc php7.0-xsl php7.0-mbstring \
    php-gettext php-apcu \
    ffmpeg \
    imagemagick \
    libjpeg-progs \
    exiftool
    
    
mkdir /var/www
chown www-data /var/www 
chgrp www-data /var/www 

# Prepare webpage test
    
sudo -u www-data bash
cd /var/www
git clone https://github.com/WPO-Foundation/webpagetest.git
cd webpagetest/www/settings/
mv settings.ini.sample settings.ini
vi settings.ini
exit


cat >/etc/nginx/sites-available/default <<EOF
server {
        listen 80 default_server;
        listen [::]:80 default_server ipv6only=on;

        root   /var/www/webpagetest/www;
        add_header Timing-Allow-Origin *;

        location /work/update {
                autoindex on;
        }

        # pass the PHP scripts to FastCGI server
        #
        location ~ \.php$ {
                include snippets/fastcgi-php.conf;
        #       fastcgi_pass    unix:/var/run/php5-fpm.sock;
                fastcgi_pass    unix:/run/php/php7.0-fpm.sock;
        #        fastcgi_param  SCRIPT_FILENAME  /var/www/webpagetest/www$fastcgi_script_name;
        #        fastcgi_param  HTTP_MOD_REWRITE On;
        #        include fastcgi_params;
        }

        include /var/www/webpagetest/www/nginx.conf;
}
EOF

systemctl restart nginx

vi /etc/php/7.0/fpm/php.ini
# Set the following values:
# upload_max_filesize = 20M
# post_max_size = 20M
# memory_limit = -1

systemctl restart php7.0-fpm