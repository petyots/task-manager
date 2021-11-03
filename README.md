### Requirements
* Docker

### Instructions
<hr>

#### 1. Install Composer Dependencies

```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v $(pwd):/var/www/html \
    -w /var/www/html \
    laravelsail/php80-composer:latest \
    composer install --ignore-platform-reqs
```

#### 2. CD to the current project dir

#### 3. copy .env-example
```bash
cp .env-example .env
```

#### 4. Run the container in detached mode
``` ./vendor/bin/sail up -d```

#### 5. Set the app Encryption Key
``` ./vendor/bin/sail artisan key:generate```

#### 6. Migrate the Database
``` ./vendor/bin/sail artisan migrate```
