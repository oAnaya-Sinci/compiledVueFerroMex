<?php 

  //Define cipher 
  $cipher = "aes-256-cbc"; 

  //Decrypt data 
  $decrypted_data = openssl_decrypt($encrypted_data, $cipher, $encryption_key, 0, $iv); 

  echo "Decrypted Text: " . $decrypted_data; 
?>