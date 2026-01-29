<?php

namespace App\utils;

class Sanitize
{

    /**
     * removes , from currency
     *
     * @param string $currency
     * @return float
     */
    public static function integer($currency)
    {
        $clean = preg_replace('/,/', "", $currency);
        return filter_var($clean, FILTER_VALIDATE_FLOAT) ?: 0.0;
    }

    /**
     * filters dirty string characters
     *
     * @param string $str
     * @return string
     */
    public static function string($str)
    {
        $str = strip_tags($str);
        return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Remove all illegal characters from a url
     *
     * @param string $url
     * @return string
     */
    public static function url($url)
    {
        return filter_var($url, FILTER_SANITIZE_URL);
    }

    /**
     * Returns encoded html string
     *
     * @param string $html
     * @return string
     */
    public static function html($html)
    {
        return htmlspecialchars($html, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Validate email
     *
     * @param string $email
     * @return mixed
     */
    public static function email($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
}
