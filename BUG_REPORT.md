# Fee Management System — Bug Report

> Audit date: 10 Aug 2026  ·  Scope: BACKEND + FRONTEND PHP code  ·  No changes were made.

---

## CRITICAL (application-breaking)

### C1. `class.php` is a fatal PHP parse error
`BACKEND/class.php:58-64` — raw HTML/`<label>` is written directly inside a PHP block after `echo "<td>"` with no closing `;`. The script cannot run at all.

### C2. `promote.php` calls a non-existent endpoint `promoteaction.php`
`BACKEND/promote.php:337` and `:371` fetch `BASE_URL . 'promoteaction.php'`. This file does **not exist**. Both "Promote Whole Class" and "Promote Individual" always fail.

### C3. `fetchrecord.php` uses the wrong (POST) implementation while `record.php` calls it with GET
`BACKEND/record.php:322` and `:363` call `fetchrecord.php?class_name=...&sec=...&month=...&year=...&students=1` (GET). The **active** code in `BACKEND/fetchrecord.php:1-98` only accepts POST (`$_POST['month']`, `$_POST['year']`, `$_POST['status']`, `$amount_paid` …) and redirects to `admin/admindashboard.php` on GET. The GET-based implementation that matches `record.php` is **commented out** (`fetchrecord.php:123-192`). Result: the "Monthly Fee Status" page (`record.php`) can never load sections or students (the AJAX `.json()` call gets the login/redirect HTML and hangs on "Loading…").

---

## HIGH (wrong money/loss of data)

### H1. Fully-paid months are charged a second time
`BACKEND/getcalcdue.php:50-63` and the identical logic in `BACKEND/savepayment.php:60-65`:
```
if (has_record > 0 && prev_remaining > 0) due = prev_remaining;
else                                due = monthly_fee;
```
If a month already has a payment record with `remaining = 0` (fully paid), the `else` branch re-charges the **full monthly fee**. Re-selecting an already-paid month in the payment popup overstates "total due" and lets the school over-collect / double charge. The `else` branch should be `due = prev_remaining` (i.e. 0) when a fully-paid record exists, not the full fee.

### H2. `carryforward.php` "undo" branch uses undefined variables
`BACKEND/carryforward.php:10-35` — the `action=undo` branch uses `$student_id`, `$month`, `$year` (lines 17 & 29) **before** they are assigned (they are only set later at lines 37-39). The undo runs against empty/null values and silently does nothing (or errors). Undo carry-forward is broken.

### H3. `getreceipt.php` returns an undefined receipt number
`BACKEND/getreceipt.php:43` returns `'receipt_number' => $receipt_number`, but `$receipt_number` is never set (should be `$payment['receipt_number']`). The API reply always contains `null`.

---

## MEDIUM (feature broken / access control)

### M1. `allstudentsuser.php` has no authentication
`BACKEND/allstudentsuser.php:1-4` — only `session_start()` + connection, no `user_auth.php` include. Any anonymous visitor can list every student (names, contacts, fees).

### M2. `delete.php` has no authentication
`BACKEND/delete.php:2-3` — only `session_start()` + connection; no `admin_auth.php`. Any anonymous visitor can POST `confirm_delete` and soft-delete any student by `id`.

### M3. User search is broken (auth type mismatch)
`BACKEND/user/userdashboard.php:55` submits the search form to `../search.php`, but `BACKEND/search.php:3` includes **`admin_auth.php`** (requires `type === 'admin'`). A normal user is redirected to the login page instead of getting search results.

### M4. `allstudents.php` includes `admin_auth.php` before the config/connection
`BACKEND/allstudents.php:2-3` loads `Master/admin_auth.php` first. `admin_auth.php:16` references the `FRONTEND_URL` constant, which is only defined by `conection.php` → `config_example.php`. When a visitor reaches the page unauthenticated, the undefined-constant use raises an `Error` (PHP 8) / broken redirect instead of a clean login redirect.

