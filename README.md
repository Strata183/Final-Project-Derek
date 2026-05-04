# SQL Scripts


CREATE TABLE Users (
    UserID INT AUTO_INCREMENT,
    Username VARCHAR(50) NOT NULL,
    Password VARCHAR(255),
    Email VARCHAR(100) NOT NULL,
    Admin BOOLEAN DEFAULT FALSE,
    PRIMARY KEY (UserID)
);


CREATE TABLE Categories (
    CategoryID INT AUTO_INCREMENT PRIMARY KEY,
    CategoryName VARCHAR(100) NOT NULL
);


CREATE TABLE Posts (
    PostID INT AUTO_INCREMENT PRIMARY KEY,
    UserID INT,
    CategoryID INT,
    Title VARCHAR(255) NOT NULL,
    Content TEXT NOT NULL,
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (UserID) REFERENCES Users(UserID),
    FOREIGN KEY (CategoryID) REFERENCES Categories(CategoryID)
);

CREATE TABLE Tags (
    TagID INT AUTO_INCREMENT PRIMARY KEY,
    TagName VARCHAR(50) NOT NULL UNIQUE
);


CREATE TABLE Post_Tags (
    PostID INT,
    TagID INT,
    PRIMARY KEY (PostID, TagID),
    FOREIGN KEY (PostID) REFERENCES Posts(PostID) ON DELETE CASCADE,
    FOREIGN KEY (TagID) REFERENCES Tags(TagID) ON DELETE CASCADE
);

CREATE TABLE Comments (
    CommentID INT AUTO_INCREMENT PRIMARY KEY,
    PostID INT,
    UserID INT,
    Comment TEXT NOT NULL,
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (PostID) REFERENCES Posts(PostID) ON DELETE CASCADE,
    FOREIGN KEY (UserID) REFERENCES Users(UserID)
);



CREATE TABLE RawData (
    UserID INT,
    Username VARCHAR(50),
    Password VARCHAR(255),
    Email VARCHAR(100),
    Admin BOOLEAN,
    PostID INT,
    Title VARCHAR(255),
    Content TEXT,
    CategoryID INT,
    CategoryName VARCHAR(100),
    CommentID INT,
    Comment TEXT,
    TagID INT,
    TagName VARCHAR(50)
);

INSERT INTO Users (UserID, Username, Password, Email, Admin)
SELECT 
    UserID,
    MAX(Username),
    MAX(Password),
    MAX(Email),
    MAX(Admin)
FROM RawData
WHERE UserID IS NOT NULL
GROUP BY UserID;

INSERT INTO Categories (CategoryID, CategoryName)
SELECT 
    CategoryID,
    MAX(CategoryName)
FROM RawData
WHERE CategoryID IS NOT NULL
GROUP BY CategoryID;

INSERT INTO Tags (TagID, TagName)
SELECT 
    TagID,
    MAX(TagName)
FROM RawData
WHERE TagID IS NOT NULL
GROUP BY TagID;

INSERT INTO Posts (PostID, UserID, CategoryID, Title, Content)
SELECT 
    PostID,
    MAX(UserID),
    MAX(CategoryID),
    MAX(Title),
    MAX(Content)
FROM RawData
WHERE PostID IS NOT NULL
GROUP BY PostID;

INSERT INTO Comments (CommentID, PostID, UserID, Comment)
SELECT 
    CommentID,
    MAX(PostID),
    MAX(UserID),
    MAX(Comment)
FROM RawData
WHERE CommentID IS NOT NULL
GROUP BY CommentID;

INSERT INTO Post_Tags (PostID, TagID)
SELECT DISTINCT PostID, TagID
FROM RawData
WHERE TagID IS NOT NULL AND PostID IS NOT NULL;

SELECT 'Users' as table_name, COUNT(*) as row_count FROM Users
UNION ALL
SELECT 'Posts', COUNT(*) FROM Posts
UNION ALL
SELECT 'RawData', COUNT(*) FROM RawData;




# Security Report



### 1. SQL Injection
**Risk:** If SQL queries concatenate untrusted user input (e.g., login fields, search terms, PostID), an attacker can alter query logic and access/modify data.

**Where it occurs:** Login, registration checks, search, viewing posts by ID, comment submission, creating posts.

**Mitigation:**
- Use prepared statements with bound parameters (`mysqli->prepare`, `bind_param`) for all queries.
- Enforce strict input types for numeric IDs (`(int)$_GET['PostID']`).
- Use least-privilege DB accounts (no GRANT/ALTER/DROP for the web user).

**Status in provided code:** Implemented via prepared statements across routes.

---

### 2. Password Storage & Authentication
**Risk:** Storing plaintext passwords or weak hashes enables credential theft and account takeover if the database is leaked.

**Mitigation:**
- Store only password hashes using `password_hash()` (bcrypt/argon2 depending on PHP version).
- Verify with `password_verify()`.
- Enforce minimum password length and optionally rate-limit login attempts.

**Status in provided code:** Uses `password_hash/password_verify`; minimum length enforced.

---


### 3. Cross-Site Scripting (XSS)
**Risk:** If post/comment content is rendered without escaping, a user can inject scripts that execute in other users’ browsers.

**Mitigation:**
- Escape output with `htmlspecialchars` for all user-supplied fields (titles, content, comments, usernames).
- If allowing rich text/HTML, use an allowlist sanitizer (not included here).

**Status in provided code:** Output is escaped using helper `e()`.

---


### 4. Authorization 
**Risk:** Users performing actions they should not be allowed to (e.g., posting without login, admin-only actions).

**Mitigation:**
- Require login for create-post and comment actions.

**Status in provided code:** `require_login()` enforced for creating posts and commenting.

---

### 5. Sensitive Information
**Risk:** Accidentally committing database credentials, or serving config files publicly.

**Mitigation:**
- Keep `config.php` out of version control (already in `.gitignore`).
- Ensure the web server does not serve PHP source as plaintext (PHP module enabled).
- Use environment variables where possible.

**Status:** `config.php` is excluded via `.gitignore`.

