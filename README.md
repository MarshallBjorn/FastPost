## Running project locally

First, create ```.env``` file in src:
```bash
cd src
cp .env.example .env
```

Then to run locally type
```
docker-compose up -d --build
docker-compose exec app php artisan migrate
```

Then your laravel application should now be accessible at:
```
http://localhost:8000
```

### Small cheatsheet for Docker-compose
- To stop the containers:
  ```bash
  docker-compose down
  ```

- To start the containers again:
  ```bash
  docker-compose up -d
  ```

- To run Artisan commands:
  ```bash
  docker-compose exec app php artisan [command]
  ```

- To enter the app container:
  ```bash
  docker-compose exec app bash
  ```

- To view logs:
  ```bash
  docker-compose logs -f
  ```
---

- To seed database:
  ```bash
  docker-compose exec app composer require fakerphp/faker --dev
  docker-compose exec app php artisan migrate:fresh
  docker-compose exec app php artisan db:seed
  ```
---

**Notes**
1. The setup uses PostgreSQL 15 with Alpine Linux for a lightweight container.
2. Nginx is configured to serve the Laravel application.
3. The PHP container includes all necessary extensions for Laravel and PostgreSQL.
4. The database data is persisted in a Docker volume so it survives container restarts.
5. The composer service ensures dependencies are installed during the build process.


## TODO Revise this below readme section.
### Baza danych

---
### **Tabela: `Paczkomaty`**
| Kolumna             | Typ danych         | Opis                                 |
|---------------------|--------------------|---------------------------------------|
| `id`                | INT (PK)           | Unikalny identyfikator paczkomatu     |
| `nazwa`             | VARCHAR            | Nazwa paczkomatu (np. WAW01A)         |
| `miasto`            | VARCHAR            | Miasto                                |
| `kod_pocztowy`      | VARCHAR            | Kod pocztowy                          |
| `szerokosc_geo`     | DECIMAL            | Szerokość geograficzna                |
| `dlugosc_geo`       | DECIMAL            | Długość geograficzna                  |
| `status`            | ENUM               | np. 'aktywne', 'niedostępne', 'serwis'|

#### Widoki do tabeli `Paczkomaty`
- Widok ze wszystkimi paczkomatami na mapie
- Widok do administracji paczkomatami

---

### **Tabela: `Skrytki`**
| Kolumna             | Typ danych         | Opis                                   |
|---------------------|--------------------|----------------------------------------|
| `id`                | INT (PK)           | Unikalny identyfikator skrytki         |
| `paczkomat_id`      | INT (FK → Paczkomaty.id) | Przynależność do paczkomatu      |
| `rozmiar`           | ENUM               | np. 'S', 'M', 'L'                      |
| `paczka_id`         | INT (FK → Paczki.id) (NULL) | Paczka znajdująca się w skrytce |
| `kod_odbioru`       | VARCHAR (NULL)     | Kod obioru przesylki z paczkomatu         |

---

### **Tabela: `Sortownie`**
| Kolumna             | Typ danych         | Opis                                  |
|---------------------|--------------------|---------------------------------------|
| `id`                | INT (PK)           | Unikalny identyfikator sortowni       |
| `miasto`            | VARCHAR            | Miasto                                |
| `kod_pocztowy`      | VARCHAR            | Kod pocztowy                          |
| `szerokosc_geo`     | DECIMAL            | Szerokość geograficzna                |
| `dlugosc_geo`       | DECIMAL            | Długość geograficzna                  |
| `status`            | ENUM               | np. 'aktywna', 'niedostępna', 'serwis'|

#### Widoki do tabeli `Sortownie`
- Widok ze wszystkimi sortowniami na mapie
- Widok do administracji sortowniami
  
