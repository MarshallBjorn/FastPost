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

Napisać, co trzeba mieć zainstalowane (oraz inne potrzebne dodatkowe informacje).

```
Umieścić komendy z start.bat

```

Przykładowi użytkownicy aplikacji:
* administrator: jan@email.com 1234
* użytkownik: anna@email.com 1234
* ...
* ...

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
