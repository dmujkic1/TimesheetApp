
# ⏱️ TimesheetApp

**TimesheetApp** je web aplikacija za upravljanje radnim vremenom zaposlenika. Omogućava evidentiranje i pregled radnih sati, upravljanje projektima i timovima, odobravanje unosa od strane menadžera te generisanje izvještaja.

---

## 🛠️ Tehnologije

- **Backend**: Laravel 12 (PHP)
- **Frontend**: Vue 3 + Inertia.js
- **Baza podataka**: PostgreSQL
- **Autentikacija**: Laravel Spatie
- **UI**: TailwindCSS + Bootstrap + Headless UI + Heroicons

---

## 📦 Ključne funkcionalnosti

- ✅ Unos radnih sati po projektima i danima
- ✅ Zahtjev za out-of-office vrijeme (godišnji odmori, bolovanje, privatni dani, vjerski praznici...)
- ✅ Pauze i višestruki unosi po danu
- ✅ Validacija preklapanja vremena
- ✅ Odobravanje timesheet unosa (menadžer)
- ✅ Odobravanje OOI unosa (menadžer)
- ✅ Upravljanje zaposlenicima, timovima i projektima
- ✅ Izvještaji: broj sati po korisniku, periodu i projektu
- ✅ Export izvještaja u CSV Excel file
- ✅ Role-based pristup (Admin, Manager, Employee, User)
- ✅ Custom 404 error screen

---

## 🧭 Struktura repozitorija

```
TimesheetApp/
├── app/
│   └── Http/
│       └── Controllers/
│           └── TimesheetController.php
├── resources/
│   └── js/
│       └── Pages/
│           └── Timesheets/
│               ├── Index.vue
│               ├── SidebarTimesheet.vue
│               └── ...
├── routes/
│   └── web.php
├── database/
│   └── migrations/
│   └── seeders/
└── ...
```

---

## 🧑‍💼 Uloge korisnika

| Uloga      | Opis                                                   |
|------------|--------------------------------------------------------|
| Admin      | Upravljanje korisnicima, projektima, timovima          |
| Manager    | Za sad neodređeno                                      |
| Employee   | Unos i pregled vlastitih radnih sati                   |
| User       | Ograničen pristup                                      |

---

## 📊 Baza podataka

Dizajn baze podataka možete pogledati ovdje:  
🔗 **[Link do baze podataka (ER diagram)](https://drawsql.app/teams/dmujkic1/diagrams/copy-of-timesheetapp)**  

---

## 🚀 Pokretanje aplikacije lokalno (nedovoljno definisano još)

1. Kloniraj repozitorij:
   ```bash
   git clone https://github.com/your-username/TimesheetApp.git
   ```

2. Instaliraj Laravel zavisnosti:
   ```bash
   composer install
   ```

3. Instaliraj npm pakete:
   ```bash
   npm install && npm run dev
   ```

4. Postavi `.env` fajl i pokreni migracije:
   ```bash
   cp .env.example .env
   php artisan key:generate
   php artisan migrate --seed
   ```

5. Pokreni lokalni server:
   ```bash
   php artisan serve
   ```

---

## ✅ TODO / Planirano

- [ ] Email i popup notifikacije zaposlenima i menadžerima kada se unos odobri odnosno pošalje na review
- [ ] Filtriranje i izvještaja po korisniku
- [ ] Client & Team CRUD
- [ ] Drag-and-drop dodjela zaposlenika timovima
- [ ] Bulk accept/reject za timesheet i OOO unose
- [ ] SQLInjection zaštita
- [ ] Dashboard da menadžer vidi unose u kalendaru za zaposlenog po želji


---

## 📄 Licenca

Ovaj projekat je open-source i dostupan pod [MIT licencom](LICENSE).
