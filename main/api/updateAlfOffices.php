<?php
// Required headers
header('Content-Type: application/json');

// Include database connection
require_once '../../config/database.php';

$response = [
 'success' => false,
 'message' => ''
];

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
 $link_embed = trim($_POST['link_embed']);

 // Validate ID
 if (!filter_var($id_maps, FILTER_VALIDATE_INT)) {
  throw new Exception("Invalid ID format.");
 }

 // Normalize the link_embed
 if (preg_match('/https:\/\/maps\.app\.goo\.gl\/.+/', $link_embed)) {
  $response['message'] = 'Google Maps share links are not directly embeddable. Please use an embed link.';
  throw new Exception($response['message']);
 }

 // If it's a plain URL, convert to iframe
 if (filter_var($link_embed, FILTER_VALIDATE_URL)) {
  if (strpos($link_embed, 'google.com/maps') !== false) {
   // Convert Google Maps URL to embed URL
   $link_embed = str_replace('/maps/', '/maps/embed?', $link_embed);
  }
  $link_embed = sprintf(
   '<iframe src="%s" width="100%%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>',
   htmlspecialchars($link_embed)
  );
 }

 // Ensure it's a valid iframe
 if (strpos($link_embed, '<iframe') === false) {
  throw new Exception('Invalid embed link format.');
 }

 // Prepare UPDATE query
 $stmt = $conn->prepare("UPDATE gmaps 
                                SET name_city_district = ?, 
                                 link_embed = ? 
                                WHERE id_maps = ?");
 $stmt->bind_param("ssi", $name_city_district, $link_embed, $id_maps);

 if ($stmt->execute()) {
  if ($stmt->affected_rows > 0) {
   $response['success'] = true;
   $response['message'] = 'Office updated successfully';
  } else {
   $response['success'] = true;
   $response['message'] = 'No changes made. The office details might be the same.';
  }
 } else {
  throw new Exception('Failed to update office: ' . $stmt->error);
 }

 $stmt->close();
} catch (Exception $e) {
 // Error handling
 $response['success'] = false;
 $response['message'] = $e->getMessage();

 // Log the error
 error_log("Exception in UpdateAlfOffice: " . $e->getMessage());
}

// Close connection
mysqli_close($conn);

// Return response
echo json_encode($response, JSON_PRETTY_PRINT);
