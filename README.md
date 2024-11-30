# Laravel Custom Background Job Runner

A custom system to execute PHP classes as background jobs, independent of Laravel's built-in queue system

## Requirements

- [Docker Desktop](https://www.docker.com/products/docker-desktop/). Please make sure that you install the right version
  for your operating system. Docker Desktop runs smoothly on Windows, macOS and Linux.

## Installation

- Clone the repository somewhere on you machine and navigate to it.

```shell
git clone
https://github.com/gmurambadoro/Custom-Background-Job-Runner-for-Laravel.git
cd Custom-Background-Job-Runner-for-Laravel
```

- When inside the folder of the repository, run the `./dkbuild.sh` for Linux/macOS or `./dkbuild.bat` for Windows. This
  command will build the docker containers required by the application.

![](./.screenshots/build.png)

- Now log into the main application container using `./dkconnect.sh`. If you are on Windows, then use `./dkconnect.bat`.

![](./.screenshots/connect.png)

- Now install Laravel and its `composer` packages.

`composer install`

![](./.screenshots/composer.png)

- Create the `.env` file, generate the `keypair` and run some migrations. You might need to fix some file permissions as
  well.

```shell
cp .env.example .env
php artisan key:generate
php artisan migrate
chmod -R 0777 storage/
```

🎉 Now the application is now ready at the following endpoints:

- [Website URL](http://localhost:42880)
- [phpMyAdmin for the Database](http://localhost:42881)

![](./.screenshots/application.png)

## How-to Guides

### Add some background Jobs

#### Using the web form

You can add some jobs from the application by clicking on the `+ New Job` button.

![](./.screenshots/new-job.png)

Complete the form and save your changes. A new job will be created.

#### Using the `runBackgroundJob` script that loads some example jobs

Inside the container, run the following command to load a list of example jobs.

```shell
php artisan runBackgroundJob
```

**Note:** You must only run this command after you have logged into the container via `./dkconnect.sh` or
`./dkconnect.bat` for Windows.

