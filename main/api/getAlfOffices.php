<?php
// Required headers
header('Content-Type: application/json');

// Include database connection
require_once '../../config/database.php';

// Initialize response array
$response = array();

// Check if the request method is GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Check database connection
    if (!$conn) {
        $response['success'] = false;
        $response['message'] = "Database connection failed.";
        echo json_encode($response);
        exit;
    }

    // Prepare an SQL statement to select data
    $stmt = $conn->prepare("SELECT id_maps, name_city_district, link_embed FROM gmaps");
    
    // Execute the statement
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $offices = array();

        // Check how many rows were returned
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $offices[] = $row;
            }
            $response['success'] = true;
            $response['data'] = $offices;
        } else {
            $response['success'] = false;
            $response['message'] = "No offices found.";
        }
    } else {
        $response['success'] = false;
        $response['message'] = "Failed to retrieve offices: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
} else {
    $response['success'] = false;
    $response['message'] = "Invalid request method.";
}

// Close the database connection
$conn->close();

// Return the response as JSON
echo json_encode($response);
?>