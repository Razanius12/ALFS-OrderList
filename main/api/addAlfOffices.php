<?php
// Required headers
header('Content-Type: application/json');

// Database connection
require_once '../../config/database.php';

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'data' => null
];

try {
    // Check if request method is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Validate required fields
    $required_fields = ['name_city_district', 'link_embed'];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            throw new Exception("$field is required");
        }
    }

    // Sanitize and validate input
    $name_city_district = mysqli_real_escape_string($conn, $_POST['name_city_district']);
    $link_embed = mysqli_real_escape_string($conn, $_POST['link_embed']);

    // Insert new map entry
    $insert_query = "INSERT INTO gmaps (name_city_district, link_embed) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $insert_query);
    mysqli_stmt_bind_param($stmt, "ss", $name_city_district, $link_embed);

    if (mysqli_stmt_execute($stmt)) {
        $response['success'] = true;
        $response['message'] = 'Map entry added successfully';
        $response['data'] = [
            'id_maps' => mysqli_insert_id($conn),
            'name_city_district' => $name_city_district,
            'link_embed' => $link_embed
        ];
    } else {
        throw new Exception('Failed to add map entry: ' . mysqli_error($conn));
    }

    mysqli_stmt_close($stmt);

} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();

    // Debug: Log the full exception
    error_log("Exception: " . $e->getMessage());
    error_log("Trace: " . $e->getTraceAsString());
} finally {
    mysqli_close($conn);

    // Ensure JSON is properly encoded
    echo json_encode($response, JSON_PRETTY_PRINT);
    exit; // Add exit to prevent any additional output
}
?>