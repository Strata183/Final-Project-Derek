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

### 3. Session Security
**Risk:** Session fixation/hijacking can allow account takeover.

**Mitigation:**
- Call `session_regenerate_id(true)` after successful login.
- Use secure cookie flags in php.ini or via `session_set_cookie_params`:
  - `HttpOnly`, `Secure` (if HTTPS), `SameSite=Lax/Strict`.
- Destroy session on logout.

**Status in provided code:** Regenerates ID on login; logout destroys session.

---

### 4. Cross-Site Scripting (XSS)
**Risk:** If post/comment content is rendered without escaping, a user can inject scripts that execute in other users’ browsers.

**Mitigation:**
- Escape output with `htmlspecialchars` for all user-supplied fields (titles, content, comments, usernames).
- If allowing rich text/HTML, use an allowlist sanitizer (not included here).

**Status in provided code:** Output is escaped using helper `e()`.

---


### 5. Authorization (AuthZ) / Access Control
**Risk:** Users performing actions they should not be allowed to (e.g., posting without login, admin-only actions).

**Mitigation:**
- Require login for create-post and comment actions.
- For admin-only features, check `Admin` flag server-side (never trust the client).

**Status in provided code:** `require_login()` enforced for creating posts and commenting.

---

### 6. Sensitive Configuration Exposure
**Risk:** Accidentally committing database credentials, or serving config files publicly.

**Mitigation:**
- Keep `config.php` out of version control (already in `.gitignore`).
- Ensure the web server does not serve PHP source as plaintext (PHP module enabled).
- Use environment variables where possible.

**Status:** `config.php` is excluded via `.gitignore` (good).

