# Hotel-Management-System

A modern, secure hotel booking website with a content management system. Customers can browse rooms and reserve dates; staff can manage bookings, payments, rooms and staff from the admin panel.

## Tech Stack

```sh
HTML
CSS
JAVASCRIPT (vanilla + Bootstrap)
PHP (mysqli prepared statements)
MariaDB / MySQL
Chart.js
```

## What was improved

- **Security**: All SQL is now parameterised through prepared statements (SQL-injection safe). Output is HTML-escaped (XSS safe). New signups store passwords with `password_hash()`; legacy plain-text passwords from the original database still verify automatically. Login sessions use `session_regenerate_id()`.
- **Access control**: Pages distinguish between customers and staff via a session `role`, so staff-only pages block ordinary users (previously any logged-in user could open the admin panel).
- **State-changing actions** (confirm / delete) now use `POST` forms instead of raw `GET` links.
- **Explorer fixes**: broken iframe tab (5th tab), hardcoded `localhost` redirect, duplicate country lists and pricing logic were removed. Pricing rules live in one place (`includes/helpers.php`).
- **Design**: refreshed hero landing page, login/signup screen and a unified admin theme (navy + gold).

## Requirements Windows

```sh
1 Download & Install: XAMPP in C:\xampp (default)
2 Clone this repository in C:\xampp\htdocs
3 Run XAMPP and start "Apache" and "MySQL"
4 Open "localhost/phpmyadmin/"
5 Create a database named "bluebirdhotel"
6 Click import and select "bluebirdhotel.sql"
7 Open "http://localhost/Hotel-Management-System/"
8 Register and login
```

## Requirements Linux (Rocky Linux 9)

```sh
1 Install dnf package manager
2 Clone this repository in your home directory
3 Enable execute permissions `chmod 755 setup.sh`
4 Login as root or use `sudo su - root`
5 Run setup.sh `./setup.sh`
6 Open "http://localhost/Hotel-Management-System/"
7 Register and login
```

> `setup.sh`, credentials and config can be adjusted in `config.php`.

## Default credentials

```sh
== Staff Login ==

Email    : Admin@gmail.com
Password : 1234
```

```sh
== Demo user ==

Email    : tusharpankhaniya2202@gmail.com
Password : 123
```

New signups are stored with bcrypt hashes; existing plain-text passwords in the seeded database keep working for a smooth migration.