<?php

require_once('methods/iyzico_app/IyzipayBootstrap.php');

IyzipayBootstrap::init();

class Sample
{
    public static function options()
    {
        # create client configuration
        $options = new \Iyzipay\Options();
        $options->setApiKey(IYZICO_KEY);
        $options->setSecretKey(IYZICO_SECRET_KEY);
        $options->setBaseUrl("https://api.iyzipay.com");
        return $options;
    }
} 