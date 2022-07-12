<?php

namespace core\lib\EmailValidator;

final class Validator
{

    private $role = null;


    private $exampleTlds = [
        '.test',
        '.example',
        '.invalid',
        '.localhost',
    ];

    private $exampleDomains = [
        'example.com',
        'example.net',
        'example.org',
    ];

    public function isValid($email)
    {

        if (!$this->isEmail($email)) {
            return false;
        }


        if ($this->isExample($email)) {
            return false;
        }


        if ($this->isRole($email)) {
            return false;
        }


        if (!$this->hasMx($email)) {
            return false;
        }

        return true;
    }

    public static function isEmail($email)
    {
        if (is_string($email)) {
            return (bool)preg_match('/^.+@.+\..+$/i', $email);
        }

        return false;
    }

    public function isExample($email)
    {
        if (!$this->isEmail($email)) {
            return null;
        }

        $hostname = $this->hostnameFromEmail($email);

        if ($hostname) {
            if (in_array($hostname, $this->exampleDomains)) {
                return true;
            }

            foreach ($this->exampleTlds as $tld) {
                $length = strlen($tld);
                $subStr = substr($hostname, -$length);

                if ($subStr == $tld) {
                    return true;
                }
            }

            return false;
        }

        return null;
    }

    private function hostnameFromEmail($email)
    {
        $parts = explode('@', $email);

        if (count($parts) == 2) {
            return strtolower($parts[1]);
        }

        return null;
    }

    public function isRole($email)
    {
        if (!$this->isEmail($email)) {
            return null;
        }

        $user = $this->userFromEmail($email);

        if ($user) {

            // Search array for hostname
            if (in_array($user, $this->role)) {
                return true;
            }

            return false;
        }

        return null;
    }

    private function userFromEmail($email)
    {
        $parts = explode('@', $email);

        if (count($parts) == 2) {
            return strtolower($parts[0]);
        }

        return null;
    }

    public function hasMx($email)
    {
        if (!$this->isEmail($email)) {
            return null;
        }

        $hostname = $this->hostnameFromEmail($email);

        if ($hostname) {
            return checkdnsrr($hostname, 'MX');
        }

        return null;
    }

    public function isSendable($email)
    {

        if (!$this->isEmail($email)) {
            return false;
        }

        if ($this->isExample($email)) {
            return false;
        }

        if (!$this->hasMx($email)) {
            return false;
        }

        return true;
    }
}
