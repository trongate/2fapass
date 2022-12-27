<?php
class Encryption extends Trongate {

    function _encrypt($string) {
        $key = openssl_random_pseudo_bytes(16);
        $nonce = openssl_random_pseudo_bytes(12);
        $ciphertext = openssl_encrypt($string, 'chacha20-poly1305', $key, OPENSSL_RAW_DATA, $nonce);
        return array(
            'encrypted_string' => base64_encode($ciphertext),
            'key' => base64_encode($key),
            'nonce' => base64_encode($nonce)
        );
    }

    function _decrypt($encrypted_string, $key, $nonce) {
        $encrypted_string = base64_decode($encrypted_string);
        $key = base64_decode($key);
        $nonce = base64_decode($nonce);
        $plaintext = openssl_decrypt($encrypted_string, 'chacha20-poly1305', $key, OPENSSL_RAW_DATA, $nonce);
        return $plaintext;
    }

}