<?php
class Encryption extends Trongate {

/*

In PHP, the 'iv' (initialization vector) is a random string of data that is used as an input to the encryption algorithm. It is used to add an extra layer of security to the encryption process, by making it more difficult for an attacker to predict the encryption key or decrypt the data.

The initialization vector is typically generated randomly and is used in combination with a secret key to encrypt the data. It is important to ensure that the initialization vector is unique for each message that is encrypted, as using the same initialization vector for multiple messages can make it easier for an attacker to guess the key or decrypt the data.

When using the _encrypt() function, the initialization vector is passed as an argument to the function. It is also important to store the initialization vector along with the encrypted data, as it is needed to decrypt the data later on.

Using a unique initialization vector (iv) for each record can help to make it more difficult for an attacker to successfully decrypt the data if the database is attacked and the attacker is able to view the ivs along with the encrypted data.

An initialization vector is a piece of data that is used as an input to the encryption algorithm and is used to add an extra layer of security to the encryption process, by making it more difficult for an attacker to predict the encryption key or decrypt the data. By using a unique iv for each record, it becomes more difficult for an attacker to identify patterns in the encrypted data and to use this information to decrypt the data.

---------------------------------------------------------------------------------------------------------------------------------------------

In cryptography, a secret key is a piece of information that is used to encrypt and decrypt data. It is used in combination with an encryption algorithm to transform plaintext (unencrypted data) into ciphertext (encrypted data) and vice versa. The secret key is typically shared between the sender and receiver of the data, and is used to ensure that only the intended parties can read the data.

In the context of PHP, the secret key is a string of data that is used as an input to the encryption functions openssl_encrypt and openssl_decrypt. It is used in combination with an initialization vector (iv) to encrypt and decrypt data.

The secret key should be kept secret and should not be shared with anyone else. It is important to use a strong, unique secret key for each message that is encrypted, as using the same secret key for multiple messages can make it easier for an attacker to guess the key or decrypt the data.

---------------------------------------------------------------------------------------------------------------------------------------------


The 'key' property in the JSON response is the base64-encoded representation of the secret key used for encryption.

In many encryption schemes, the secret key is a string of data that is used as an input to the encryption algorithm. It is typically shared between the sender and receiver of the data, and is used to ensure that only the intended parties can read the data.

Here, the 'key' property is base64-encoded, which means that it has been transformed into a string of characters that consists of the characters A-Z, a-z, 0-9, +, and /. This is a common way to encode binary data as a string, and is often used to transmit binary data over a network or to store it in a text-based format.

To use the 'key' property as the secret key, you will need to decode it from its base64-encoded representation. In PHP, you can use the base64_decode function to do this:
*/

    function test() {
        $str = "<h1>The IV</h1> In PHP, the 'iv' (initialization vector) is a random string of data that is used as an input to the encryption algorithm. It is used to add an extra layer of security to the encryption process, by making it more difficult for an attacker to predict the encryption key or decrypt the data.

The initialization vector is typically generated randomly and is used in combination with a secret key to encrypt the data. It is important to ensure that the initialization vector is unique for each message that is encrypted, as using the same initialization vector for multiple messages can make it easier for an attacker to guess the key or decrypt the data.

When using the PHP functions openssl_encrypt and openssl_decrypt, the initialization vector is passed as an argument to the function. It is also important to store the initialization vector along with the encrypted data, as it is needed to decrypt the data later on.

<hr><br><br><br>

<h1>The Secret Key</h1> In cryptography, a secret key is a piece of information that is used to encrypt and decrypt data. It is used in combination with an encryption algorithm to transform plaintext (unencrypted data) into ciphertext (encrypted data) and vice versa. The secret key is typically shared between the sender and receiver of the data, and is used to ensure that only the intended parties can read the data.

In the context of PHP, the secret key is a string of data that is used as an input to the encryption functions openssl_encrypt and openssl_decrypt. It is used in combination with an initialization vector (iv) to encrypt and decrypt data.

The secret key should be kept secret and should not be shared with anyone else. It is important to use a strong, unique secret key for each message that is encrypted, as using the same secret key for multiple messages can make it easier for an attacker to guess the key or decrypt the data.

<hr><br><br><br>


The 'key' property in the JSON response is the base64-encoded representation of the secret key used for encryption.

In many encryption schemes, the secret key is a string of data that is used as an input to the encryption algorithm. It is typically shared between the sender and receiver of the data, and is used to ensure that only the intended parties can read the data.

Here, the 'key' property is base64-encoded, which means that it has been transformed into a string of characters that consists of the characters A-Z, a-z, 0-9, +, and /. This is a common way to encode binary data as a string, and is often used to transmit binary data over a network or to store it in a text-based format.

To use the 'key' property as the secret key, you will need to decode it from its base64-encoded representation. In PHP, you can use the base64_decode function to do this:";
echo $str; die();
        $str = 'Simon';
        echo $str.'<br>';
        $enc_response = $this->_encrypt($str);
        json($enc_response);
    }

    function _encrypt($string) {
        $key = openssl_random_pseudo_bytes(16);
        $iv = openssl_random_pseudo_bytes(12);
        $ciphertext = openssl_encrypt($string, 'chacha20-poly1305', $key, OPENSSL_RAW_DATA, $iv);
        return array(
            'encrypted_string' => base64_encode($ciphertext),
            'key' => base64_encode($key),
            'iv' => base64_encode($iv)
        );
    }

    function _decrypt($encrypted_string, $key, $iv) {
        $encrypted_string = base64_decode($encrypted_string);
        $key = base64_decode($key);
        $iv = base64_decode($iv);
        $plaintext = openssl_decrypt($encrypted_string, 'chacha20-poly1305', $key, OPENSSL_RAW_DATA, $iv);
        return $plaintext;
    }

}