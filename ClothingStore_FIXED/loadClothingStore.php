<?php
/**
 * loadDISCOVER AND RE-WIND.php
 * Drops all tables then recreates the full DISCOVER AND RE-WIND schema.
 * Uses MySQLi (improved MySQL).
 */

// SECURITY: Only accessible with the correct secret token
// Visit: loadDISCOVER AND RE-WIND.php?token=setup_DR2025 to run setup
if (!isset($_GET['token']) || $_GET['token'] !== 'setup_DR2025') {
    http_response_code(403);
    die('<h2>403 Forbidden</h2><p>This setup file is protected. Contact the administrator.</p>');
}

require_once 'DBConn.php';

$log = [];

// ── Helper: execute & log ─────────────────────────────────────────────────────
function run(mysqli $c, string $sql, string $label, array &$log): void {
    if ($c->query($sql)) {
        $log[] = ["ok", "$label — done."];
    } else {
        $log[] = ["err", "$label — FAILED: " . $c->error];
    }
}

// ── 1. Drop in FK-safe order ──────────────────────────────────────────────────
$conn->query("SET FOREIGN_KEY_CHECKS = 0");
foreach (['tblOrder','tblClothes','tblAdmin','tblUser'] as $t) {
    run($conn, "DROP TABLE IF EXISTS `$t`", "Drop $t", $log);
}
$conn->query("SET FOREIGN_KEY_CHECKS = 1");

