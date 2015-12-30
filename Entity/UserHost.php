<?php

namespace JMose\CommandSchedulerBundle\Entity;

/**
 * UserHost
 */
class UserHost
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string $title short description
     */
    private $title;

    /**
     * @var string $user user required for exection
     */
    private $user;

    /**
     * @var string $host hostname required for execution
     */
    private $host;

    /**
     * @var string $user_excluded user excluded from execution
     */
    private $user_excluded;

    /**
     * @var string $host_excluded hostname excluded from execution
     */
    private $host_excluded;

    /**
     * @var string $info detailed description of requirement (optional)
     */
    private $info;

    /**
     * @var bool $superuser
     */
    private $superuser = false;

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Set id
     *
     * @param integer $id ID
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set user
     *
     * @param string $user
     *
     * @return UserHost
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set host
     *
     * @param string $host
     *
     * @return UserHost
     */
    public function setHost($host)
    {
        $this->host = $host;

        return $this;
    }

    /**
     * Get host
     *
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }


    /**
     * get excluded user
     *
     * @return string
     */
    public function getUserExcluded()
    {
        return $this->user_excluded;
    }

    /**
     * set excluded user
     *
     * @param string $user_excluded
     */
    public function setUserExcluded($user_excluded)
    {
        $this->user_excluded = $user_excluded;
    }

    /**
     * get excluded hostname
     *
     * @return string
     */
    public function getHostExcluded()
    {
        return $this->host_excluded;
    }

    /**
     * set excluded hostname
     *
     * @param string $host_excluded
     */
    public function setHostExcluded($host_excluded)
    {
        $this->host_excluded = $host_excluded;
    }

    /**
     * Set info
     *
     * @param string $info
     *
     * @return UserHost
     */
    public function setInfo($info)
    {
        $this->info = $info;

        return $this;
    }

    /**
     * Get info
     *
     * @return string
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * set superuser flag
     *
     * @param bool $su
     *
     * @return $this to allow chaining
     */
    public function setSuperuser($su)
    {
        $this->superuser = $su;
        return $this;
    }

    /**
     * get Superuser flag
     *
     * @return bool
     */
    public function getSuperuser()
    {
        return $this->superuser;
    }
}

