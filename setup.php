<?php
include 'includes/db.php';

// Create roles table
$queries = [
"CREATE TABLE IF NOT EXISTS roles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  role_name VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

"CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role_id INT NOT NULL DEFAULT 3,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

"CREATE TABLE IF NOT EXISTS login_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  ip_address VARCHAR(100),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

"CREATE TABLE IF NOT EXISTS password_resets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  token VARCHAR(128) NOT NULL,
  expires_at DATETIME NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
];

foreach($queries as $q){
    if ($conn->query($q) === TRUE) {
        echo "OK: Query executed.\n";
    } else {
        echo "Error: " . $conn->error . "\n";
    }
}

// Insert default roles if not exist
$roles = ['Admin','Editor','Viewer'];
$stmt = $conn->prepare('INSERT IGNORE INTO roles (role_name) VALUES (?)');
foreach($roles as $r){
    $stmt->bind_param('s',$r);
    $stmt->execute();
}

// Create default admin if not exists - password: admin123 (change after first login)
$admin_email = 'admin@example.com';
$admin_name = 'Administrator';
$check = $conn->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
$check->bind_param('s', $admin_email);
$check->execute();
$res = $check->get_result();
if (!$res->fetch_assoc()) {
    $hash = password_hash('admin123', PASSWORD_DEFAULT);
    // get admin role id
    $rid = 0;
    $rres = $conn->query("SELECT id FROM roles WHERE role_name='Admin' LIMIT 1");
    if ($rr = $rres->fetch_assoc()) $rid = $rr['id'];
    $ins = $conn->prepare('INSERT INTO users (username, email, password, role_id) VALUES (?, ?, ?, ?)');
    $ins->bind_param('sssi', $admin_name, $admin_email, $hash, $rid);
    if ($ins->execute()) echo "Default admin created (email: $admin_email, password: admin123). Please change password after login.\n";
    else echo "Failed to create admin: " . $ins->error . "\n";
} else {
    echo "Admin already exists.\n";
}

echo "Done.\n";
?>