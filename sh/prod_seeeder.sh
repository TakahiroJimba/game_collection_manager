#!/bin/bash

# Composerのオートローダを再生成
composer dump-autoload
php artisan db:seed --class="ProductionSeeder"
