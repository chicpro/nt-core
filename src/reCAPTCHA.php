<?php
/**
 * Google reCAPTCHA class
 */

class reCAPTCHA
{
    protected $siteKey;
    protected $secretKey;

    protected $response;
    protected $siteVerifyUrl = 'https://www.google.com/recaptcha/api/siteverify';

    public function __construct(string $siteKey = '', string $secretKey = '')
    {
        if (!$siteKey)
            $siteKey = (string)__c('cf_recaptcha_site_key');

        if (!$secretKey)
            $secretKey = (string)__c('cf_recaptcha_secret_key');

        $this->setCredentials($siteKey, $secretKey);
    }

    public function setCredentials(string $siteKey, string $secretKey)
    {
        $this->siteKey   = $siteKey;
        $this->secretKey = $secretKey;
    }

    public function checkResponse(string $response)
    {
        if (!$this->siteKey || !$this->secretKey)
            return true;

        if (!$response)
            return false;

        $param = array(
            'secret'   => $this->secretKey,
            'response' => $response,
            'remoteip' => $_SERVER['REMOTE_ADDR']
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->siteVerifyUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $json = curl_exec($ch);
        curl_close($ch);

        $obj = json_decode($json);

        if ($obj->success == false)
            return false;

        return true;
    }

    public function getScript()
    {
        if ($this->siteKey) {
            echo '<script type="text/javascript">'.PHP_EOL;
            echo 'var onloadCallback = function() {'.PHP_EOL;
            echo '  grecaptcha.render("recaptcha_area", {'.PHP_EOL;
            echo '      "sitekey" : "'.__c('cf_recaptcha_site_key').'"'.PHP_EOL;
            echo '  });'.PHP_EOL;
            echo '};'.PHP_EOL;
            echo '</script>'.PHP_EOL;
        }
    }

    public function getElement()
    {
        if ($this->siteKey) {
            echo '<div id="recaptcha_area" class="recaptcha_area" data-toggle="popover" data-trigger="focus"></div>';
        }
    }
}