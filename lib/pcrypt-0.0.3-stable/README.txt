**********************************************************************
* Id: README.txt
* Subject: pcrypt 0.0.3 (stable)
* Author: Tim Gall (Oz-DevWorX)
* Date: 2009-02-02 13:53 (+10 GMT)
*
* Simple-Description: 2 way encryption class for PHP - (read me)
* Copyright (C) 2009 Tim Gall (Oz-DevWorX)
* pcrypt is Released under the GNU General Public License
* ********************************************************************

==============================================
CONTENTS:
1) Intro and acknowledgements
2) System Requirements
3) Installation
4) Use

==============================================
1) Intro and acknowledgements
----------------------------------------------
Encrypt or decrypt your data using a single method call instead of having to wade
through the complexities of mcrypt every time you need a strong encryption system.

I have been using this class since I wrote it in 2007-06-07.
I designed it for encrypting passwords and sensitive database data on a number of commercial websites.

The Base 64 encoding functions used by this class for url, cookie and algrythm safety 
were copied from the PHP documents.
I would like to thank Massimo Scamarcia for his public contribution in the php manual.
Page: http://us2.php.net/manual/en/function.base64-encode.php#63543

==============================================
2) System Requirements
----------------------------------------------
php 5 (recommended)
or php 4

==============================================
3) Installation
----------------------------------------------
Copy the file "pcrypt.php" to a location in your file structure that you want to access it from
(presumably on a web server).
If your not sure, I would put it somewhere like:
/includes/classes/pcrypt.php

There are no settings to change, however your web-server should have the "mcrypt" php extension installed.

==============================================
4) Use
----------------------------------------------
See the test script included with this file.
The code is easy to follow. Ive also pasted it below incase the test script is for some reason absent.

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