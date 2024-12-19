<?php
header('Content-Type: application/json');

require_once '../../config/database.php';

$response = [
 'success' => false,
 'message' => '',
 'data' => null
];

try {
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

 // Clean and prepare the input
 $name_city_district = trim($_POST['name_city_district']);
 $link_embed = trim($_POST['link_embed']);

 // Normalize the iframe HTML
 // Remove any existing escaped quotes or backslashes
 $link_embed = stripslashes($link_embed);

 // If it's a Google Maps Share Link, convert to an embed URL
 if (preg_match('/https:\/\/maps\.app\.goo\.gl\/.+/', $link_embed)) {
  $response['message'] = 'Google Maps share links are not directly embeddable. Please use an embed link.';
  throw new Exception($response['message']);
 }

 // If it's a plain URL (not iframe), convert to iframe
 if (filter_var($link_embed, FILTER_VALIDATE_URL)) {
  if (strpos($link_embed, 'google.com/maps') !== false) {
   // Convert Google Maps URL to embed URL format
   $link_embed = str_replace('/maps/', '/maps/embed?', $link_embed);
  }
  $link_embed = sprintf(
   '<iframe src="%s" width="100%%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>',
   htmlspecialchars($link_embed)
  );
 }

 // Ensure it's a valid iframe
 if (strpos($link_embed, '<iframe') === false) {
  throw new Exception('Invalid embed link format');
 }

 // Prepare the statement
 $stmt = $conn->prepare("INSERT INTO gmaps (name_city_district, link_embed) VALUES (?, ?)");
 $stmt->bind_param("ss", $name_city_district, $link_embed);

 if ($stmt->execute()) {
  $response['success'] = true;
  $response['message'] = 'Map entry added successfully';
  $response['data'] = [
   'id_maps' => $stmt->insert_id,
   'name_city_district' => $name_city_district,
   'link_embed' => $link_embed
  ];
 } else {
  throw new Exception('Failed to add map entry: ' . $stmt->error);
 }

 $stmt->close();
} catch (Exception $e) {
 $response['success'] = false;
 $response['message'] = $e->getMessage();

 // Log the error
 error_log("Exception in addAlfOffices: " . $e->getMessage());
} finally {
 $conn->close();

 // Output JSON response
 echo json_encode($response, JSON_PRETTY_PRINT);
 exit;
}
?>