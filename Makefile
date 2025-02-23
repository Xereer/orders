start:
    docker-compose up -d --build

stop:
    docker-compose down

migrate:
    docker-compose exec app php bin/console doctrine:migrations:migrate --no-interaction

fixtures:
    docker-compose exec app php bin/console doctrine:fixtures:load --no-interaction
