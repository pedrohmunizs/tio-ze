#!/bin/sh
set -e

# Verifica se o arquivo .env existe
if [ ! -f .env ]; then
    echo "📝 Criando arquivo .env a partir de .env.example..."
    cp .env.example .env
fi

# Configura APP_KEY
if ! grep -q "^APP_KEY=" .env || [ -z "$(grep "^APP_KEY=" .env | cut -d '=' -f2)" ]; then
    echo "🔑 Gerando APP_KEY..."
    php artisan key:generate --no-interaction --force
fi

# Ajusta permissões
echo "🔐 Ajustando permissões..."
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R ug+rwX /var/www/storage /var/www/bootstrap/cache
find /var/www/storage /var/www/bootstrap/cache -type d -exec chmod 775 {} \;

mkdir -p /var/www/public/build
chown -R www-data:www-data /var/www/public/build
chmod -R 777 /var/www/public/build

if [ ! -f resources/js/bootstrap.js ]; then
    echo "📄 Criando bootstrap.js..."
    cat > resources/js/bootstrap.js << 'EOF'
import axios from 'axios';
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
EOF
fi

# MELHORIA: Verifica se é ambiente de produção antes de instalar
if [ "$APP_ENV" = "production" ]; then
    echo "📦 Instalando dependências de produção..."
    composer install --no-dev --optimize-autoloader --prefer-dist
    npm install --production && npm run build
else
    echo "📦 Instalando dependências de desenvolvimento..."
    composer install --optimize-autoloader
    npm install && npm run build
fi

# MELHORIA: Executa migrations se necessário
if [ "$RUN_MIGRATIONS" = "true" ]; then
    echo "🔄 Executando migrations..."
    php artisan migrate --force
fi

# MELHORIA: Limpa cache em desenvolvimento
if [ "$APP_ENV" != "production" ]; then
    echo "🧹 Limpando cache..."
    php artisan config:clear
    php artisan cache:clear
    php artisan view:clear
    php artisan route:clear
fi

echo "✅ Setup completo! Iniciando servidor..."
exec "$@"