<?php
// Set India timezone
date_default_timezone_set('Asia/Kolkata');  

require '../config/database_sqlite.php';

// Simple authentication
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    if (isset($_POST['password']) && $_POST['password'] === 'Aircon@#$121') {
        $_SESSION['admin_logged_in'] = true;
    } else {
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Admin Login</title>
            <style>
                body { font-family: Arial; padding: 50px; background: #f5f5f5; }
                .login-box { max-width: 300px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; }
                input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 4px; }
                button { background: #007cba; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
            </style>
        </head>
        <body>
            <div class="login-box">
                <h2>Admin Login</h2>
                <form method="POST">
                    <input type="password" name="password" placeholder="Enter Password" required>
                    <button type="submit">Login</button>
                </form>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
}

$database = new Database();
$conn = $database->getConnection();

if (!$conn) {
    echo '<div style="padding: 20px; background: #f8d7da; color: #721c24; border-radius: 5px; margin: 20px;">Database connection failed. Please check XAMPP MySQL service.</div>';
    exit;
}

// Handle date filtering
$dateFrom = $_GET['date_from'] ?? '';
$dateTo = $_GET['date_to'] ?? '';
$searchTerm = $_GET['search'] ?? '';

$whereClause = "WHERE 1=1";
$params = [];

if ($dateFrom) {
    $whereClause .= " AND DATE(submitted_at) >= ?";
    $params[] = $dateFrom;
}
if ($dateTo) {
    $whereClause .= " AND DATE(submitted_at) <= ?";
    $params[] = $dateTo;
}
if ($searchTerm) {
    $whereClause .= " AND (name LIKE ? OR email LIKE ? OR company LIKE ?)";
    $params[] = "%$searchTerm%";
    $params[] = "%$searchTerm%";
    $params[] = "%$searchTerm%";
}

// Get submissions
$query = "SELECT * FROM form_submissions $whereClause ORDER BY submitted_at DESC";
$stmt = $conn->prepare($query);
$stmt->execute($params);
$submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalQuery = "SELECT COUNT(*) as total FROM form_submissions $whereClause";
$totalStmt = $conn->prepare($totalQuery);
$totalStmt->execute($params);
$totalCount = $totalStmt->fetch(PDO::FETCH_ASSOC)['total'];

include 'dashboard_html.php';
?>