// ── 2. Create tblUser ─────────────────────────────────────────────────────────
run($conn, "
CREATE TABLE IF NOT EXISTS tblUser (
    userID      INT AUTO_INCREMENT PRIMARY KEY,
    fullName    VARCHAR(100)  NOT NULL,
    email       VARCHAR(150)  NOT NULL UNIQUE,
    password    VARCHAR(255)  NOT NULL,
    province    VARCHAR(50)   DEFAULT NULL,
    isVerified  TINYINT(1)    NOT NULL DEFAULT 0,
    status      ENUM('active','inactive','pending') NOT NULL DEFAULT 'pending',
    createdAt   DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB CHARACTER SET utf8mb4;
", "Create tblUser", $log);

// ── 3. Create tblAdmin ────────────────────────────────────────────────────────
run($conn, "
CREATE TABLE IF NOT EXISTS tblAdmin (
    adminID     INT AUTO_INCREMENT PRIMARY KEY,
    fullName    VARCHAR(100)  NOT NULL,
    email       VARCHAR(150)  NOT NULL UNIQUE,
    password    VARCHAR(255)  NOT NULL,
    createdAt   DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB CHARACTER SET utf8mb4;
", "Create tblAdmin", $log);

// ── 4. Create tblClothes ──────────────────────────────────────────────────────
run($conn, "
CREATE TABLE IF NOT EXISTS tblClothes (
    clothesID   INT AUTO_INCREMENT PRIMARY KEY,
    title       VARCHAR(200)  NOT NULL,
    category    VARCHAR(80)   NOT NULL,
    brand       VARCHAR(100)  DEFAULT NULL,
    size        VARCHAR(20)   DEFAULT NULL,
    colour      VARCHAR(50)   DEFAULT NULL,
    condition_  ENUM('Mint','Good','Fair','Well-Loved') NOT NULL DEFAULT 'Good',
    sellPrice   DECIMAL(10,2) NOT NULL,
    retailPrice DECIMAL(10,2) DEFAULT NULL,
    imageFile   VARCHAR(255)  DEFAULT 'placeholder.jpg',
    status      ENUM('active','sold','inactive') NOT NULL DEFAULT 'active',
    createdAt   DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB CHARACTER SET utf8mb4;
", "Create tblClothes", $log);

// ── 5. Create tblOrder ────────────────────────────────────────────────────────
run($conn, "
CREATE TABLE IF NOT EXISTS tblOrder (
    orderID         INT AUTO_INCREMENT PRIMARY KEY,
    userID          INT           NOT NULL,
    clothesID       INT           NOT NULL,
    quantity        INT           NOT NULL DEFAULT 1,
    totalAmount     DECIMAL(10,2) NOT NULL,
    deliveryAddress TEXT          DEFAULT NULL,
    status          ENUM('pending','processing','shipped','delivered','cancelled')
                                  NOT NULL DEFAULT 'pending',
    createdAt       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_order_user    FOREIGN KEY (userID)    REFERENCES tblUser(userID)    ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_order_clothes FOREIGN KEY (clothesID) REFERENCES tblClothes(clothesID) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB CHARACTER SET utf8mb4;
", "Create tblOrder", $log);

// ── 6. Seed tblAdmin ──────────────────────────────────────────────────────────
run($conn, "
INSERT INTO tblAdmin (fullName, email, password) VALUES
('Super Admin',   'admin@clothingstore.co.za',   MD5('admin123')),
('Store Manager', 'manager@clothingstore.co.za', MD5('manager123')),
('Support Admin', 'support@clothingstore.co.za', MD5('support123')),
('Content Admin', 'content@clothingstore.co.za', MD5('content123')),
('Finance Admin', 'finance@clothingstore.co.za', MD5('finance123'));
", "Seed tblAdmin", $log);

// ── 7. Seed tblUser ───────────────────────────────────────────────────────────
run($conn, "
INSERT INTO tblUser (fullName, email, password, province, isVerified, status) VALUES
('John Doe',       'j.doe@abc.co.za',        MD5('password1'), 'Gauteng',       1, 'active'),
('Jane Smith',     'j.smith@xyz.co.za',      MD5('password2'), 'Western Cape',  1, 'active'),
('Thabo Nkosi',    't.nkosi@mail.co.za',     MD5('password3'), 'KwaZulu-Natal', 1, 'active'),
('Ayanda Maseko',  'a.maseko@web.co.za',     MD5('password4'), 'Gauteng',       0, 'pending'),
('Lerato Dlamini', 'l.dlamini@shop.co.za',   MD5('password5'), 'Limpopo',       1, 'active'),
('Sipho Mthembu',  's.mthembu@clothe.co.za', MD5('password6'), 'Mpumalanga',   0, 'pending'),
('Naledi Khumalo', 'n.khumalo@wear.co.za',   MD5('password7'), 'North West',   1, 'active'),
('David van Wyk',  'd.vanwyk@store.co.za',   MD5('password8'), 'Eastern Cape', 0, 'pending');
", "Seed tblUser (8 rows)", $log);

// ── 8. Seed tblClothes (30 rows) ──────────────────────────────────────────────
run($conn, "
INSERT INTO tblClothes (title, category, brand, size, colour, condition_, sellPrice, retailPrice, imageFile) VALUES
('Vintage Denim Jacket',     'Jackets',    'Levis',         'M',  'Blue',    'Good',       350.00, 1200.00, 'jacket1.jpg'),
('Classic White T-Shirt',    'Tops',       'Nike',          'L',  'White',   'Mint',       120.00,  350.00, 'tshirt1.jpg'),
('Slim Fit Chinos',          'Pants',      'H&M',           '32', 'Khaki',   'Good',       180.00,  450.00, 'chino1.jpg'),
('Floral Summer Dress',      'Dresses',    'Zara',          'S',  'Multi',   'Mint',       260.00,  800.00, 'dress1.jpg'),
('Adidas Track Jacket',      'Jackets',    'Adidas',        'XL', 'Black',   'Fair',       200.00,  700.00, 'jacket2.jpg'),
('High-Waist Jeans',         'Pants',      'Topshop',       '28', 'Dark Blue','Good',      290.00,  950.00, 'jeans1.jpg'),
('Knit Pullover Sweater',    'Tops',       'Woolworths',    'M',  'Cream',   'Good',       220.00,  600.00, 'sweater1.jpg'),
('Canvas Sneakers',          'Shoes',      'Converse',      '8',  'White',   'Fair',       150.00,  550.00, 'shoes1.jpg'),
('Leather Crossbody Bag',    'Accessories','Fossil',        'OS', 'Brown',   'Good',       380.00, 1100.00, 'bag1.jpg'),
('Printed Midi Skirt',       'Skirts',     'Cotton On',     'M',  'Orange',  'Mint',       175.00,  399.00, 'skirt1.jpg'),
('Bomber Jacket',            'Jackets',    'Superdry',      'L',  'Olive',   'Good',       420.00, 1500.00, 'jacket3.jpg'),
('Striped Polo Shirt',       'Tops',       'Lacoste',       'M',  'Navy',    'Good',       310.00,  900.00, 'polo1.jpg'),
('Cargo Shorts',             'Shorts',     'Quiksilver',    '32', 'Beige',   'Fair',       140.00,  400.00, 'shorts1.jpg'),
('Wrap Maxi Dress',          'Dresses',    'Zara',          'M',  'Red',     'Mint',       340.00,  999.00, 'dress2.jpg'),
('Running Shoes',            'Shoes',      'New Balance',   '9',  'Grey',    'Good',       450.00, 1600.00, 'shoes2.jpg'),
('Quilted Puffer Vest',      'Jackets',    'The North Face','L',  'Black',   'Good',       550.00, 1800.00, 'vest1.jpg'),
('Linen Wide-Leg Trousers',  'Pants',      'Witchery',      '10', 'Beige',   'Mint',       280.00,  750.00, 'pants1.jpg'),
('Graphic Band Tee',         'Tops',       'H&M',           'S',  'Black',   'Fair',        90.00,  200.00, 'tshirt2.jpg'),
('Ankle Boots',              'Shoes',      'Steve Madden',  '7',  'Tan',     'Good',       520.00, 1400.00, 'boots1.jpg'),
('Denim Overalls',           'Overalls',   'Levis',         'M',  'Blue',    'Good',       380.00, 1100.00, 'overalls1.jpg'),
('Silk Blouse',              'Tops',       'Zara',          'S',  'Ivory',   'Mint',       230.00,  700.00, 'blouse1.jpg'),
('Sports Leggings',          'Activewear', 'Nike',          'M',  'Black',   'Good',       200.00,  600.00, 'leggings1.jpg'),
('Corduroy Jacket',          'Jackets',    'Woolworths',    'L',  'Brown',   'Fair',       270.00,  800.00, 'jacket4.jpg'),
('Pleated Mini Skirt',       'Skirts',     'Cotton On',     'XS', 'Pink',    'Mint',       130.00,  350.00, 'skirt2.jpg'),
('Chelsea Boots',            'Shoes',      'Dr. Martens',   '8',  'Black',   'Good',       680.00, 2000.00, 'boots2.jpg'),
('Fleece Hoodie',            'Tops',       'Adidas',        'XL', 'Grey',    'Good',       250.00,  750.00, 'hoodie1.jpg'),
('Tailored Blazer',          'Jackets',    'Zara',          '38', 'Black',   'Mint',       490.00, 1500.00, 'blazer1.jpg'),
('Slip Dress',               'Dresses',    'Forever 21',    'M',  'Nude',    'Good',       180.00,  500.00, 'dress3.jpg'),
('Bucket Hat',               'Accessories','Nike',          'OS', 'White',   'Mint',        95.00,  299.00, 'hat1.jpg'),
('Platform Sandals',         'Shoes',      'Zara',          '7',  'Black',   'Fair',       210.00,  600.00, 'sandals1.jpg');
", "Seed tblClothes (30 rows)", $log);

$conn->close();

// ── Render log page ───────────────────────────────────────────────────────────
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>loadDISCOVER AND RE-WIND.php</title>
<style>
  body { font-family:'Segoe UI',sans-serif; background:#0f0f0f; color:#e5e5e5; padding:2rem; max-width:800px; margin:auto; }
  h1   { color:#c9a86c; border-bottom:1px solid #333; padding-bottom:.5rem; }
  .ok  { background:#1a2a1a; border-left:4px solid #4caf50; }
  .err { background:#2a1a1a; border-left:4px solid #f44336; }
  li   { padding:.6rem 1rem; margin:.35rem 0; border-radius:4px; list-style:none; }
  a    { color:#c9a86c; }
</style>
</head>
<body>
<h1>📦 loadDISCOVER AND RE-WIND.php — Execution Log</h1>
<ul>
<?php foreach ($log as [$type, $msg]): ?>
  <li class="<?= $type ?>"><?= htmlspecialchars($msg) ?></li>
<?php endforeach; ?>
</ul>
<p><a href="index.php">← Back to Home</a></p>
</body>
</html>
