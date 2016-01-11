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
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return UserHost ($this)
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Set id
     *
     * @param integer $id ID
     *
     * @return UserHost ($this)
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
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
     * @return UserHost ($this)
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
     * @return UserHost ($this)
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
     *
     * @return UserHost ($this)
     */
    public function setUserExcluded($user_excluded)
    {
        $this->user_excluded = $user_excluded;

        return $this;
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
     *
     * @return UserHost ($this)
     */
    public function setHostExcluded($host_excluded)
    {
        $this->host_excluded = $host_excluded;

        return $this;
    }

    /**
     * Set info
     *
     * @param string $info
     *
     * @return UserHost ($this)
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
}

