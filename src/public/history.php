<?php
require_once __DIR__ . '/../autoload.php';
use db\repository\HistoryRepository;
use db\repository\SessionRepository;
use db\repository\UserRepository;
use db\DBClient;
use PageModels\HistoryPageModel;
use services\AuthService;

session_start();

$db = new DBClient();
$history = new HistoryRepository($db);
$sessions = new SessionRepository($db);
$users = new UserRepository($db);
$auth = new AuthService($sessions, $users);

$auth->guard();

$model = new HistoryPageModel($history, $sessions);

$currentPage = $model->getCurrentPage();
$itemsPerPage = $model->getItemsPerPage();
$currentSort = $model->getCurrentSort();
$currentFilter = $model->getCurrentFilter();

function formatDate($dateString)
{
    $date = new DateTime($dateString);
    return $date->format('F j, Y');
}

$totalPages = $model->getTotalPages();
$itemsToShow = $model->getItemsToShow();

function buildQueryString($params)
{
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
                        <option value="date-desc" <?php echo $currentSort == 'date-desc' ? 'selected' : ''; ?>>Newest
                            first</option>
                        <option value="date-asc" <?php echo $currentSort == 'date-asc' ? 'selected' : ''; ?>>Oldest first
                        </option>
                        <option value="title-asc" <?php echo $currentSort == 'title-asc' ? 'selected' : ''; ?>>Title A–Z
                        </option>
                        <option value="title-desc" <?php echo $currentSort == 'title-desc' ? 'selected' : ''; ?>>Title Z–A
                        </option>
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
                            <a href="converter.php?id=<?php echo urlencode($item->id) ?>" class="history-item-name">
                                <p><?php echo htmlspecialchars($item->name); ?></p>
                            </a>
                            <time class="history-item-date"><?php echo formatDate($item->updated_at); ?></time>
                        </header>
                        <p class="history-item-description"><?php echo htmlspecialchars($item->description); ?></p>
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
                    <a
                        href="history.php<?php echo $baseQuery . ($baseQuery ? '&' : '?'); ?>page=<?php echo $currentPage - 1; ?>">Previous</a>
                <?php else: ?>
                    <button disabled>Previous</button>
                <?php endif; ?>

                <span class="page-info">Page <?php echo $currentPage; ?> of <?php echo $totalPages; ?></span>

                <?php if ($currentPage < $totalPages): ?>
                    <a
                        href="history.php<?php echo $baseQuery . ($baseQuery ? '&' : '?'); ?>page=<?php echo $currentPage + 1; ?>">Next</a>
                <?php else: ?>
                    <button disabled>Next</button>
                <?php endif; ?>

                <?php if ($currentPage < $totalPages): ?>
                    <a href="history.php<?php echo $baseQuery . ($baseQuery ? '&' : '?'); ?>page=<?php echo $totalPages; ?>">Last
                        &raquo;</a>
                <?php else: ?>
                    <button disabled>Last &raquo;</button>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    </main>
</body>

</html>