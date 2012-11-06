<?php
/* ********************************************************************
 * $id: pcrypt.php ver. 0.0.3 $
 *
 * Copyright � 2007-2009 Tim Gall (Oz-DevWorX)
 * ********************************************************************
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * ********************************************************************
 * DESCRIPTION:
 * Simple two way PHP encryption class (Uses mcrypt php functions)
 * --------------------------------------------------
 * To make initialise this class:
 *   include('path/to/this/class/pcrypt.php'); // path/to/this/file/pcrypt.php
 *   $key = 'any unique static value'; // string. Please make it a good one and store securely
 *   $encryptor = new pcrypt($key); // init class
 * To change keys during use:
 *   $encryptor->make_key('new key source');
 * To cipher/decipher stuff:
 *   $cipher_text = $encryptor->cipher($plain_text);
 *   OR
 *   $plain_text = $encryptor->decipher($cipher_text);
 * To cleanup after use:
 *   if(version_compare(PHP_VERSION, '5', '<' )) $encryptor->destruct_cipher();
 * --------------------------------------------------
 * FEATURES:
 * Output is url and cookie safe and cypher-algorythm friendly.
 * IV is fully reproducable, NOT random;
 * this allows for smaller data storage requirements.
 * --------------------------------------------------
 * NOTES:
 * decryption IV & salt must match encryption IV & salt
 * use a combination of static reproducable values like users login name or/and email-address or and secret-key, etc.
 * Important: Keep the key's information in a secure location (preferably not hardcoded).
 * --------------------------------------------------
 * Author: Tim Gall (Oz-DevWorX)
 * Date: 2007-06-07  14:19 (+10 GMT)
 * Last Modified: 2009-02-01 14-50 (+10 GMT) - updated comments, renamed class from cypher_text to pcrypt
 *
 * Released under the GNU General Public License
 * --------------------------------------------------
 * You can freely make any changes you want to the code and can use it however you like, including redistribution.
 * In the interests of the (OSI) Open-Source-Initiative the copyright and license block at the top must remain intact.
 */
class pcrypt {
  var $cipher_key;
  var $cipher;
  var $iv;

  // constructor php 5
  function __construct($c_key, $append='') {
    $this->make_key($c_key.$append);
  }
  // constructor php 4
  // (use this one in your code for safety)
/*  function pcrypt($c_key, $append='') {
    $this->__construct($c_key, $append);
  } */

  // destructor php 5 (auto cleanup)
  function __destruct() {
    mcrypt_module_close($this->cipher);
  unset($this->cipher);
    unset($this->iv);
    unset($this->cipher_key);
  }
  // destructor php 4 (manual trigger)
  // its always a good idea to allow for backward compatability (EG: php4).
  // To use safely, check the php version first EG:
  // if(version_compare(PHP_VERSION, '5', '<' )) $encryptor->destruct_cipher();
  function destruct_cipher() {
    if(isset($this->cipher)) $this->__destruct();
  }

  // public ciphering function
  function cipher($plain_text) {
    mcrypt_generic_init($this->cipher, $this->cipher_key, $this->iv);
    $encrypted = mcrypt_generic($this->cipher, $this->urlsafe_b64encode($plain_text));
    mcrypt_generic_deinit($this->cipher);
    return $this->urlsafe_b64encode($encrypted);
  }
  // public deciphering function
  function decipher($cipher_text) {
    mcrypt_generic_init($this->cipher, $this->cipher_key, $this->iv);
    $decrypted = mdecrypt_generic($this->cipher, $this->urlsafe_b64decode($cipher_text));
    mcrypt_generic_deinit($this->cipher);
    return $this->urlsafe_b64decode($decrypted);
  }

  // semi private make_key function. Can be called if you require a new key
  // for an existing instance of this class
  function make_key($c_key){
    // MCRYPT_3DES
    // MCRYPT_BLOWFISH
    // MCRYPT_RIJNDAEL_256
  $key_cipher = mcrypt_module_open(MCRYPT_RIJNDAEL_256,'',MCRYPT_MODE_CBC,'');
    $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC);
    $_iv = substr(sha1($c_key), 0, $iv_size); // temp iv for salt creation
    $_key = substr(md5($c_key), 0, $iv_size); // temp key for salt creation

    /// create secret cipher key
    mcrypt_generic_init($key_cipher, $_key, $_iv);
    $c_key = mcrypt_generic($key_cipher, $this->urlsafe_b64encode($c_key));
    mcrypt_generic_deinit($key_cipher);
    $c_key = $this->urlsafe_b64encode($c_key);

    $this->cipher = $key_cipher; // active cipher
    $this->iv = substr(md5($_iv), 0, $iv_size); // active iv
    $this->cipher_key = substr(md5($c_key), 0, $iv_size); // active key
  }

  // helper functions to maintain cipher-algorythm and cookie compatibility
  // regardless of contents.
  // US-ASCII charset is used for encoding/decoding.
  //
  // The Base64 functions (below) were copied from the php manual
  // All credit for these go to: Massimo Scamarcia (massimo dot scamarcia at gmail dot com)
  // Page: http://us2.php.net/manual/en/function.base64-encode.php#63543
  function urlsafe_b64encode($string) {
    $data = base64_encode($string);
    $data = str_replace(array('+','/','='),array('-','_','.'),$data);
    return $data;
  }
  function urlsafe_b64decode($string) {
    $data = str_replace(array('-','_','.'),array('+','/','='),$string);
    $mod4 = strlen($data) % 4;
    if($mod4) $data .= substr('====', $mod4);
    return base64_decode($data);
  }
}
?>