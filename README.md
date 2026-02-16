# 🛠️ Projekt Prestashop

## 📦 Opis projektu

Ten projekt to wdrożenie strony internetowej opartej na silniku **PrestaShop**.

Może być używany jako baza do dalszego rozwoju, migracji środowiska lub testów funkcjonalnych.

---

## ⚙️ Technologia

- **Silnik:** PrestaShop `9.0.1`
- **Język programowania:** PHP `8.4`
- **Baza danych:** MariaDB `10.11.14`
- **Serwer WWW:** Apache `2.4+`
- **Composer:** 2.8.11

---

## 📁 Struktura plików / Co śledzimy w Git

> Uwaga: W repozytorium śledzony jest wyłącznie motyw i moduły. Pliki core powinny być instalowane przez zewnętrzne narzędzia (np. `composer`, `git submodule` lub `docker`).

### Przykładowa struktura:

```
├── .gitignore
├── README.md
├── override/
├── modules/
└── themes/
    └── maxstol/
```

---

## 🔐 Bezpieczeństwo

Upewnij się, że dane logowania do bazy danych, klucze API i inne dane wrażliwe **nie są** przechowywane w repozytorium. Używaj `.env` lub lokalnych plików konfiguracyjnych dodanych do `.gitignore`.

---

## 🔄 Deployment

Deployment można przeprowadzić ręcznie lub z wykorzystaniem CI/CD (np. GitHub Actions, GitLab CI, Bitbucket Pipelines).