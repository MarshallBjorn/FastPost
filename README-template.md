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

Gdy projekt jest pierwszy raz włączany, należy wygenerować klucz.
```
docker-compose exec app php artisan key:generate
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

![Erd](/docs-img/erd.png)

### Widoki aplikacji

---

#### Strona startowa (Landing Page)

![LandingPage](/docs-img/LandingPage.png)

---

#### Przegląd paczkomatów

![ClientBrowsePostmats](/docs-img/ClientBrowsePostmats.png)

---

#### Logowanie i rejestracja

![ClientLoginAndRegister](/docs-img/ClientLoginAndRegister.png)

---

#### Walidacja rejestracji

![ClientRegisterValidation](/docs-img/ClientRegisterValidation.png)

---

#### Weryfikacja e-mail

![ClientVerifyEmail](/docs-img/ClientVerifyEmail.png)

---

#### Ponowna wysyłka e-maila weryfikacyjnego

![ClientResentVerifyEmail](/docs-img/ClientResentVerifyEmail.png)

---

#### Przekierowanie po weryfikacji konta

![ClientVerifedAccountRedirect](/docs-img/ClientVerifedAccountRedirect.png)

---

#### Wysyłka paczki

![ClientSendParcel](/docs-img/ClientSendParcel.png)

---

#### Filtrowanie paczkomatów przy wysyłce paczki

![ClientSendParcelPostmatFiltration](/docs-img/ClientSendParcelPostmatFiltration.png)

---

#### Paczka wysłana

![ClientSendedParcel](/docs-img/ClientSendedParcel.png)

#### W przypadku braku miejsca w paczkomacie, rezerwowana jest inna skrytka.

![ClientSendedParcelReserve](/docs-img/ClientPackageSummaryOtherPostmat.png)

---

#### Śledzenie paczki (właściciel)

![ClientOwnerTrackParcel](/docs-img/ClientOwnerTrackParcel.png)

---

#### Śledzenie paczki (nieautoryzowane)

![ClientUnauthorizedTrackParcel](/docs-img/ClientUnauthorizedTrackParcel.png)

---

#### Moje wysłane paczki

![ClientYourSentPackages](/docs-img/ClientYourSentPackages.png)

---

#### Zarezerwowany schowek w paczkomacie

![ClientReservedStashInPostmat](/docs-img/ClientReservedStashInPostmat.png)

---

#### Pasek nawigacji kuriera

![CourierNavbar](/docs-img/CourierNavbar.png)

---

#### Pusta lista paczek do odbioru

![CourierPickupEmpty](/docs-img/CourierPickupEmpty.png)

---

#### Paczka uzytkownika po umieszczeniu w paczkomacie

![ClientYourSentPackgesAfterPutInStash](/docs-img/ClientYourSentPackgesAfterPutInStash.png)

---

#### Odbiór paczki po umieszczeniu w paczkomacie przez klienta

![CourierPickupAfterUserPutInStash](/docs-img/CourierPickupAfterUserPutInStash.png)

---

#### Pusta lista paczek u kuriera

![CourierCurrentPackagesEmpty](/docs-img/CourierCurrentPackagesEmpty.png)

---

#### Paczki u kuriera po odebraniu paczki

![CourierCurrentPackagesAfterTakingParcel](/docs-img/CourierCurrentPackagesAfterTakingParcel.png)

---

#### Śledzenie paczki po odbiorze przez kuriera

![ClientTrackPageAfterCourierPickup](/docs-img/ClientTrackPageAfterCourierPickup.png)

---

#### Paczki u kuriera po umieszczeniu w magazynie

![CourierCurrentPackagesAfterPuttingInWarehouse](/docs-img/CourierCurrentPackagesAfterPuttingInWarehouse.png)

---

#### Śledzenie paczki po umieszczeniu w magazynie

![ClientTrackPageAfterCourierPutInWarehouse](/docs-img/ClientTrackPageAfterCourierPutInWarehouse.png)

---
### Widok admina

#### Trasa paczki

![AdminPageRouteOfPackage](/docs-img/AdminPageRouteOfPackage.png)

---

#### Statystyki

![AdminStatistics](/docs-img/AdminStatistics.png)

---

#### Statystyki (widok 2)

![AdminStatistics2](/docs-img/AdminStatistics2.png)

---

#### Logistyka

![AdminLogistics](/docs-img/AdminLogistics.png)

---

#### Edycja magazynu (logistyka)

![AdminLogisticsEditWarehouse](/docs-img/AdminLogisticsEditWarehouse.png)

---

#### Dostawy

![AdminDeliveries](/docs-img/AdminDeliveries.png)

---

#### Dostawy (widok 2)

![AdminDeliveries2](/docs-img/AdminDeliveries2.png)

---

#### Tworzenie paczki (dostawy)

![AdminDeliveriesCreatePackage](/docs-img/AdminDeliveriesCreatePackage.png)

---

#### Aktualizacja danych

![AdminActualization](/docs-img/AdminActualization.png)

---

#### Konta użytkowników

![AdminAccounts](/docs-img/AdminAccounts.png)

---

#### Tworzenie konta użytkownika

![AdminAccountsCreate](/docs-img/AdminAccountsCreate.png)

---

#### Widok kuriera między magazynowego (kurier który jeździ tylko pomiędzy magazynami)

![WarehouseCourier](/docs-img/WarehouseCourier.png)

---

#### Rozpoczęcie trasy

![WarehouseTakeRoute](/docs-img/WarehouseTakeRoute.png)

---

#### Aktualne paczki u kuriera (1)

![WarehouseCourierCurrentPackges1](/docs-img/WarehouseCourierCurrentPackges1.png)

---

#### Potwierdzenie przyjazdu kuriera

![WarehouseCourierConfirmArrival](/docs-img/WarehouseCourierConfirmArrival.png)

---

#### Rozpoczęcie trasy zwrotnej kuriera

![WarehouseCourierStartReturnTrip](/docs-img/WarehouseCourierStartReturnTrip.png)

---

#### Aktualne paczki u kuriera (zwrot)

![WarehouseCourierCurrentPackages2_return](/docs-img/WarehouseCourierCurrentPackages2_return.png)

---

#### Potwierdzenie zwrotu przez kuriera

![WarehouseCourierConfirmReturn](/docs-img/WarehouseCourierConfirmReturn.png)

---



### TODO odbieranie paczki 