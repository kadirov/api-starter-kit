# Instruction for developers of Api Starter Kit

When new version of Symfony relised you should update ASK by next steps
For updating ASK to new version of symfony
- composer create-project symfony/skeleton
- copy .gitignore file from old version of ASK
- copy docker folder, docker-compose and .gitlab-ci files
- copy public/media folder
- copy DOCKER_ configs from .env file and change DOCKER_PROJECT_NAME value
- docker compose php exec composer req api
- docker compose php exec composer req lexik/jwt-authentication-bundle
- docker compose php exec composer req symfony/maker-bundle --dev
- check and copy configs from /config folder
- check and copy .env configs
- copy /src folder (all folder but without the Kernel.php file)
- docker compose exec php bin/console make:migration
- add description into generated migration file
- docker compose exec php bin/console ask:install
- check the all endpoint via swagger
- do screenshot an save as poster.png if endpoint has changed
- fix versions in the composer.js
- add name and license info into compose.json
- delete all files in the old repo then copy all new files to the old folder
- commit and push the project
