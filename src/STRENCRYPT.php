<?php
/**
 * String Encrypt and Decrypt class
 */

class STRENCRYPT
{
    protected $salt;
    protected $lenght;

    public function __construct(string $salt = '')
    {
        if (!$salt)
            $this->salt = __c('cf_enc_salt');
        else
            $this->salt = $salt;

        $this->length = strlen($this->salt);
    }

    public function encrypt(string $str)
    {
        if (!$str)
            return '';

        $length = strlen($str);
        $result = '';

        for ($i=0; $i<$length; $i++) {
            $char    = substr($str, $i, 1);
            $keychar = substr($this->salt, ($i % $this->length) - 1, 1);
            $char    = chr(ord($char) + ord($keychar));
            $result .= $char;
        }

        return base64_encode($result);
    }

    public function decrypt(string $str)
    {
        if (!$str)
            return '';

        $result = '';
        $str    = base64_decode($str);
        $length = strlen($str);

        for ($i=0; $i<$length; $i++) {
            $char    = substr($str, $i, 1);
            $keychar = substr($this->salt, ($i % $this->length) - 1, 1);
            $char    = chr(ord($char) - ord($keychar));
            $result .= $char;
        }

        return $result;
    }
}