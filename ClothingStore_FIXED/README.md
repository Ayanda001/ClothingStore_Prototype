# DISCOVER AND RE-WIND EVERYWHERE

## WEDE6021 POE Project

A local PHP / MySQL web app for browsing pre-loved clothing, managing a shopping cart, and placing orders with admin-verified users.

---

## Prerequisites

- WAMP Server installed and running
- VS Code installed
- A modern browser (Chrome, Edge, Firefox)

---

## Setup Instructions

### 1. Place the project folder in WAMP

1. Open **File Explorer**.
2. Go to `C:\wamp64\www\`.
   - If you have 32-bit WAMP, use `C:\wamp\www\`.
3. Copy the entire folder `ClothingStore_FIXED` into that directory.

Resulting path:

```text
C:\wamp64\www\ClothingStore_FIXED\
```

---

### 2. Start WAMP

1. Open the WAMP Server application.
2. Wait until the tray icon turns **GREEN**.
3. If it stays orange, right-click the tray icon and choose **Restart All Services**.

> WAMP must be green before the PHP site works.

---

### 3. Import the database

1. Open phpMyAdmin from the WAMP tray menu.
2. Login with:
   - Username: `root`
   - Password: *(leave blank)*
3. Click **Import**.
4. Choose `myClothingStore.sql` from the project folder.
5. Click **Go**.

This creates the `ClothingStore` database and the following tables:

- `tblUser`
- `tblAdmin`
- `tblClothes`
- `tblOrder`

It also inserts sample data with 30 product records, 5 admin users, and 8 customer accounts.

---

### 4. Open the project in VS Code

1. Launch **VS Code**.
2. Select **File → Open Folder**.
3. Open `C:\wamp64\www\ClothingStore_FIXED`.

---

### 5. Run the site

Open this URL in your browser:

```text
http://localhost/ClothingStore_FIXED/
```

If the page does not load, confirm WAMP is running and the folder is in the correct `www` directory.

---

## Login Credentials

### Customer Accounts

| Name | Email | Password | Status |
|------|-------|----------|--------|
| John Doe | j.doe@abc.co.za | password1 | Active |
| Jane Smith | j.smith@xyz.co.za | password2 | Active |
| Thabo Nkosi | t.nkosi@mail.co.za | password3 | Active |
| Lerato Dlamini | l.dlamini@shop.co.za | password5 | Active |
| Naledi Khumalo | n.khumalo@wear.co.za | password7 | Active |
| Ayanda Maseko | a.maseko@web.co.za | password4 | Pending |
| Sipho Mthembu | s.mthembu@clothe.co.za | password6 | Pending |
| David van Wyk | d.vanwyk@store.co.za | password8 | Pending |

### Admin Accounts

| Name | Email | Password |
|------|-------|----------|
| Super Admin | admin@clothingstore.co.za | admin123 |
| Store Manager | manager@clothingstore.co.za | manager123 |
| Support Admin | support@clothingstore.co.za | support123 |
| Content Admin | content@clothingstore.co.za | content123 |
| Finance Admin | finance@clothingstore.co.za | finance123 |

Admin login page:

```text
http://localhost/ClothingStore_FIXED/admin/login.php
```

---

## Pages

| Page | Purpose |
|------|---------|
| `/` | Home page |
| `/shop.php` | Browse products and add to cart |
| `/checkout.php` | Finalize order (login required) |
| `/login.php` | Customer login |
| `/register.php` | Customer registration |
| `/dashboard.php` | Account and order history |
| `/admin/login.php` | Admin login |
| `/admin/index.php` | Admin dashboard |

---

## Database Reset

To rebuild the database from scratch, open:

```text
http://localhost/ClothingStore_FIXED/loadClothingStore.php?token=setup_DR2025
```

> Warning: This deletes all existing data and restores the default seed values.

---

## Project Structure

```text
ClothingStore_FIXED/
├── admin/
│   ├── index.php
│   ├── login.php
│   └── logout.php
├── css/
├── images/
├── js/
├── DBConn.php
├── checkout.php
├── dashboard.php
├── index.php
├── loadClothingStore.php
├── login.php
├── logout.php
├── myClothingStore.sql
├── register.php
└── shop.php
```

---

## Troubleshooting

- **Blank page**: Ensure WAMP is green and both Apache/MySQL are running.
- **Database connection failed**: Open `DBConn.php` and confirm `DB_HOST`, `DB_USER`, `DB_PASS`, and `DB_NAME` are correct.
- **Page not found**: Verify the project folder is located at `C:\wamp64\www\ClothingStore_FIXED`.
- **phpMyAdmin will not open**: Restart WAMP services from the tray icon.
- **Login issues**: Ensure the database import completed successfully.
