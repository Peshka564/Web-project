<?php
require_once __DIR__ . '/../autoload.php';
use db\repository\SessionRepository;
use db\repository\UserRepository;
use db\DBClient;
use services\AuthService;

session_start();

$db = new DBClient();
$sessions = new SessionRepository($db);
$users = new UserRepository($db);
$auth = new AuthService($sessions, $users);

$auth->guard();
?>

<?php
$sampleHistoryData = [
    ['id' => 1, 'title' => 'User Data Conversion', 'date' => '2023-10-15', 'description' => 'Converted user data from CSV to JSON format for analytics.'],
    ['id' => 2, 'title' => 'API Response Processing', 'date' => '2023-10-14', 'description' => 'Processed API response data and converted XML to JSON.'],
    ['id' => 3, 'title' => 'Customer Records', 'date' => '2023-10-12', 'description' => 'Transformed customer database records from SQL to JSON.'],
    ['id' => 4, 'title' => 'Product Catalog', 'date' => '2023-10-10', 'description' => 'Converted product catalog data from Excel to JSON format.'],
    ['id' => 5, 'title' => 'Log Analysis', 'date' => '2023-10-08', 'description' => 'Parsed server logs and converted to structured JSON for analysis.'],
    ['id' => 6, 'title' => 'Weather Data', 'date' => '2023-10-05', 'description' => 'Converted weather station data from XML to JSON format.'],
    ['id' => 7, 'title' => 'Financial Transactions', 'date' => '2023-10-03', 'description' => 'Transformed financial transaction records to JSON for reporting.'],
    ['id' => 8, 'title' => 'Inventory Update', 'date' => '2023-10-01', 'description' => 'Updated inventory records and converted to JSON for API consumption.'],
    ['id' => 9, 'title' => 'Employee Directory', 'date' => '2023-09-28', 'description' => 'Converted employee directory from CSV to JSON for web application.'],
    ['id' => 10, 'title' => 'Survey Results', 'date' => '2023-09-25', 'description' => 'Processed survey results and converted to JSON format for visualization.'],
    ['id' => 11, 'title' => 'Social Media Data', 'date' => '2023-09-20', 'description' => 'Transformed social media metrics from various formats to JSON.'],
    ['id' => 12, 'title' => 'E-commerce Orders', 'date' => '2023-09-18', 'description' => 'Converted e-commerce order history to JSON for analytics dashboard.'],
    ['id' => 13, 'title' => 'Sensor Readings', 'date' => '2023-09-15', 'description' => 'Processed IoT sensor readings and converted to JSON format.'],
    ['id' => 14, 'title' => 'Marketing Campaign', 'date' => '2023-09-12', 'description' => 'Transformed marketing campaign data from Excel to JSON.'],
    ['id' => 15, 'title' => 'Event Logs', 'date' => '2023-09-10', 'description' => 'Parsed application event logs and structured as JSON for analysis.'],
    ['id' => 16, 'title' => 'Real Estate Listings', 'date' => '2023-09-08', 'description' => 'Converted real estate listings from database to JSON for API.'],
    ['id' => 17, 'title' => 'Healthcare Records', 'date' => '2023-09-05', 'description' => 'Anonymized and converted patient records to JSON for research.'],
    ['id' => 18, 'title' => 'Academic Data', 'date' => '2023-09-01', 'description' => 'Transformed academic records from legacy system to JSON format.'],
    ['id' => 19, 'title' => 'Shipping Logs', 'date' => '2023-08-28', 'description' => 'Converted shipping and logistics data to JSON for tracking system.'],
    ['id' => 20, 'title' => 'News Articles', 'date' => '2023-08-25', 'description' => 'Processed news articles and metadata into JSON for content API.'],
    ['id' => 21, 'title' => 'Music Catalog', 'date' => '2023-08-20', 'description' => 'Converted music library catalog from XML to JSON format.'],
    ['id' => 22, 'title' => 'Sports Statistics', 'date' => '2023-08-18', 'description' => 'Transformed sports statistics data for JSON API consumption.']
];

$currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$itemsPerPage = 10;
$currentSort = isset($_GET['sort']) ? $_GET['sort'] : 'date-desc';
$currentFilter = isset($_GET['search']) ? strtolower(trim($_GET['search'])) : '';

function formatDate($dateString) {
    $date = new DateTime($dateString);
    return $date->format('F j, Y');
}

$filteredData = [];
foreach ($sampleHistoryData as $item) {
    if (empty($currentFilter) || 
        strpos(strtolower($item['title']), $currentFilter) !== false || 
        strpos(strtolower($item['description']), $currentFilter) !== false) {
        $filteredData[] = $item;
    }
}

