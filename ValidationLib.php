<?php

class ValidationLib
{
    // - - - Properties - - -
    private $trigger = true;

    // - - - Methods - - -

    //Trigger
    function pass()
    {
        if ($this->trigger == true) {
            return true;
        }
        return false;
    }

    //Validate - Empty Field
    function isEmpty($value)
    {
        if (trim($value) == '') {
            $this->trigger = false;
            return true;
        }
        return false;
    }

    //Validate - Has Numbers
    function hasNum($value)
    {
        $pattern = '/[0-9]/';
        if (preg_match($pattern, $value)) {
            $this->trigger = false;
            return true;
        }
        return false;
    }

    //Validate - Too Long
    function tooLong($value, $length)
    {
        if (strlen(trim($value)) > $length) {
            $this->trigger = false;
            return true;
        }
        return false;
    }

    //Validate - Phone
    function notPhone($value)
    {
        $pattern = "([0-9]{3} [0-9]{3} [0-9]{4}|[0-9]{3}-[0-9]{3}-[0-9]{4}|[0-9]{10})";
        if (!preg_match($pattern, $value)) {
            $this->trigger = false;
            return true;
        }
        return false;
    }

    //Validate - Postal Code
    function notPostal($value)
    {
        $pattern = '(([A-Za-z][0-9]){3}|[A-Za-z]{1}[0-9]{1}[A-Za-z]{1} {1}[0-9]{1}[A-Za-z]{1}[0-9]{1})';
        if (!preg_match($pattern, $value)) {
            $this->trigger = false;
            return true;
        }
        return false;
    }

    //Validate - Email
    function notEmail($value)
    {
        if (!filter_var(trim($value), FILTER_VALIDATE_EMAIL)) {
            $this->trigger = false;
            return true;
        }
        return false;
    }
}