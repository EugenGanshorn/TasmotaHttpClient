<?php

namespace TasmotaHttpClient;

class Url
{
    protected $ipAddress;
    protected $username;
    protected $password;

    /**
     * @param string $command
     * @param null|string $payload
     * @return string
     */
    public function build(?string $command = null, ?string $payload = null): string
    {
        $httpData = [];

        $cmnd = trim(sprintf('%s %s', $command, $payload));

        if (!empty($cmnd)) {
            $httpData['cmnd'] = $cmnd;
        }

        // add username and password only if both are given
        if (!empty($this->username) && !empty($this->password)) {
            $httpData['user'] = $this->username;
            $httpData['password'] = $this->password;
        }

        $httpQuery = http_build_query($httpData, '', '&', PHP_QUERY_RFC3986);

        return sprintf('http://%s/cm?%s', $this->ipAddress, $httpQuery);
    }

    /**
     * @return mixed
     */
    public function getIpAddress()
    {
        return $this->ipAddress;
    }

    /**
     * @param mixed $ipAddress
     * @return Url
     */
    public function setIpAddress($ipAddress): Url
    {
        $this->ipAddress = $ipAddress;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     * @return Url
     */
    public function setUsername($username): Url
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     * @return Url
     */
    public function setPassword($password): Url
    {
        $this->password = $password;
        return $this;
    }
}