usort($filteredData, function($a, $b) use ($currentSort) {
    switch($currentSort) {
        case 'date-asc':
            return strtotime($a['date']) - strtotime($b['date']);
        case 'date-desc':
            return strtotime($b['date']) - strtotime($a['date']);
        case 'title-asc':
            return strcmp($a['title'], $b['title']);
        case 'title-desc':
            return strcmp($b['title'], $a['title']);
        default:
            return 0;
    }
});

$totalItems = count($filteredData);
$totalPages = ceil($totalItems / $itemsPerPage);
$startIndex = ($currentPage - 1) * $itemsPerPage;
$endIndex = min($startIndex + $itemsPerPage, $totalItems);
$itemsToShow = array_slice($filteredData, $startIndex, $itemsPerPage);

function buildQueryString($params) {
    $queryParts = [];
    foreach ($params as $key => $value) {
        if (!empty($value) && $key !== 'page') {
            $queryParts[] = $key . '=' . urlencode($value);
        }
    }
    return empty($queryParts) ? '' : '?' . implode('&', $queryParts);
}

$baseQuery = buildQueryString(['sort' => $currentSort, 'search' => $currentFilter]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JSONConverter - History</title>
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body class="flex-body">
    <nav class="sidebar">
        <div class="avatar-placeholder"><img src="img/avatar.png" class="avatar"></div> 
        <a href="converter.php" class="nav-item">Converter</a>
        <a href="login.php" class="nav-item">Logout</a>
    </nav>

    <main class="main-content">
        <header class="top-bar">
            <h1>History</h1>
        </header>

        <div class="history-controls">
            <div class="sort-controls">
                <form method="GET" class="sort-form">
                    <p>Sort by: </p>
                    <select name="sort" onchange="this.form.submit()">
                        <option value="date-desc" <?php echo $currentSort == 'date-desc' ? 'selected' : ''; ?>>Newest first</option>
                        <option value="date-asc" <?php echo $currentSort == 'date-asc' ? 'selected' : ''; ?>>Oldest first</option>
                        <option value="title-asc" <?php echo $currentSort == 'title-asc' ? 'selected' : ''; ?>>Title A–Z</option>
                        <option value="title-desc" <?php echo $currentSort == 'title-desc' ? 'selected' : ''; ?>>Title Z–A</option>
                    </select>
                    <?php if (!empty($currentFilter)): ?>
                        <input type="hidden" name="search" value="<?php echo htmlspecialchars($currentFilter); ?>">
                    <?php endif; ?>
                </form>
            </div>
        
            <div class="search-controls">
                <form method="GET" class="search-form">
                    <p>Search: </p>
                    <input type="text" name="search" placeholder="Enter title to search..." 
                           value="<?php echo htmlspecialchars($currentFilter); ?>">
                    <?php if (!empty($currentSort) && $currentSort != 'date-desc'): ?>
                        <input type="hidden" name="sort" value="<?php echo htmlspecialchars($currentSort); ?>">
                    <?php endif; ?>
                    <input type="submit" value="Search">
                </form>
            </div>
        </div>

        <section class="history-section" id="historyContainer">
            <?php if (empty($itemsToShow)): ?>
                <div class="no-results">No history items found. Try adjusting your search.</div>
            <?php else: ?>
                <?php foreach ($itemsToShow as $item): ?>
                    <article class="history-item">
                        <header class="history-item-header">
                            <a href="history.php" class="history-item-name"><p><?php echo htmlspecialchars($item['title']); ?></p></a>
                            <time class="history-item-date"><?php echo formatDate($item['date']); ?></time>
                        </header>
                        <p class="history-item-description"><?php echo htmlspecialchars($item['description']); ?></p>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>

        <?php if ($totalPages > 1): ?>
            <div class="pagination" id="paginationControls">
                
                <?php if ($currentPage > 1): ?>
                    <a href="history.php<?php echo $baseQuery; ?>">&laquo; First</a>
                <?php else: ?>
                    <button disabled>&laquo; First</button>
                <?php endif; ?>
                
                <?php if ($currentPage > 1): ?>
                    <a href="history.php<?php echo $baseQuery . ($baseQuery ? '&' : '?'); ?>page=<?php echo $currentPage - 1; ?>">Previous</a>
                <?php else: ?>
                    <button disabled>Previous</button>
                <?php endif; ?>
            
                <span class="page-info">Page <?php echo $currentPage; ?> of <?php echo $totalPages; ?></span>
                
                <?php if ($currentPage < $totalPages): ?>
                    <a href="history.php<?php echo $baseQuery . ($baseQuery ? '&' : '?'); ?>page=<?php echo $currentPage + 1; ?>">Next</a>
                <?php else: ?>
                    <button disabled>Next</button>
                <?php endif; ?>
            
                <?php if ($currentPage < $totalPages): ?>
                    <a href="history.php<?php echo $baseQuery . ($baseQuery ? '&' : '?'); ?>page=<?php echo $totalPages; ?>">Last &raquo;</a>
                <?php else: ?>
                    <button disabled>Last &raquo;</button>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
    </main>
</body>
</html>