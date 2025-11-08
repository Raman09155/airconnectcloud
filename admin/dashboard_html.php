<!DOCTYPE html>
<html>
<head>
    <title>Contact Form Analytics Dashboard</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
        .header { background: #007cba; color: white; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin: 20px 0; }
        .stat-card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .stat-number { font-size: 2em; font-weight: bold; color: #007cba; }
        .filters { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .filters input, .filters button { padding: 8px; margin: 5px; border: 1px solid #ddd; border-radius: 4px; }
        .filters button { background: #007cba; color: white; cursor: pointer; }
        .table-container { background: white; border-radius: 8px; overflow: hidden; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f8f9fa; font-weight: bold; }
        .export-btn { background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; margin: 10px 0; }
        .logout-btn { background: #dc3545; color: white; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer; float: right; }
    </style>
</head>
<body>
    <div class="header">
        <div class="container">
            <h1>Contact Form Analytics Dashboard</h1>
            <form method="POST" action="logout.php" style="display: inline;">
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </div>
    </div>

    <div class="container">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?= $totalCount ?></div>
                <div>Total Submissions</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= count(array_unique(array_column($submissions, 'email'))) ?></div>
                <div>Unique Users</div>
            </div>
        </div>

        <div class="filters">
            <form method="GET">
                <input type="date" name="date_from" value="<?= htmlspecialchars($dateFrom) ?>" placeholder="From Date">
                <input type="date" name="date_to" value="<?= htmlspecialchars($dateTo) ?>" placeholder="To Date">
                <input type="text" name="search" value="<?= htmlspecialchars($searchTerm) ?>" placeholder="Search name, email, company...">
                <button type="submit">Filter</button>
                <a href="dashboard.php"><button type="button">Clear</button></a>
                <button type="button" class="export-btn" onclick="exportToCSV()">Export CSV</button>
            </form>
        </div>

        <div class="table-container">
            <table id="submissionsTable">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Company</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($submissions as $submission): ?>
                    <tr>
                        <td><?php 
                            date_default_timezone_set('Asia/Kolkata');
                            $time = new DateTime($submission['submitted_at']);
                            $time->setTimezone(new DateTimeZone('Asia/Kolkata'));
                            echo $time->format('d-m-Y h:i A');
                        ?></td>
                        <td><?= htmlspecialchars($submission['name']) ?></td>
                        <td><?= htmlspecialchars($submission['email']) ?></td>
                        <td><?= htmlspecialchars($submission['phone']) ?></td>
                        <td><?= htmlspecialchars($submission['company']) ?></td>
                        <td><?= htmlspecialchars(substr($submission['description'], 0, 100)) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function exportToCSV() {
            const table = document.getElementById('submissionsTable');
            let csv = [];
            
            const headers = Array.from(table.querySelectorAll('th')).map(th => th.textContent);
            csv.push(headers.join(','));
            
            const rows = Array.from(table.querySelectorAll('tbody tr'));
            rows.forEach(row => {
                const cells = Array.from(row.querySelectorAll('td')).map(td => `"${td.textContent.replace(/"/g, '""')}"`);
                csv.push(cells.join(','));
            });
            
            const csvContent = csv.join('\n');
            const blob = new Blob([csvContent], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'contact_submissions_' + new Date().toISOString().split('T')[0] + '.csv';
            a.click();
            window.URL.revokeObjectURL(url);
        }
    </script>
</body>
</html>