### **Tabela: `SortowaniePolaczenia`**
| Kolumna              | Typ danych               | Opis                                     |
|----------------------|--------------------------|------------------------------------------|
| `id`                 | INT(PK)                  | Unikalny identyfikator sortowni          |
| `from_warehouse_id`  | INT (FK -> sortownia.id) | ID sortowni z której wychodzi połączenie |
| `to_warehouse_id`    | INT (FK -> sortownia.id  | ID sortowni do której idzie polączenie   |
| `dystans_km`         | DECIMAL                  | Odległość pomiędzy sortowniami           |

---

### **Tabela: `Uzytkownicy`**
| Kolumna             | Typ danych         | Opis                                  |
|---------------------|--------------------|----------------------------------------|
| `id`                | INT (PK)           | Unikalny identyfikator użytkownika    |
| `imie`              | VARCHAR            | Imię                                  |
| `nazwisko`          | VARCHAR            | Nazwisko                              |
| `email`             | VARCHAR            | E-mail                                |
| `telefon`           | VARCHAR            | Numer telefonu                        |
| `haslo`             | VARCHAR            | zaszyfrowane hasło        |

### **Tabela: `Pracownicy`**
| Kolumna             | Typ danych         | Opis                                  |
|---------------------|--------------------|----------------------------------------|
| `uzytkownik_id`       | INT (FK → Uzytkownicy.id) (PK) | ID uzytkownika |
| `typ_pracownika`      | ENUM | 'admin', 'kurier', 'magazyn' |
| `sortownia_id`        | INT (FK → sortownia.id) (NULL) | ID sortowni do jakiej jest przypisany |
| `data_zatrudnienia`   | DATETIME           | Data zatrudnienia |
| `data_rozwiazania`    | DATETIME (NULL)    | Data rozwiązania umowy |

#### Logika stojąca za uzytkownikami (i pracownikami)
- Uzytkownik moze stworzyć konto na 'oficjalnej' stronie.
- Uzytkownik powinien potwierdzic swoj e-mail (system weryfikacji emaili)
- Pracownicy są tworzeni przez adminów i przypisywani do oddziałów (sortowni)

#### Widoki uzytkownikow
- Logowanie
- Rejestracja wraz z weryfikacja e-maila
- Zmiana hasla
- Edytowanie podstawowych informacji 

### **Tabela: `Weryfikacja`**
| Kolumna             | Typ danych         | Opis                                  |
|---------------------|--------------------|----------------------------------------|
| `uzytkownik_id`       | INT (FK → Uzytkownicy.id) (PK) | ID uzytkownika |
| `kod`                 | VARCHAR    | Wygenerowany kod potwierdzający email |
| `data_wygaśnięcia`    | DATETIME   | Data wygaśnięcia kodu (po której można go usunąć z bazy) |

---

### **Tabela: `Paczki`**
| Kolumna             | Typ danych         | Opis                                   |
|---------------------|--------------------|----------------------------------------|
| `id`                | INT (PK)           | Unikalny identyfikator paczki             |
| `nadawca_id`        | INT (FK → Uzytkownicy.id) | Nadawca                            |
| `docelowy_paczkomat_id`| INT (FK → Paczkomat.id) | Docelowy paczkomat paczk          |
| `email_odbiorcy`    | VARCHAR            | E-mail odbiorcy                           |
| `telefon_odbiorcy`  | VARCHAR            | numer telefonu odbiorcy                   |
| `odbiorca_id`       | INT (FK → Uzytkownicy.id) | Id odbiorcy - jeśli tylko znalezionio e-mail uzytkownika przy nadawaniu paczki    |
| `status`            | ENUM               | np. 'w drodze', 'w paczkomacie', 'odebrana' |
| `data_nadania`      | DATETIME           | Data nadania paczki                   |
| `data_dostarczenia` | DATETIME (NULL)    | Data dostarczenia (jeśli dotyczy)     |
| `data_odbioru`      | DATETIME (NULL)    | Data odbioru przez odbiorcę           |

#### Widoki `Paczek`
- Widok ze wszystkimi paczkomatami na mapie przy nadawniu przesyłki 
    - Nadawanie paczki z odpowiednim formularzem
- Szczegolowy widok co sie dzieje z paczka (nalezy pobrac informacje z odpowiedniej historii paczki) dla obu uzytkownikow
- Odbieranie zamowienia - stworzenie widoku dla odbierania paczki (symulacja paczkomatu), w tym celu pobierac kod odbioru oraz telefon odbierajacego
- Odbieranie zamowienia - z poziomu panelu klienta (aplikacji)
- System do wysylania kodów odbioru / informacji uzytkownikom na e-maila/telefon, jeszcze przemyslec czy zrobic to na poziomie 'chrono taska', czy uzyc 'asynchronous task queue'
    - Odbywa się w momencie gdy paczka staje się gotowa do odbioru

---

### **Tabela: `Aktualizacje`**
| Kolumna             | Typ danych         | Opis                                      |
|---------------------|--------------------|-------------------------------------------|
| `id`                | INT (PK)           | ID historii                               |
| `paczki_id`     | INT (FK → Paczki.id) | ID paczki |
| `wiadomosc`         | ENUM | 'Wyslano', 'Odebrano w magazynie', 'Wydano do doreczenia' |
| `ostatni_kurier_id`     | INT (FK → uzytkownik.id ) | ostatni kurier który był odpowiedzialny za paczkę (nadpisywany automatycznie gdy 'skanuje przesylke) |
| `ostatnia_sortownia_id`     | INT (FK → sortownia.id ) | ostatnia sortownia w której znajdowała się paczka |
| `utworzono` | DATETIME | kiedy miało miejsce wydarzenie |

---

Logika wysyłania paczki:
- osoba wysyłająca podaje (musi posiadac konto! - narazie zrobic na sztywno) - potrzebna autoryzacja
  - (Dzisiaj tak naprawde się generują etykiety z kodami QR wystawiane przez API, pomiedzy systemami np vinted - paczkomaty.)
  - Formularz posiadajacy:
    - Paczkomat startowy
    - E-mail
    - Telefon
    - Paczkomat docelowy
    - TODO jesli rozwijany bedzie rozwijany ten system.
      - E-mail uytkownika (jesli nie ma go request)
      - Telefon uzytkownika (jesli nie ma go w request)
  - Po walidacji danych:
    - Odbiorca id - jesli e-mail znaleziono w bazie danych.
    - automatyczny status
    - automatyczna data nadania
    - Stworzenie historii paczki 
  - Generowanie kodu QR do route z historia paczki
