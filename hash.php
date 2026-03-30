<?php
$hash = password_hash('admin123', PASSWORD_DEFAULT);
echo "UPDATE users SET password = '" . $hash . "' WHERE username = 'admin';";
?>