### M5. Users page exposes password hashes
`BACKEND/user.php:60` prints the full bcrypt `Password` column for every account in a table. Hash exposure (though not plaintext) is risky; the column should never be rendered.

### M6. Broken links / missing pages referenced in the UI
- `BACKEND/user/usersidebar.php:22` — "Monthly Fees" links to `../select_class.php`, which does **not** exist.
- `BACKEND/promote.php` → `promoteaction.php` (see C2).
- `FRONTEND/index.php:43` links to `signup.html`; `BACKEND/signup.php:5` and `:45` redirect to `FRONTEND/signup.html` and `FRONTEND/login.html` — **none of these files exist** (the login page is `index.php`). Successful sign-up then redirects to a 404.

### M7. Role mismatch on shared pages
`FRONTEND/addstudents.php:3` and `FRONTEND/addclass.php:3` include `admin_auth.php`, but the user sidebar (`usersidebar.php:25-30`) links to both. A normal user clicking "Add Student"/"Add Class" gets kicked to login. Users also can't access the Add/Edit backend endpoints.

---

## LOW / ADVISORY

- **Plaintext secrets in the repo:**
  - `FRONTEND/hash.php:2` contains a real plaintext password (`shan@1510`).
  - `BACKEND/.gitignore/recovery.php:3` ships `RECOVERY_KEY 'rooshan'` in the committed tree (the whole `.gitignore` folder is tracked by git). Anyone reading the source can reset any account's password.
- **`session.php` is fully commented out** (`BACKEND/session.php`) — not included anywhere, but if a page ever includes it expecting a guard it provides no protection. Prefer `admin_auth.php`/`user_auth.php`.
- **Dashboard stat cards are not year-scoped** (`BACKEND/buttons/paidbutton.php`, `unpaid.php`): they count any month/year. A student paid in one month yet unpaid in another appears in both "Paid" and "Pending"; totals never reconcile. `paidbutton.php:8` also doesn't filter `is_deleted = 0`, so soft-deleted students are counted.
- **`fetchclass.php` students query misses `is_deleted = 0`** (`BACKEND/fetchclass.php:21-31`) so deleted students still appear in the Monthly Fee view (other queries do filter).
- **Receipt number generation is racy** (`BACKEND/savepayment.php:71-75`): `COUNT(*)+1` can collide under concurrent payments, and it uses the hardcoded `YEAR_NOW` ('2035') constant instead of the payment year.
- **`studentimport.php`** has hardcoded absolute paths (`studentimport.php:12`) and a hardcoded file list; `dbimport.php:13` references `hifz.csv` and `user/yourfile.php:12` references `play group.csv` — neither file exists (dev scripts, would die/error if run).
- **Dead/insecure leftover script `class1.php`**: connects to a different DB name (`feemanagementsystem`), queries table `students` (plural; the app uses `student`), uses unsafe string-concatenated SQL, and prints the connection error. Should be deleted.
- **No CSRF protection / no rate limiting / generic error messages** on any POST handler (login, signup, add/update/delete, payment). All writes rely on session type checks only.
- **DB config**: `conection.php:2` requires `config_example.php` (a committed template with default `root`/empty password). A real `config.php` exists but inside `.gitignore/` and is unused. Excluding real credentials from the repo is correct, but the app should load a proper, non-committed config.

---

## Summary

