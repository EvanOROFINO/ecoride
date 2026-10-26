#!/bin/bash
# Démarre une base MariaDB embarquée (mode démo), la remplit avec le schéma et
# le jeu de données de test, puis lance Apache.
#
# Pourquoi : l'offre gratuite de Render n'a pas de MySQL, et les bases gratuites
# externes finissent par être suspendues/supprimées (c'est ce qui est arrivé à
# AlwaysData). La base est recréée à chaque démarrage du conteneur : les données
# saisies pendant la démo ne sont donc pas conservées, ce qui convient à une démo.
set -euo pipefail

DB_NAME="${DB_NAME:?DB_NAME manquant}"
DB_USER="${DB_USER:?DB_USER manquant}"
DB_PASSWORD="${DB_PASSWORD:?DB_PASSWORD manquant}"
APP_DIR=/var/www/html

# Base vierge à chaque démarrage (disque éphémère)
rm -rf /var/lib/mysql
mkdir -p /var/lib/mysql /run/mysqld
chown -R mysql:mysql /var/lib/mysql /run/mysqld
mariadb-install-db --user=mysql --datadir=/var/lib/mysql >/dev/null

# Réglages sobres pour tenir dans 512 Mo de RAM, écoute locale uniquement
mariadbd-safe --user=mysql --bind-address=127.0.0.1 \
    --innodb-buffer-pool-size=32M --innodb-log-file-size=16M \
    --performance-schema=OFF --max-connections=30 \
    --character-set-server=utf8mb4 --collation-server=utf8mb4_unicode_ci \
    >/var/log/mariadb-boot.log 2>&1 &

for _ in $(seq 1 90); do
    if mariadb-admin --silent ping 2>/dev/null; then break; fi
    sleep 1
done
mariadb-admin --silent ping   # échoue (set -e) si la base n'a pas démarré

# Schéma + données de démonstration (les scripts créent la base elle-même)
mariadb --default-character-set=utf8mb4 < "$APP_DIR/sql/01_schema.sql"
mariadb --default-character-set=utf8mb4 < "$APP_DIR/sql/02_seed.sql"

mariadb <<SQL
CREATE USER '${DB_USER}'@'127.0.0.1' IDENTIFIED BY '${DB_PASSWORD}';
CREATE USER '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASSWORD}';
GRANT ALL PRIVILEGES ON \`${DB_NAME}\`.* TO '${DB_USER}'@'127.0.0.1';
GRANT ALL PRIVILEGES ON \`${DB_NAME}\`.* TO '${DB_USER}'@'localhost';
FLUSH PRIVILEGES;
SQL

exec apache2-foreground
