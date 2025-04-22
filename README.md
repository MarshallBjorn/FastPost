## Baza danych

---
### **Tabela: `Paczkomaty`**
| Kolumna             | Typ danych         | Opis                                 |
|---------------------|--------------------|---------------------------------------|
| `id`                | INT (PK)           | Unikalny identyfikator paczkomatu     |
| `nazwa`             | VARCHAR            | Nazwa paczkomatu (np. WAW01A)         |
| `adres`             | VARCHAR            | Pełny adres                           |
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
| `zajeta`            | BOOLEAN            | Czy skrytka jest zajęta                |

---

### **Tabela: `Paczki`**
| Kolumna             | Typ danych         | Opis                                  |
|---------------------|--------------------|----------------------------------------|
| `id`                | INT (PK)           | Unikalny identyfikator paczki         |
| `skrytka_id`        | INT (FK → Skrytki.id) | Skrytka, w której jest paczka      |
| `status`            | ENUM               | np. 'w drodze', 'w paczkomacie', 'odebrana' |
| `data_nadania`      | DATETIME           | Data nadania paczki                   |
| `data_dostarczenia` | DATETIME (NULL)    | Data dostarczenia (jeśli dotyczy)     |
| `data_odbioru`      | DATETIME (NULL)    | Data odbioru przez odbiorcę           |

---

### **Tabela: `Uzytkownicy`**
| Kolumna             | Typ danych         | Opis                                  |
|---------------------|--------------------|----------------------------------------|
| `id`                | INT (PK)           | Unikalny identyfikator użytkownika    |
| `imie`              | VARCHAR            | Imię                                  |
| `nazwisko`          | VARCHAR            | Nazwisko                              |
| `email`             | VARCHAR            | E-mail                                |
| `telefon`           | VARCHAR            | Numer telefonu                        |
| `haslo`             | VARCHAR (nada się na hasha(?)) | zaszyfrowane hasło        |
| `typ_uzytkownika`   | ENUM | 'admin', 'kurier', 'zwykly', 'magazyn'              |

#### Logika stojąca za uzytkownikami
- Uzytkownik moze stworzyć konto na 'oficjalnej' stronie. Wtedy typ uzytkownika to w naszym enumie `'zwykly'`
- Uzytkownik kurier jest tworzony przez admina
- Uzytkownik powinien potwierdzic swoj e-mail (system weryfikacji emaili)

#### Widoki uzytkownikow
- Logowanie
- Rejestracja wraz z weryfikacja e-maila
- Zmiana hasla
- edytowanie podstawowych informacji 

**Prawdopodobnie** do weryfikacji e-maila trzeba bedzie dorobic odpowiednie kolumny, badz osobna tabele.

---

### **Tabela: `Zamowienia`**
| Kolumna             | Typ danych         | Opis                                     |
|---------------------|--------------------|-------------------------------------------|
| `id`                | INT (PK)           | ID zamówienia                             |
| `nadawca_id`        | INT (FK → Uzytkownicy.id) | Nadawca                            |
| `paczka_id`         | INT (FK → Paczki.id) | Powiązana paczka                        |
| `status`            | ENUM               | 'w trakcie', 'zakończone', 'anulowane'    |
| `docelowy_paczkomat_id`| INT (FK → Paczkomat.id) | Docelowy paczkomat paczk          |
| `email_odbiorcy`    | VARCHAR            | E-mail odbiorcy                           |
| `telefon_odbiorcy`  | VARCHAR            | numer telefonu odbiorcy                   |
| `odbiorca_id`       | INT (FK → Uzytkownicy.id) | Id odbiorcy - jeśli tylko znalezionio e-mail uzytkownika przy nadawaniu paczki    |
| `kod_odbioru`       | VARCHAR            | kod obioru przesylki z paczkomatu         |

#### Widoki `Zamowien`
- Widok ze wszystkimi paczkomatami na mapie przy nadawniu przesyłki 
    - Nadawanie paczki z odpowiednim formularzem
- Szczegolowy widok co sie dzieje z paczka (nalezy pobrac informacje z odpowiedniej historii paczki) dla obu uzytkownikow
- Odbieranie zamowienia - stworzenie widoku dla odbierania paczki (symulacja paczkomatu), w tym celu pobierac kod odbioru oraz telefon odbierajacego
- Odbieranie zamowienia - z poziomu panelu klienta (aplikacji)
- System do wysylania kodów odbioru / informacji uzytkownikom na e-maila/telefon, jeszcze przemyslec czy zrobic to na poziomie 'chrono taska', czy uzyc 'asynchronous task queue'
    - W jakim momencie maja byc wysylane te kody?
        - Gdy nadano paczke -> powiadomienie odbiorcy
        - Gdy paczka jest do odbioru -> powiadomienie odbiorcy


### **Tabela: `Historia Paczki`**
| Kolumna             | Typ danych         | Opis                                      |
|---------------------|--------------------|-------------------------------------------|
| `id`                | INT (PK)           | ID historii paczki                        |
| `zamowienie_id`     | INT (FK → Zamowienia.id) | ID zamowienia                       |


### **Tabela: `Historia-Wiadomosc`**
| Kolumna             | Typ danych         | Opis                                      |
|---------------------|--------------------|-------------------------------------------|
| `id`                | INT (PK)           | ID historii                               |
| `zamowienie_id`     | INT (FK → historia-paczki.id) | ID historii paczki             |
| `wiadomosc`         | ENUM | 'Wyslano', 'Odebrano w magazynie', 'Wydano do doreczenia' |
| `ostatni_kurier_id`     | INT (FK → uzytkownik.id ) | ostatni kurier który był odpowiedzialny za paczkę (nadpisywany automatycznie gdy 'skanuje przesylke) |
| `utworzono` | DATETIME | kiedy miało miejsce wydarzenie |

---
