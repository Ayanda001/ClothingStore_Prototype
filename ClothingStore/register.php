<?php
/**
 * register.php
 * New user registration — stores hashed password, status = 'pending'
 * Admin must verify before user can log in.
 */

session_start();
require_once 'DBConn.php';

$errors  = [];
$success = '';

// Sticky form values
$fullName = $email = $province = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName  = trim($_POST['fullName']  ?? '');
    $email     = trim($_POST['email']     ?? '');
    $password  = trim($_POST['password']  ?? '');
    $confirm   = trim($_POST['confirm']   ?? '');
    $province  = trim($_POST['province']  ?? '');

    // ── Validation ────────────────────────────────────────────────────────────
    if (empty($fullName))  $errors[] = "Full name is required.";
    if (empty($email))     $errors[] = "Email address is required.";
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format.";
    if (empty($password))  $errors[] = "Password is required.";
    elseif (strlen($password) < 8) $errors[] = "Password must be at least 8 characters.";
    if ($password !== $confirm) $errors[] = "Passwords do not match.";
    if (empty($province))  $errors[] = "Please select your province.";

    // ── Check duplicate email ─────────────────────────────────────────────────
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT userID FROM tblUser WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "An account with that email already exists.";
        }
        $stmt->close();
    }

    // ── Insert ────────────────────────────────────────────────────────────────
    if (empty($errors)) {
        $hashed = md5($password);   // MD5 hash as required by assignment
        $stmt = $conn->prepare(
            "INSERT INTO tblUser (fullName, email, password, province, isVerified, status)
             VALUES (?, ?, ?, ?, 0, 'pending')"
        );
        $stmt->bind_param("ssss", $fullName, $email, $hashed, $province);
        if ($stmt->execute()) {
            $success  = "Registration successful! Your account is pending admin verification. You will be able to login once approved.";
            $fullName = $email = $province = ''; // Clear form
        } else {
            $errors[] = "Registration failed. Please try again.";
        }
        $stmt->close();
    }
}

$conn->close();

$provinces = ['Eastern Cape','Free State','Gauteng','KwaZulu-Natal',
              'Limpopo','Mpumalanga','North West','Northern Cape','Western Cape'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register — ClothingStore</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
  :root {
    --bg:    #0c0c0c;
    --card:  #161616;
    --gold:  #c9a86c;
    --gold2: #e8c98a;
    --text:  #e5e5e5;
    --muted: #888;
    --err:   #e05252;
    --ok:    #5cb85c;
    --border:#2a2a2a;
    --radius:8px;
  }
  * { box-sizing: border-box; margin:0; padding:0; }
  body {
    font-family: 'DM Sans', sans-serif;
    background: var(--bg);
    color: var(--text);
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 2rem 1rem;
  }
  nav {
    width: 100%; max-width: 900px;
    display: flex; justify-content: space-between; align-items: center;
    padding: .75rem 0; border-bottom: 1px solid var(--border);
    margin-bottom: 2.5rem;
  }
  .logo { font-family:'Playfair Display',serif; color:var(--gold); font-size:1.4rem; text-decoration:none; }
  .nav-links a { color:var(--muted); text-decoration:none; margin-left:1.5rem; font-size:.9rem; transition:color .2s; }
  .nav-links a:hover { color:var(--gold); }

  .card {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 2.5rem 2.5rem;
    width: 100%; max-width: 480px;
  }
  h1 { font-family:'Playfair Display',serif; color:var(--gold); font-size:1.8rem; margin-bottom:.25rem; }
  .subtitle { color:var(--muted); font-size:.9rem; margin-bottom:2rem; }

  .alert { padding:.9rem 1rem; border-radius:var(--radius); margin-bottom:1.2rem; font-size:.9rem; }
  .alert-err { background:#2a1212; border-left:4px solid var(--err); color:#ffaaaa; }
  .alert-ok  { background:#122a12; border-left:4px solid var(--ok);  color:#aaffaa; }

  .form-group { margin-bottom:1.25rem; }
  label { display:block; font-size:.82rem; color:var(--muted); margin-bottom:.4rem; letter-spacing:.04em; text-transform:uppercase; }
  input, select {
    width:100%; padding:.65rem .9rem;
    background:#1f1f1f; border:1px solid var(--border);
    color:var(--text); border-radius:var(--radius);
    font-family:'DM Sans',sans-serif; font-size:.95rem;
    transition: border-color .2s;
  }
  input:focus, select:focus { outline:none; border-color:var(--gold); }
  select option { background:#1f1f1f; }

  .btn {
    width:100%; padding:.75rem;
    background: linear-gradient(135deg, var(--gold), var(--gold2));
    color:#111; font-weight:600; border:none; border-radius:var(--radius);
    cursor:pointer; font-size:1rem; letter-spacing:.03em;
    transition: opacity .2s;
  }
  .btn:hover { opacity:.9; }
  .form-footer { text-align:center; margin-top:1.5rem; font-size:.88rem; color:var(--muted); }
  .form-footer a { color:var(--gold); text-decoration:none; }
</style>
</head>
<body>
<nav>
  <a href="index.php" class="logo">ClothingStore</a>
  <div class="nav-links">
    <a href="index.php">Home</a>
    <a href="shop.php">Shop</a>
    <a href="login.php">Login</a>
  </div>
</nav>

<div class="card">
  <h1>Create Account</h1>
  <p class="subtitle">Join ClothingStore — pending admin verification</p>

  <?php if ($success): ?>
    <div class="alert alert-ok"><?= htmlspecialchars($success) ?></div>
  <?php endif; ?>

  <?php if ($errors): ?>
    <div class="alert alert-err">
      <?php foreach ($errors as $e): ?>
        <div>• <?= htmlspecialchars($e) ?></div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <form method="POST" action="register.php" novalidate>
    <div class="form-group">
      <label for="fullName">Full Name</label>
      <input type="text" id="fullName" name="fullName"
             value="<?= htmlspecialchars($fullName) ?>"
             placeholder="Your full name" required>
    </div>

    <div class="form-group">
      <label for="email">Email Address</label>
      <input type="email" id="email" name="email"
             value="<?= htmlspecialchars($email) ?>"
             placeholder="you@example.co.za" required>
    </div>

    <div class="form-group">
      <label for="password">Password (min 8 characters)</label>
      <input type="password" id="password" name="password" placeholder="••••••••" required>
    </div>

    <div class="form-group">
      <label for="confirm">Confirm Password</label>
      <input type="password" id="confirm" name="confirm" placeholder="••••••••" required>
    </div>

    <div class="form-group">
      <label for="province">Province</label>
      <select id="province" name="province" required>
        <option value="">Select province…</option>
        <?php foreach ($provinces as $p): ?>
          <option value="<?= $p ?>" <?= $province === $p ? 'selected' : '' ?>>
            <?= $p ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <button type="submit" class="btn">Create My Account</button>
  </form>

  <div class="form-footer">
    Already have an account? <a href="login.php">Login here</a>
  </div>
</div>
</body>
</html>
