[![StyleCI](https://github.styleci.io/repos/424315874/shield?branch=master)](https://github.styleci.io/repos/424315874?branch=master)

**UI**: [Task Manager UI](https://github.com/petyots/task-manager-ui)
### Requirements
* Docker

### Instructions
<hr>

#### 0. Clone this repository
`git clone git@github.com:petyots/task-manager.git`

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

#### 6. Set JWT Secret
``` ./vendor/bin/sail artisan jwt:secret```

#### 7. Migrate the Database
``` ./vendor/bin/sail artisan migrate```

#### 8. Run the tests
``` ./vendor/bin/sail artisan test```

#### 10. **It should now be available at:**  [http://localhost:8084](http://localhost:8084)

