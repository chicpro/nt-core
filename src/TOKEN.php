<?php
/**
 * Token Class
 */

class TOKEN
{
    protected $token;
    protected $name;
    protected $time;

    public function __construct()
    {
        $this->token = '';
        $this->name  = '';
        $this->time  = (int)__c('cf_token_time'); // sec
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function getToken(string $name = 'ss_token', int $length = NT_TOKEN_LENGTH)
    {
        if ($name)
            $this->setName($name);

        $key = randomChar($length);

        $enc = new STRENCRYPT();
        $this->token = $enc->encrypt($key);

        $_SESSION[$this->name] = array(
            'token' => $this->token,
            'time'  => time()
        );

        return $this->token;
    }

    public function verifyToken(string $token, string $name = 'ss_token', bool $unset = true)
    {
        if (!$token)
            return false;

        if ($name)
            $this->setName($name);

        $ssToken = $_SESSION[$this->name];

        if (!is_array($ssToken) || empty($ssToken)) {
            unset($_SESSION[$this->name]);
            return false;
        }

        if ($token !== $ssToken['token']) {
            unset($_SESSION[$this->name]);
            return false;
        }

        if (time() - $ssToken['time'] > $this->time) {
            unset($_SESSION[$this->name]);
            return false;
        }

        if ($unset === true)
            unset($_SESSION[$this->name]);

        return true;
    }
}