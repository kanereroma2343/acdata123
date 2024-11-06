<?php
header("Content-Type: application/json");

// Database connection
$conn = mysqli_connect("localhost", "root", "", "excel_data");

if (!$conn) {
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$selected_province = isset($_GET['province']) ? mysqli_real_escape_string($conn, $_GET['province']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$records_per_page = 75;
$offset = ($page - 1) * $records_per_page;

$where_clause = [];

if (!empty($search)) {
    $where_clause[] = "(assessment_center LIKE '%$search%' OR 
        center_manager LIKE '%$search%' OR 
        sector LIKE '%$search%' OR 
        qualification_title LIKE '%$search%' OR 
        accreditation_number LIKE '%$search%')";
}

if (!empty($selected_province)) {
    $where_clause[] = "province = '$selected_province'";
}

$where_sql = !empty($where_clause) ? 'WHERE ' . implode(' AND ', $where_clause) : '';

// Main query
$query = "SELECT province, assessment_center, center_manager, sector, qualification_title, 
          accreditation_number, date_accredited, valid_until 
          FROM ac_data 
          $where_sql 
          LIMIT $offset, $records_per_page";
$result = mysqli_query($conn, $query);

$results = [];
while ($row = mysqli_fetch_assoc($result)) {
    $results[] = $row;
}

// Count total records
$count_query = "SELECT COUNT(*) as total FROM ac_data $where_sql";
$count_result = mysqli_query($conn, $count_query);
$count_row = mysqli_fetch_assoc($count_result);
$total_records = (int)$count_row['total'];
$total_pages = ceil($total_records / $records_per_page);

echo json_encode([
    "results" => $results,
    "totalPages" => $total_pages
]);

mysqli_close($conn);
?>
