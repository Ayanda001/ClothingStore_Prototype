# Discover & Re-Wind — ClothingStore
### WEDE6021 POE Project — WAMP + VS Code Setup Guide

---

## What You Need

- **WAMP Server** — already installed
- **VS Code** — already installed
- **Browser** — Chrome or Firefox recommended

---

## Step-by-Step Setup

---

### STEP 1 — Copy the project into WAMP

1. Open **File Explorer**
2. Navigate to: `C:\wamp64\www\`
   *(If you installed 32-bit WAMP it may be `C:\wamp\www\`)*
3. Copy the entire `ClothingStore_FIXED` folder into that `www` folder

Your path should look like:
```
C:\wamp64\www\ClothingStore_FIXED\
```

---

### STEP 2 — Start WAMP

1. Double-click the **WAMP Server** icon on your desktop or taskbar
2. Wait for the icon in the **system tray** (bottom-right corner) to turn **GREEN**
3. If it stays orange, right-click it → Restart All Services

> The icon MUST be green before anything will work.

---

### STEP 3 — Open phpMyAdmin

1. Left-click the green WAMP tray icon
2. Click **phpMyAdmin** — it opens in your browser
3. Login with:
   - Username: `root`
   - Password: *(leave blank — WAMP default has no password)*

---

### STEP 4 — Import the database

1. In phpMyAdmin, click **Import** in the top menu bar
2. Click **Choose File**
3. Navigate to `C:\wamp64\www\ClothingStore_FIXED\myClothingStore.sql` and select it
4. Scroll down and click **Go**

You will see a green success message. This creates:
- The `ClothingStore` database
- 4 tables: tblUser, tblAdmin, tblClothes, tblOrder
- All sample data (30 clothing items, 5 admin accounts, 8 users)

---

### STEP 5 — Open the project in VS Code

1. Open **VS Code**
2. Go to **File → Open Folder**
3. Navigate to `C:\wamp64\www\ClothingStore_FIXED` and click **Select Folder**

All your PHP files will appear in the Explorer panel on the left.

---

### STEP 6 — Run the site

Open your browser and go to:
```
http://localhost/ClothingStore_FIXED/
```

The home page should load. You are ready to go.

---

## Login Credentials

### Customer Accounts

| Name | Email | Password | Status |
|------|-------|----------|--------|
| John Doe | j.doe@abc.co.za | password1 | Active ✅ |
| Jane Smith | j.smith@xyz.co.za | password2 | Active ✅ |
| Thabo Nkosi | t.nkosi@mail.co.za | password3 | Active ✅ |
| Lerato Dlamini | l.dlamini@shop.co.za | password5 | Active ✅ |
| Naledi Khumalo | n.khumalo@wear.co.za | password7 | Active ✅ |
| Ayanda Maseko | a.maseko@web.co.za | password4 | Pending ⏳ (needs admin approval) |
| Sipho Mthembu | s.mthembu@clothe.co.za | password6 | Pending ⏳ |
| David van Wyk | d.vanwyk@store.co.za | password8 | Pending ⏳ |

---

### Admin Accounts

| Name | Email | Password |
|------|-------|----------|
| Super Admin | admin@clothingstore.co.za | admin123 |
| Store Manager | manager@clothingstore.co.za | manager123 |
| Support Admin | support@clothingstore.co.za | support123 |
| Content Admin | content@clothingstore.co.za | content123 |
| Finance Admin | finance@clothingstore.co.za | finance123 |

Admin panel: `http://localhost/ClothingStore_FIXED/admin/login.php`

---

## All Site Pages

| URL | What it does |
|-----|-------------|
| `http://localhost/ClothingStore_FIXED/` | Home page |
| `.../shop.php` | Browse clothes, add to cart |
| `.../checkout.php` | Place an order (login required) |
| `.../login.php` | Customer login |
| `.../register.php` | Register new account |
| `.../dashboard.php` | View account + order history |
| `.../admin/login.php` | Admin login |
| `.../admin/index.php` | Admin panel — verify/manage users |

---

## Database Reset Tool

To wipe and rebuild the database from scratch, visit:
```
http://localhost/ClothingStore_FIXED/loadClothingStore.php?token=setup_DR2025
```
> WARNING: This deletes ALL data and re-seeds from defaults.

---

## Folder Structure

```
ClothingStore_FIXED/
├── admin/
│   ├── index.php        Admin dashboard
│   ├── login.php        Admin login
│   └── logout.php
├── css/                 Add external stylesheets here
├── images/              Add product photos here (jpg/png)
│                        If an image fails to load, it will show the category emoji instead (🧥)
├── js/                  Add external scripts here
├── DBConn.php           Database connection settings
├── checkout.php         Order placement page
├── dashboard.php        Customer account & orders
├── index.php            Home page
├── loadClothingStore.php  DB reset (token-protected)
├── login.php            Customer login
├── logout.php
├── myClothingStore.sql  Database schema + seed data
├── register.php         New customer registration
└── shop.php             Shop + cart
---

## Video Demonstration

[Watch the video demonstration](https://youtu.be/DTmx_lF54tY)

---

## Troubleshooting

| Problem | Solution |
|---------|----------|
| White/blank page | WAMP icon must be green. Check Apache and MySQL are running. |
| "Connection failed" error | Open DBConn.php in VS Code — make sure DB_PASS is empty `''` |
| Page not found | Make sure folder is inside `C:\wamp64\www\` not your Desktop |
| phpMyAdmin won't open | Right-click WAMP tray icon → Restart All Services |
| Can't log in with test accounts | Make sure you imported myClothingStore.sql in Step 4 |