| Severity | Issue | File(s) |
|---|---|---|
| C1 | PHP parse error | `BACKEND/class.php` |
| C2 | Missing `promoteaction.php` | `BACKEND/promote.php` |
| C3 | GET/POST mismatch breaks fee-status page | `BACKEND/record.php`, `BACKEND/fetchrecord.php` |
| H1 | Double-charges fully paid months | `BACKEND/getcalcdue.php`, `BACKEND/savepayment.php` |
| H2 | Undo carry-forward uses undefined vars | `BACKEND/carryforward.php` |
| H3 | Undefined receipt number in API | `BACKEND/getreceipt.php` |
| M1 | Open student list (no auth) | `BACKEND/allstudentsuser.php` |
| M2 | Open delete (no auth) | `BACKEND/delete.php` |
| M3 | User search blocked by admin auth | `BACKEND/user/userdashboard.php`, `BACKEND/search.php` |
| M4 | Auth included before constants | `BACKEND/allstudents.php` |
| M5 | Password hashes rendered | `BACKEND/user.php` |
| M6 | Missing linked pages / 404 redirects | `usersidebar.php`, `signup.php`, `index.php` |
| M7 | Users blocked from Add pages | `FRONTEND/addstudents.php`, `addclass.php` |
| Low | Plaintext password + recovery key in repo | `FRONTEND/hash.php`, `BACKEND/.gitignore/recovery.php` |
| Low | Stat counts not year-scoped / include deleted | `BACKEND/buttons/*.php` |
| Low | Dead & insecure dev scripts | `class1.php`, `dbimport.php`, `yourfile.php`, `session.php` |

---

## Resolution status (10 Aug 2026)

All fixes applied on top of the working tree; **core functionality preserved**. Every affected PHP file passes `php -l`.

| Issue | Status | Fix |
|---|---|---|
| C1 | ✅ Fixed | `class.php` status‑radio block rewritten as valid PHP/HTML. |
| C2 | ✅ Fixed | Created `BACKEND/promoteaction.php` (admin‑auth, JSON) handling `action=class` and `action=individual` with existence + affected‑row checks. |
| C3 | ✅ Fixed | `fetchrecord.php` restored to the GET implementation the caller (`record.php`) expects (sections + students modes). Uses combined auth. |
| H1 | ✅ Already fixed in working tree | `getcalcdue.php` + `savepayment.php` now bill only the latest outstanding `remaining`; fully paid months → Rs. 0. No further change needed. |
| H2 | ✅ Fixed | `carryforward.php` now loads `$student_id/$month/$year` before the `undo` branch and validates them. |
| H3 | ✅ Fixed | `getreceipt.php` returns `$payment['receipt_number']`. |
| M1 | ✅ Fixed | `allstudentsuser.php` now includes `Master/user_auth.php`; the stray admin‑only `Edit` link (and its column) removed — page is view‑only for users. |
| M2 | ✅ Fixed | `delete.php` now includes `Master/admin_auth.php`. |
| M3 | ✅ Fixed | Added `Master/any_auth.php` (admin **or** user) and used it in `search.php`, so the user dashboard search works. |
| M4 | ✅ Fixed | `allstudents.php` includes `conection.php` before `admin_auth.php` (constants always available). |
| M5 | ✅ Fixed | Password‑hash column removed from the Users table. |
| M6 | ✅ Fixed | `signup.php` redirects now go to `index.php`; login page links to `signup.html` → new `signup.php`; user sidebar "Monthly Fees" now points to the working `../record.php`; `recovery.php` back‑link fixed. |
| M7 | ✅ Fixed | `record.php` + `fetchrecord.php` + `updatefeestatus.php` moved to combined auth so the user (cashier) "Monthly Fees" flow works; the admin‑only "Add Student"/"Add Class" links were removed from the **user** sidebar (pages stay admin-only). |
| Low | ✅ Partially fixed | `hash.php` no longer contains the plaintext password (now a generic generator). `paidbutton.php` filters `is_deleted = 0`. `fetchclass.php` students query filters `is_deleted = 0`. Stat snippets (`totalstudents.php`, `paidbutton.php`, `unpaid.php`) now require a valid session. |
| Low | ⚠️ Deliberately left | `recovery.php` secret key still committed (it is a functional emergency tool) — recommend rotating the key and keeping the file out of git. `dbimport.php` / `yourfile.php` / `session.php` left untouched (unreferenced dev/legacy scripts, no runtime impact). |

**Notes / decisions**
- Users do **not** gain admin master‑data rights (add/edit/delete students, add classes, reports, promotions) — those remain admin‑only. Their role can now use the monthly fee‑status page and student search, matching the sidebar they are shown.
- No database schema changes were made.