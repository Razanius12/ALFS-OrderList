<?php
// Required headers
header('Content-Type: application/json');

// Include database connection
require_once '../../config/database.php';

// Initialize response array

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the data from the POST request
    $id_maps = isset($_POST['id_maps']) ? $_POST['id_maps'] : null;
    $name_city_district = isset($_POST['name_city_district']) ? $_POST['name_city_district'] : null;
    $link_embed = isset($_POST['link_embed']) ? $_POST['link_embed'] : null;

    // Validate inputs
    if ($id_maps && $name_city_district && $link_embed) {
        // Validate ID
        if (filter_var($id_maps, FILTER_VALIDATE_INT) === false) {
            $response['success'] = false;
            $response['message'] = "Invalid ID.";
            echo json_encode($response);
            exit;
        }
        $response = array();
if (filter_var($link_embed, FILTER_VALIDATE_URL)) 
    $link_embed = sprintf(
     '<iframe src="%s" width="100%%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>',
     htmlspecialchars($link_embed)
    );

        // Prepare an SQL statement to prevent SQL injection
        $stmt = $conn->prepare("UPDATE gmaps SET name_city_district = ?, link_embed = ? WHERE id_maps = ?");
        $stmt->bind_param("ssi", $name_city_district, $link_embed, $id_maps);

        // Execute the statement
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $response['success'] = true;
                $response['message'] = "Office updated successfully.";
            } else {
                $response['success'] = false;
                $response['message'] = "No changes made. The office might not exist.";
            }
        } else {
            $response['success'] = false;
            $response['message'] = "Failed to update office: " . $stmt->error;
        }

                // Close the statement
                $stmt->close();
            } else {
                $response['success'] = false;
                $response['message'] = "All fields are required.";
            }
        } else {
            $response['success'] = false;
            $response['message'] = "Invalid request method.";
        }
        
        // Close the database connection
        $conn->close();
        
        // Return the response as JSON
        echo json_encode($response);
        ?>