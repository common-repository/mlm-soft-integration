<?php
/**
 * Class: MLMSoftAesEncryption.
 *
 * @since 3.4.24
 */

namespace MLMSoft\core\lib\crypto;

class MLMSoftAesEncryption
{
    /**
     * @return string|boolean
     */ 
    public static function decryptRemoteAuthToken($token, $key = false)
    {
        if ( ! $key ) {
            return false;
        }
        
        $data = base64_decode($token);
        $data = json_decode($data, true);
        
        if ( isset($data['data'], $data['iv'], $data['salt']) ) {
            
            $derivedKey = hash_pbkdf2('sha256', $key, hex2bin($data['salt']), 1000, 32, true);
            
            $decryptedPassword = openssl_decrypt(
                base64_decode($data['data']),
                "aes-256-cbc",
                $derivedKey,
                OPENSSL_RAW_DATA,
                hex2bin($data['iv'])
            );

            return $decryptedPassword;
        }

        return false;
    }
}

# --- EOF