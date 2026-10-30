# Online Bookstore Management System — DBMS Mini Project

Stack: **HTML + CSS + PHP** (frontend) → **MySQL** (backend), exactly as
suggested in the project brief.

## Folder structure

```
Online_Bookstore/
├── database/
│   ├── 01_database.sql      -- CREATE DATABASE
│   ├── 02_ddl.sql            -- CREATE TABLE + constraints (7 tables)
│   ├── 03_dml.sql            -- INSERT / UPDATE / DELETE / SEARCH
│   ├── 04_dcl.sql            -- CREATE USER / GRANT / REVOKE
│   ├── 05_trigger.sql        -- auto stock-reduction trigger
│   └── 06_procedure.sql      -- procedure + function (PL/SQL style)
│
├── frontend/
│   ├── db_connect.php        -- shared MySQL connection + session
│   ├── style.css
│   ├── index.php             -- home page
│   ├── register.php          -- registration + validation
│   ├── login.php             -- login (admin/customer)
│   ├── logout.php
│   ├── books.php             -- browse + search books
│   ├── place_order.php       -- creates order (fires the trigger)
│   ├── orders.php            -- customer's own orders
│   ├── admin.php             -- admin dashboard
│   ├── add_book.php          -- admin: add book
│   ├── manage_books.php      -- admin: update / delete book
│   ├── view_orders.php       -- admin: all orders
│   └── view_users.php        -- admin: all users
│
├── er_diagram/                -- put your ER diagram image here
├── schema/                    -- put your schema diagram image here
├── screenshots/                -- put screenshots for the report here
└── documentation/              -- final report goes here
```

## How to run it

1. **Start MySQL** (XAMPP / WAMP / standalone MySQL server).
2. **Create the schema**, in order:
   ```
   mysql -u root -p < database/01_database.sql
   mysql -u root -p < database/02_ddl.sql
   mysql -u root -p < database/03_dml.sql
   mysql -u root -p < database/04_dcl.sql
   mysql -u root -p < database/05_trigger.sql
   mysql -u root -p < database/06_procedure.sql
   ```
   (Or run each `.sql` file's contents in phpMyAdmin / MySQL Workbench.)
3. **Set your DB credentials** in `frontend/db_connect.php`
   (`$DB_USER`, `$DB_PASS`).
4. **Serve the frontend** with PHP's built-in server from inside `frontend/`:
   ```
   php -S localhost:8000
   ```
   Then open `http://localhost:8000/index.php`.
   (Or drop the `frontend/` folder into XAMPP's `htdocs/`.)
5. Because `03_dml.sql` inserts a **plaintext** admin password (`admin123`)
   for demo data, but `login.php` checks a `password_hash()`-hashed value,
   either:
   - register a **new** account through `register.php` (it hashes correctly), or
   - update the seeded admin row's password to a real hash, e.g. run once in PHP:
     `echo password_hash('admin123', PASSWORD_DEFAULT);` and `UPDATE Users SET Password='<hash>' WHERE Email='admin@bookstore.com';`

## What's demonstrated where (maps to the report structure)

| Report section              | File(s) |
|------------------------------|---------|
| 4. Database Design / ER      | `er_diagram/`, `schema/` (add your diagram images) |
| 5. Normalization (3NF)        | write-up in `documentation/` — schema is already in 3NF (no repeating groups, no partial/transitive dependencies) |
| 6. Database Schema            | `database/02_ddl.sql` |
| 7. DDL                        | `database/02_ddl.sql` |
| 8. DML                        | `database/03_dml.sql` |
| 9. DCL                        | `database/04_dcl.sql` |
| 10. Trigger                   | `database/05_trigger.sql` |
| 11. Procedure/Function        | `database/06_procedure.sql` |
| 12. Frontend GUI + validation | `frontend/*.php` (validation shown in `register.php`, `add_book.php`, `manage_books.php`) |

## Validation examples already built in (Screen 8 of the brief)

- Invalid email → `register.php`
- Empty field → `register.php`, `add_book.php`
- Invalid phone number (must be 10 digits) → `register.php`, DB-level CHECK constraint too
- Negative price → `add_book.php`, `manage_books.php`, DB-level CHECK constraint too
- Stock unavailable → `books.php` (order button hidden), `place_order.php` (server-side check)
- Wrong login credentials → `login.php`

## Next steps you'll still need to do yourself

- Take screenshots of each screen + query result for the report
- Draw the ER diagram (e.g. in EasyDraw / draw.io) and save it into `er_diagram/`
- Write up the 1NF → 2NF → 3NF explanation in your report using this schema as the example
- Compile everything into `documentation/DBMS_Mini_Project_Report.docx` following the 14-section structure
