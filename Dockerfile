# VulnShop PHP - deliberately vulnerable training image. Do NOT expose publicly.
FROM php:8.2-apache

# Intentionally weak runtime config for the lab (A05 Security Misconfiguration):
#  - display_errors ON leaks stack traces / SQL
#  - allow_url_include ON makes the LFI module reachable as RFI
RUN { \
      echo "display_errors = On"; \
      echo "display_startup_errors = On"; \
      echo "allow_url_include = On"; \
      echo "allow_url_fopen = On"; \
    } > /usr/local/etc/php/conf.d/zz-vuln.ini

# SQLite driver for the demo DB (needs the sqlite3 dev headers to compile).
# Use HTTPS Debian mirrors (plain-HTTP apt is unreliable in some build networks).
RUN sed -i "s|http://deb.debian.org|https://deb.debian.org|g" /etc/apt/sources.list.d/debian.sources 2>/dev/null || true; \
    sed -i "s|http://deb.debian.org|https://deb.debian.org|g" /etc/apt/sources.list 2>/dev/null || true; \
    apt-get update \
    && apt-get install -y --no-install-recommends libsqlite3-dev \
    && docker-php-ext-install pdo pdo_sqlite \
    && rm -rf /var/lib/apt/lists/*

COPY src/ /var/www/html/
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
# App listens on container :80 -> published as :8081 (see docker-compose / run cmd)
