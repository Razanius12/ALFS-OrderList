<?php
// Required headers
header('Content-Type: application/json');

// Include database connection
require_once '../../config/database.php';

try {
 // Ensure all required fields are present
 $requiredFields = ['id_maps', 'name_city_district', 'link_embed'];
 foreach ($requiredFields as $field) {
  if (!isset($_POST[$field]) || empty($_POST[$field])) {
   throw new Exception("Missing required field: $field");
  }
 }

 // Sanitize inputs
 $id_maps = mysqli_real_escape_string($conn, $_POST['id_maps']);
 $name_city_district = mysqli_real_escape_string($conn, $_POST['name_city_district']);
 $link_embed = mysqli_real_escape_string($conn, $_POST['link_embed']);

 // Validate ID
 if (filter_var($id_maps, FILTER_VALIDATE_INT) === false) {
  throw new Exception("Invalid ID.");
 }

 // Prepare the link_embed (only wrap in iframe if it's not already an iframe)
 if (strpos($link_embed, '<iframe') === false) {
  if (filter_var($link_embed, FILTER_VALIDATE_URL)) {
   $link_embed = sprintf(
    '<iframe src="%s" width="100%%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>',
    htmlspecialchars($link_embed)
   );
  } else {
   throw new Exception("Invalid URL format for link_embed.");
  }
 }

 // Prepare UPDATE query
 $query = "UPDATE gmaps 
           SET name_city_district = '$name_city_district', 
            link_embed = '$link_embed' 
           WHERE id_maps = '$id_maps'";

 // Execute query
 if (mysqli_query($conn, $query)) {
  if (mysqli_affected_rows($conn) > 0) {
   echo json_encode([
    'success' => true,
    'message' => 'Office updated successfully'
   ]);
  } else {
   echo json_encode([
    'success' => true,
    'message' => 'No changes made. The office details might be the same.'
   ]);
  }
 } else {
  throw new Exception('Failed to update office: ' . mysqli_error($conn));
 }
} catch (Exception $e) {
 // Error handling
 echo json_encode([
  'success' => false,
  'message' => $e->getMessage()
 ]);
}

// Close connection
mysqli_close($conn);
?>