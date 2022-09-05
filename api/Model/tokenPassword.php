<?php 

  class tokenPassword{

    //Define cipher 
    private $_cipher = "aes-256-cbc"; 
    private $_encryption_key = 0;
    private $_iv = 0;

    function __construct(){

      $iv_size = openssl_cipher_iv_length($this->_cipher); 

      $this->_encryption_key = openssl_random_pseudo_bytes(32);
      $this->_iv = openssl_random_pseudo_bytes($iv_size);
    }

    public function encryptToken($data){

      //Data to encrypt 
      return openssl_encrypt($data, $this->_cipher, $this->_encryption_key, 0, $this->_iv); 
    }

    public function decryptToken($encrypted_data){

      //Decrypt data 
      return openssl_decrypt($encrypted_data, $this->_cipher, $this->_encryption_key, 0, $this->_iv); 
    }
  }