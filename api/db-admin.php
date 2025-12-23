<?php
/**
 * Sinematix - Database Admin Panel
 * Simple phpMyAdmin alternative for basic database management
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../models/Database.php';

$db = Database::getInstance();
$error = null;
$success = null;
$results = null;
$columns = [];

// Get all tables
$tables = Database::fetchAll("SHOW TABLES");
$tableList = array_map(fn($t) => array_values($t)[0], $tables);

$currentTable = $_GET['table'] ?? null;
$action = $_GET['action'] ?? 'browse';

// Handle SQL query
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sql'])) {
    $sql = trim($_POST['sql']);
    if (!empty($sql)) {
        try {
            if (stripos($sql, 'SELECT') === 0 || stripos($sql, 'SHOW') === 0 || stripos($sql, 'DESCRIBE') === 0) {
                $results = Database::fetchAll($sql);
                if (!empty($results)) {
                    $columns = array_keys($results[0]);
                }
                $success = count($results) . ' kayıt bulundu.';
            } else {
                $stmt = Database::query($sql);
                $success = $stmt->rowCount() . ' satır etkilendi.';
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id']) && $currentTable) {
    try {
        Database::query("DELETE FROM `$currentTable` WHERE id = ?", [$_POST['delete_id']]);
        $success = 'Kayıt silindi.';
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Browse table
if ($currentTable && $action === 'browse') {
    try {
        $results = Database::fetchAll("SELECT * FROM `$currentTable` ORDER BY id DESC LIMIT 100");
        if (!empty($results)) {
            $columns = array_keys($results[0]);
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Get table structure
$structure = [];
if ($currentTable && $action === 'structure') {
    try {
        $structure = Database::fetchAll("DESCRIBE `$currentTable`");
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

$pageTitle = 'Veritabanı Yönetimi';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🗄️ Sinematix - DB Admin</title>
    <style>
        :root {
            --bg: #0a0a0f;
            --bg2: #13131a;
            --bg3: #1a1a24;
            --text: #e5e5e5;
            --muted: #888;
            --primary: #e50914;
            --success: #22c55e;
            --error: #ef4444;
            --border: #2a2a3a;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); min-height: 100vh; }
        .layout { display: flex; }
        .sidebar { width: 220px; background: var(--bg2); border-right: 1px solid var(--border); height: 100vh; position: fixed; overflow-y: auto; }
        .sidebar-header { padding: 20px; border-bottom: 1px solid var(--border); }
        .sidebar-header h2 { font-size: 1rem; color: var(--primary); }
        .table-list { padding: 10px; }
        .table-link { display: block; padding: 10px 15px; color: var(--text); text-decoration: none; border-radius: 8px; margin-bottom: 5px; transition: all 0.2s; }
        .table-link:hover { background: var(--bg3); }
        .table-link.active { background: var(--primary); }
        .main { margin-left: 220px; padding: 20px; flex: 1; min-height: 100vh; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid var(--border); }
        .tabs { display: flex; gap: 10px; margin-bottom: 20px; }
        .tab { padding: 10px 20px; background: var(--bg2); border: 1px solid var(--border); border-radius: 8px; color: var(--text); text-decoration: none; }
        .tab:hover, .tab.active { background: var(--primary); border-color: var(--primary); }
        .card { background: var(--bg2); border: 1px solid var(--border); border-radius: 12px; padding: 20px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid var(--border); }
        th { background: var(--bg3); font-weight: 600; }
        tr:hover { background: var(--bg3); }
        .btn { padding: 8px 16px; border: none; border-radius: 6px; cursor: pointer; font-size: 0.85rem; }
        .btn-primary { background: var(--primary); color: white; }
        .btn-danger { background: var(--error); color: white; }
        .btn-sm { padding: 4px 10px; font-size: 0.75rem; }
        textarea { width: 100%; min-height: 120px; padding: 15px; background: var(--bg3); border: 1px solid var(--border); border-radius: 8px; color: var(--text); font-family: monospace; resize: vertical; }
        .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .alert-success { background: rgba(34, 197, 94, 0.2); border: 1px solid var(--success); color: var(--success); }
        .alert-error { background: rgba(239, 68, 68, 0.2); border: 1px solid var(--error); color: var(--error); }
        .back-link { color: var(--primary); text-decoration: none; display: inline-block; margin-bottom: 20px; }
        .truncate { max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    </style>
</head>
<body>
    <div class="layout">
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>🗄️ SINEMATIX DB</h2>
            </div>
            <div class="table-list">
                <a href="db-admin.php" class="table-link <?= !$currentTable ? 'active' : '' ?>">📊 SQL Sorgusu</a>
                <hr style="border-color: var(--border); margin: 10px 0;">
                <?php foreach ($tableList as $table): ?>
                <a href="?table=<?= urlencode($table) ?>" 
                   class="table-link <?= $currentTable === $table ? 'active' : '' ?>">
                    📁 <?= htmlspecialchars($table) ?>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="main">
            <div class="header">
                <h1><?= $currentTable ? htmlspecialchars($currentTable) : 'SQL Sorgusu Çalıştır' ?></h1>
                <a href="../index.php" class="btn btn-primary">🏠 Ana Sayfa</a>
            </div>
            
            <?php if ($error): ?>
            <div class="alert alert-error">❌ <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
            <div class="alert alert-success">✅ <?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            
            <?php if ($currentTable): ?>
            <div class="tabs">
                <a href="?table=<?= urlencode($currentTable) ?>&action=browse" class="tab <?= $action === 'browse' ? 'active' : '' ?>">📋 Veriler</a>
                <a href="?table=<?= urlencode($currentTable) ?>&action=structure" class="tab <?= $action === 'structure' ? 'active' : '' ?>">🔧 Yapı</a>
            </div>
            <?php endif; ?>
            
            <?php if (!$currentTable): ?>
            <!-- SQL Query -->
            <div class="card">
                <h3 style="margin-bottom: 15px;">SQL Sorgusu Çalıştır</h3>
                <form method="POST">
                    <textarea name="sql" placeholder="SELECT * FROM movies LIMIT 10;"><?= htmlspecialchars($_POST['sql'] ?? '') ?></textarea>
                    <button type="submit" class="btn btn-primary" style="margin-top: 15px;">▶️ Çalıştır</button>
                </form>
            </div>
            <?php endif; ?>
            
            <?php if ($action === 'structure' && !empty($structure)): ?>
            <!-- Table Structure -->
            <div class="card">
                <h3 style="margin-bottom: 15px;">Tablo Yapısı</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Sütun</th>
                            <th>Tür</th>
                            <th>Null</th>
                            <th>Key</th>
                            <th>Varsayılan</th>
                            <th>Extra</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($structure as $col): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($col['Field']) ?></strong></td>
                            <td><?= htmlspecialchars($col['Type']) ?></td>
                            <td><?= $col['Null'] ?></td>
                            <td><?= $col['Key'] ?></td>
                            <td><?= $col['Default'] ?? '-' ?></td>
                            <td><?= $col['Extra'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($results)): ?>
            <!-- Data Table -->
            <div class="card" style="overflow-x: auto;">
                <h3 style="margin-bottom: 15px;">
                    <?= $currentTable ? 'Veriler' : 'Sonuçlar' ?> 
                    <span style="color: var(--muted); font-weight: normal;">(<?= count($results) ?> kayıt)</span>
                </h3>
                <table>
                    <thead>
                        <tr>
                            <?php foreach ($columns as $col): ?>
                            <th><?= htmlspecialchars($col) ?></th>
                            <?php endforeach; ?>
                            <?php if ($currentTable && in_array('id', $columns)): ?>
                            <th>İşlem</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $row): ?>
                        <tr>
                            <?php foreach ($columns as $col): ?>
                            <td class="truncate" title="<?= htmlspecialchars($row[$col] ?? '') ?>">
                                <?= htmlspecialchars(substr($row[$col] ?? '', 0, 100)) ?>
                            </td>
                            <?php endforeach; ?>
                            <?php if ($currentTable && in_array('id', $columns)): ?>
                            <td>
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Bu kaydı silmek istediğinizden emin misiniz?');">
                                    <input type="hidden" name="delete_id" value="<?= $row['id'] ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">🗑️</button>
                                </form>
                            </td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php elseif ($currentTable && $action === 'browse'): ?>
            <div class="card" style="text-align: center; padding: 40px;">
                <p style="color: var(--muted);">Bu tabloda veri yok.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
