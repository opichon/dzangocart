<?php
/* Id: PCRYPT_TEST.php
 * Simple test script to illustrate the use of pcrypt to cipher/decipher data.
 *
 * Author: Tim Gall
 * Date: 2009-02-02 14-50 (+10 GMT)
 */

// I recommend you put the pcrypt.php class in a .htaccess protected folder rather than at the top level.
// For one, it will stop web-bots trying to index it.
// It will also eliminate the chance of someone tampering with it.
// The choice of location is up to you.
include('pcrypt.php'); // EG: path/to/this/file/pcrypt.php
$key = 'mysecretkey akldjshfsaldkjhfaslkdfjh=-+*'; // string. Please make it a good one and store securely
$encryptor = new pcrypt($key); // init class

//// To change keys during use:
//$encryptor->make_key('new key source');

$plain_text = 'This is some secret stuff';
echo 'Plain Text: ' . $plain_text . '<br />';

//// To cipher:
$cipher_text = $encryptor->cipher($plain_text);
echo 'Cipher Text: ' . $cipher_text . '<br />';

//// To decipher:
$plain_text = $encryptor->decipher($cipher_text);
echo 'Plain Text Again: ' . $plain_text . '<br />';

//To cleanup after use:
if(version_compare(PHP_VERSION, '5', '<' )) $encryptor->destruct_cipher();
?>