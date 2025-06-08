# FastPost: Kiedy prędkość jest obowiązkowa

[Projektowe repozytorium](https://github.com/MarshallBjorn/FastPost.git)

[Tablica projektowa](https://github.com/users/MarshallBjorn/projects/3)

---


### O projekcie

Projekt ma na celu stworzenie symulacji działania systemu paczkomatów, inspirowanego rozwiązaniami stosowanymi przez firmy kurierskie, takie jak InPost. System został zaprojektowany w sposób modułowy i odzwierciedla kluczowe procesy realizowane w rzeczywistych systemach dostarczania i odbioru paczek przez paczkomaty.

Główne cele projektu to:

- Zrozumienie i odwzorowanie architektury rozproszonego systemu paczkomatowego,
- Implementacja podstawowych funkcjonalności użytkownika, kuriera oraz administratora systemu,
- Wizualizacja i uproszczenie przepływu danych między poszczególnymi modułami systemu (np. interfejs użytkownika, serwery, bazy danych, urządzenia paczkomatowe),
- Symulacja typowych scenariuszy, takich jak nadawanie, odbiór i śledzenie paczek.

Projekt uwzględnia zarówno warstwę frontendową (interfejs użytkownika), jak i backendową (logika działania i komunikacja z bazą danych), umożliwiając realistyczną symulację interakcji między użytkownikami a systemem.

---

### Zespół B2

| Profil | Rola |
| ------ | ------ |
| [Oleksii Nawrocki](https://github.com/MarshallBjorn) | lider zespołu |
| [Tomasz Nowak](https://github.com/Tnovyloo) | członek zespołu |
| [Dawid Bar](https://github.com/noradenshi) | członek zespołu |

---


## Opis projektu

Projekt przedstawia symulację systemu paczkomatów, stworzoną w środowisku akademickim przy użyciu frameworka Laravel (PHP) oraz architektury MVC (Model-View-Controller). Główne zadanie aplikacji to odwzorowanie procesów związanych z nadawaniem, transportem i odbiorem paczek w sposób zbliżony do funkcjonowania systemów firm kurierskich, takich jak InPost.

### Aplikacja oparta jest o wzorzec MVC, zapewniając logiczny podział odpowiedzialności:
- Model – reprezentuje dane (np. paczka, użytkownik, paczkomat) i operacje na bazie danych, korzystając z ORM Eloquent.
- View – interfejs użytkownika utworzony w Blade, odpowiadający za prezentację danych.
- Controller – pośredniczy między warstwami, obsługując żądania użytkownika i zwracając odpowiednie odpowiedzi.

### Wykorzystane technologie:
- Laravel – framework backendowy (PHP) wspierający routing, middleware, migracje i obsługę sesji,
- Blade – silnik szablonów do dynamicznych widoków,
- PostgreSQL – relacyjna baza danych do przechowywania informacji o paczkach, użytkownikach, paczkomatach i logach systemowych,
- Docker – narzędzie do konteneryzacji, wykorzystane jako środowisko uruchomieniowe i testowe (kontenery dla PHP, serwera web, bazy danych PostgreSQL itp.),
- Composer – zarządzanie zależnościami PHP.

### Projekt miał na celu:
- Praktyczne wykorzystanie wiedzy z zakresu programowania w Laravelu i wzorca MVC,
- Stworzenie realistycznej symulacji systemu logistycznego,
- Nauczanie pracy zespołowej w kontenerowym środowisku deweloperskim (Docker),
- Zaprojektowanie spójnego systemu CRUD z różnymi poziomami dostępu użytkowników,
- Ćwiczenie pracy z relacyjną bazą danych i ORM.

### Dostępne funkcjonalności:
- zakładanie konta,
- nadawania paczek,
- odbieranie paczek,
- śledzenie paczek,
- zarzadzanie danymi przez CRUD
- w panelu kuriera dostawa i odpiór paczek
  pomiędzy paczkomatami oraz sortowniami


### Uruchomienie aplikacji
Przygotowano poradnik uruchomienia aplikacji laravel w środowisku dockera dla systemów: Windows, Linux, MacOS

#### Windows:
Włączyć skrypt ```start.bat```

```
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

REM SOMETIMES IT FAILS - READ THE README.MD ! 
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
```

Przy Seedowaniu bazy danych, losowo występuje problem z instalacją Fakera w kontenerze (W szczególności na systemie Windows przy włączeniu start.bat), należy wtedy samemu uruchomić komendę:

```
docker-compose run --rm composer require fakerphp/faker --dev
docker-compose exec app php artisan db:seed
```



#### MacOS/Linux

Najpierw utwórz plik ```.env``` w folderze src:
```bash
cd src
cp .env.example .env
```

Następnie, aby uruchomić lokalnie, wpisz:
```
docker-compose up -d --build
docker-compose exec app php artisan migrate
```

Twoja aplikacja Laravel powinna być teraz dostępna pod adresem:
```
http://localhost:8000
```

#### Krótki poradnik dla Docker-compose
- Aby zatrzymać kontenery:
  ```bash
  docker-compose down
  ```

- Aby ponownie uruchomić kontenery:
  ```bash
  docker-compose up -d
  ```

- Aby uruchomić polecenia Artisan:
  ```bash
  docker-compose exec app php artisan [polecenie]
  ```

- Aby wejść do kontenera aplikacji:
  ```bash
  docker-compose exec app bash
  ```

- Aby wyświetlić logi:
  ```bash
  docker-compose logs -f
  ```
---

#### Seedowanie bazy danych
- Aby wypełnić bazę danych danymi testowymi:
  ```bash
  docker-compose exec app composer require fakerphp/faker --dev
  docker-compose exec app php artisan migrate:fresh
  docker-compose exec app php artisan db:seed
  ```

  - Jeśli instalacja fakerphp nie powiedzie się (szczególnie na Windowsie), spróbuj:
    ```
    docker-compose run --rm composer require fakerphp/faker --dev
    docker-compose exec app php artisan db:seed
    ```


### Baza danych

![Diagram ERD](./docs-img/erd.png)

## Widoki aplikacji 

![Strona główna](./docs-img/screen.png)
*Strona główna*

![Strona główna](./docs-img/screen.png)
*Logowanie*

![Strona główna](./docs-img/screen.png)
*Rejestracja*

...

*CRUD*

...

*Zarządzanie użytkownikami*

...

*Profil użytkownika*

...

*Dokonanie zakupu/wypożyczenia...*

...

itd.

...


...
