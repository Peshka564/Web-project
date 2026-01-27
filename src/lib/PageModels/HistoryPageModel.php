<?php

namespace PageModels;

use db\models\History;
use db\repository\HistoryRepository;
use db\repository\SessionRepository;

class HistoryPageModel
{
    private HistoryRepository $history;
    private SessionRepository $sessions;
    private int $curr_user_id;

    private int $currentPage;
    private int $itemsPerPage;
    private string $currentSort;
    private string $currentFilter;
    /**
     * @var History[]
     */
    private array $data;
    private int $totalPages;
    /**
     * @var History[]
     */
    private array $itemsToShow;

    public function __construct(HistoryRepository $history, SessionRepository $sessions)
    {
        $this->history = $history;
        $this->sessions = $sessions;
        $this->curr_user_id = self::getCurrentUser();
        $this->currentPage = 1;
        $this->itemsPerPage = 10;
        $this->currentSort = "date-desc";
        $this->currentFilter = "";

        if (array_key_exists("page", $_GET)) {
            $this->currentPage = max(1, intval($_GET['page']));
        }
        if (array_key_exists("sort", $_GET)) {
            $this->currentSort = $_GET['sort'];
        }
        if (array_key_exists("search", $_GET)) {
            $this->currentFilter = strtolower(trim($_GET['search']));
        }

        $this->data = self::getFilteredAndSortedData();
        $totalItems = count($this->data);
        $this->totalPages = ceil($totalItems / $this->itemsPerPage);
        $startIndex = ($this->currentPage - 1) * $this->itemsPerPage;
        $this->itemsToShow = array_slice($this->data, $startIndex, $this->itemsPerPage);
    }

    private function getCurrentUser()
    {
        return $this->sessions->findByToken($_SESSION["auth_token"])->user_id;
    }

    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    public function getItemsPerPage(): int
    {
        return $this->itemsPerPage;
    }

    public function getCurrentSort(): string
    {
        return $this->currentSort;
    }

    public function getCurrentFilter(): string
    {
        return $this->currentFilter;
    }

    public function getTotalPages(): int {
        return $this->totalPages;
    }

    /**
     * @return History[]
     */
    public function getItemsToShow():array{
        return $this->itemsToShow;
    }

    private function getFilteredAndSortedData(): array
    {
        $data = $this->history->findByUserId($this->curr_user_id);

        /**
         * @var History[]
         */
        $filteredData = [];
        foreach ($data as $item) {
            if (
                empty($this->currentFilter) ||
                strpos(strtolower($item->name), $this->currentFilter) !== false ||
                strpos(strtolower($item->description), $this->currentFilter) !== false
            ) {
                $filteredData[] = $item;
            }
        }

        $currentSort = $this->currentSort;
        usort($filteredData, function (History $a, History $b) use ($currentSort) {
            switch ($currentSort) {
                case 'date-asc':
                    return strtotime($a->updated_at) - strtotime($b->updated_at);
                case 'date-desc':
                    return strtotime($b->updated_at) - strtotime($a->updated_at);
                case 'title-asc':
                    return strcmp($a->name, $b->name);
                case 'title-desc':
                    return strcmp($b->name, $a->name);
                default:
                    return 0;
            }
        });

        return $filteredData;
    }


}