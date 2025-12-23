# Abnehm-App (PHP 8 + MySQL, XAMPP)

Komplettes, lauffähiges Beispielprojekt für eine Abnehm-/Gewichts-Tracking-App mit Gamification.

## Features
- Registrierung/Login, Passwort-Reset (Token wird in der UI angezeigt)
- Profil mit Privatsphäre-Schalter (öffentlich/privat), Größe, Zielgewicht/-datum
- Gewichtstracking mit Validierung, Verlaufstabelle, Trendkarten, Schnell-Eintrag, Warnung bei großen Sprüngen
- Ziele & Streaks inkl. Badge-Engine (Erster Eintrag, 7 Tage, 1/5/10 kg, Ziel erreicht, Monat voll, Konstanz)
- Leaderboards nur mit Nickname + aggregierten Statistiken (keine E-Mail/Rohgewichte)
- Challenges (30 Tage eintragen, 8 Wochen Konstanz, 10k Schritte) + Fortschrittsberechnung
- Gruppen mit Invite-Codes, Owner/Member, interne Ranglisten vorbereitet
- CSV-Export/-Import der Gewichtsdaten
- Fortschrittsfotos mit sicherem Upload (Privat)
- CSRF-Schutz, Prepared Statements, Login-Rate-Limit, sichere Sessions

## Ordnerstruktur
```
/public
  index.php            Front Controller/Router
  .htaccess            Rewrite auf index.php
  assets/css, assets/js
  uploads/             Benutzer-Uploads (per .htaccess gegen PHP-Ausführung geschützt)
/app
  config.php           DB-Settings
  bootstrap.php        Autoload, Session, CSRF, DB
  routes.php           Routen-Map
  middleware/         Auth, CSRF, Rate-Limit
  controllers/        PHP-Controller
  models/             PDO-Modelle
  services/           Badge-, Leaderboard-, Challenge-, CSV-, Photo-Logik
  views/              Server-Rendered-Views
/sql
  create_tables.sql    Schema
  seed.sql             Seed-Daten
```

## Setup (XAMPP)
1. XAMPP installieren (Apache + PHP 8 + MySQL/MariaDB).
2. Projekt nach `htdocs/abnehm-app` kopieren oder als VirtualHost einrichten.
3. Schreibrechte für `public/uploads` setzen (unter Linux z.B. `chmod -R 755 public/uploads`).
4. Datenbank anlegen:
   ```sql
   CREATE DATABASE biggestlooser CHARACTER SET utf8mb4;
   USE biggestlooser;
   SOURCE /pfad/zum/projekt/sql/create_tables.sql;
   SOURCE /pfad/zum/projekt/sql/seed.sql;
   ```
5. In `app/config.php` DB-Zugangsdaten setzen (`DB_USER`, `DB_PASS`).
6. Apache Rewrite aktivieren (.htaccess erlauben). Unter Windows: `AllowOverride All` für htdocs, Apache neu starten.
7. Aufruf im Browser: `http://localhost/abnehm-app/public/` (oder entsprechendem Host).

### Beispiel-Logins
- alice@example.com / `password`
- bob@example.com / `password`
- carla@example.com / `password`

## Sicherheitshinweise
- Passwörter mit `password_hash`/`password_verify`
- Sessions mit `httponly`, `samesite=Lax`, optional `secure` bei HTTPS (siehe `bootstrap.php`)
- CSRF-Token in jedem POST-Formular (`csrf_field()`)
- Prepared Statements (PDO) in allen Queries
- Login-Brute-Force-Schutz: Rate-Limit per `login_attempts` (5 Fehlversuche/15 Min) + Logging
- Upload-Schutz: MIME-Check, Größenlimit, zufällige Dateinamen, `.htaccess` in `/public/uploads` verhindert PHP-Ausführung

## Technische Notizen
- Minimaler MVC-ähnlicher Ansatz: `public/index.php` routet anhand von `app/routes.php` auf Controller
- Services kapseln Kernlogik: BadgeService (regelbasierte Vergabe), LeaderboardService (Aggregationen), ChallengeService (Fortschritt), CsvService, PhotoService
- Views sind PHP-Templates mit Layout und Escaping-Helfer `e()`
- Chart.js wird lokal als schlanke Implementierung (`public/assets/js/chart.min.js`) eingebunden

## Datenschutz
- Öffentliche Profile zeigen nur Nickname + aggregierte Werte (Prozent, BMI-Kategorie, Badge-Anzahl, Eintragsanzahl)
- E-Mail-Adressen werden nirgendwo öffentlich gerendert
- Foto-Galerie ist nur für den eingeloggten User sichtbar

## Troubleshooting
- 404 trotz vorhandener Route: prüfe Apache `mod_rewrite` und `.htaccess`
- Upload schlägt fehl: prüfe `file_uploads=On`, `upload_max_filesize`, Verzeichnisrechte von `public/uploads`
- DB-Fehler: stimmen `DB_HOST/DB_USER/DB_PASS` in `app/config.php`? Wurde `create_tables.sql` importiert?

## TODO / Erweiterungen
- E-Mail-Versand für Passwort-Reset produktiv machen
- 2FA/One-Time-Codes für Login
- Streak-Freeze/Joker buchbar machen
- Mehr Badge-Regeln (Prozentuale Abnahme, Gruppen-Erfolge)
- HTTPS-Setup/Reverse-Proxy-Doku
- API-Endpunkte (JSON) + PWA
