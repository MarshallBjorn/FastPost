@echo off
REM Initialize FastPost project with Docker and run seeders
REM ------------------------------------------------------

echo Changing directory to src...
cd src || (
    echo Failed to change directory to src
    pause
    exit /b 1
)

echo Current directory: %CD%
echo Copying .env.example to .env...
copy /Y .env.example .env || (
    echo Failed to copy .env file
    pause
    exit /b 1
)


echo Returning to project root directory...
cd .. || (
    echo Failed to return to root directory
    pause
    exit /b 1
)

echo Current directory after cd ..: %CD%
echo Checking docker-compose version...
docker-compose --version || (
    echo docker-compose command not found
    pause
    exit /b 1
)

echo Building and starting Docker containers...
docker-compose up -d --build || (
    echo Docker-compose failed
    echo Make sure Docker is installed and running
    pause
    exit /b 1
)

echo Generating application key...
docker-compose exec app php artisan key:generate || (
    echo Failed to generate application key
    pause
    exit /b 1
)

echo Running fresh migrations...
docker-compose exec app php artisan migrate:fresh || (
    echo Database migration failed
    pause
    exit /b 1
)


echo.
echo Laravel application should now be accessible at:
echo http://localhost:8000
echo.

REM Prompt user to seed the database
set /p SEED_DB=Would you like to seed the database? (Y/N): 
if /i "%SEED_DB%"=="Y" (
    echo Installing FakerPHP...
    docker-compose exec app composer require fakerphp/faker --dev || (
        echo Failed to install FakerPHP
        pause
        exit /b 1
    )

    echo Seeding the database...
    docker-compose exec app php artisan db:seed || (
        echo Database seeding failed
        pause
        exit /b 1
    )

    echo Database seeding completed successfully.
) else (
    echo Skipping database seeding.
)


echo.
echo Laravel application should now be accessible at:
echo http://localhost:8000
echo.

pause