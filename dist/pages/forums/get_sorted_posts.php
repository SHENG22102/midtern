<?php
require __DIR__ . '/../parts/init.php';

$sort = isset($_GET['sort']) ? $_GET['sort'] : 'id';
$direction = isset($_GET['direction']) ? $_GET['direction'] : 'asc';
$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';

// 驗證並清理排序參數
$allowedColumns = ['id', 'title', 'user_id', 'user_name', 'likes_count', 'bookmark_count', 'is_pinned', 'created_at', 'updated_at', 'status'];
if (!in_array($sort, $allowedColumns)) {
    $sort = 'id';
}
$direction = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';

$where = ' WHERE 1 ';
if ($keyword) {
    $keyword_ = $pdo->quote("%{$keyword}%");
    $where .= " AND (posts.title LIKE $keyword_ OR users.user_name LIKE $keyword_ OR posts.status LIKE $keyword_) ";
}

// 修改 SQL 查詢以實現數字邏輯排序
$orderBy = $sort;
if (in_array($sort, ['id', 'likes_count', 'bookmark_count'])) {
    $orderBy = "CAST(posts.$sort AS UNSIGNED)";
} elseif ($sort === 'user_id') {
    $orderBy = "CAST(users.user_id AS UNSIGNED)";
} elseif ($sort === 'title') {
    $orderBy = "CAST(SUBSTRING_INDEX(posts.title, ' ', -1) AS UNSIGNED)";
}

$sql = "SELECT posts.*, users.user_id, users.user_name,
        CAST(SUBSTRING_INDEX(posts.title, ' ', -1) AS UNSIGNED) as title_number
        FROM posts
        JOIN users ON posts.user_id = users.user_id
        $where
        ORDER BY $orderBy $direction, posts.title $direction";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($rows);
