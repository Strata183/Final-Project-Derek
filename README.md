# Final Project (PHP + MySQL Blog App)

A simple blog-style web application built with **PHP**, **HTML/CSS**, and a **MySQL** database. Users can register/login, create posts, comment on posts, and organize posts using categories and tags. Admin support is included via an `Admin` flag on users.

---

## Features

- User accounts (register / login / logout)
- Password hashing with `password_hash()` + verification using `password_verify()`
- Create and view posts
- Comments on posts
- Categories and tags (many-to-many via `Post_Tags`)
- Basic authorization checks (login required for posting/commenting)
- Output escaping for XSS mitigation (example helper like `e()`)

---

## Database Schema 

The SQL schema is defined in: `index.sql`

Tables included:

- `Users` (includes `Admin` boolean flag)
- `Posts`
- `Comments`
- `Categories`
- `Tags`
- `Post_Tags` (join table for many-to-many tags on posts)
- `RawData` (staging/import table used to populate normalized tables)

Key relationships:

- `Posts.UserID → Users.UserID`
- `Posts.CategoryID → Categories.CategoryID`
- `Comments.PostID → Posts.PostID`
- `Comments.UserID → Users.UserID`
- `Post_Tags.PostID → Posts.PostID`
- `Post_Tags.TagID → Tags.TagID`

---

## Setup Instructions

### 1) Clone the repository
```bash
git clone https://github.com/Strata183/Final-Project-Derek.git
cd Final-Project-Derek
```

### 2) Create the database
1. Open your MySQL tool (phpMyAdmin, MySQL Workbench, etc.)
2. Create a database (example name: `final_project`)
3. Run the SQL script from `index.sql` to create tables

### 3) Configure database credentials
Create a `config.php` file (this should **NOT** be committed if it contains secrets).

Example `config.php`:
```php
<?php
$DB_HOST = "localhost";
$DB_USER = "root";
$DB_PASS = "";
$DB_NAME = "final_project";
```

### 4) Open Website

Open `http_url` from your row in a browser.

Example:

```text
http://146.190.144.31:8013/

---

## Usage

- Register a new user
- Log in
- Create a post (login required)
- View posts by category (if implemented)
- Add comments to posts (login required)
- Admin users can be represented via the `Users.Admin` field

---

## Security Notes 

Defined in the Security Report - Google Docs.pdf
