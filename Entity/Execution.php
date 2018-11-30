<?php

namespace JMose\CommandSchedulerBundle\Entity;

use JMose\CommandSchedulerBundle\Entity\ScheduledCommand;

/**
 * Execution
 */
class Execution
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $executionDate;

    /**
     * @var integer
     */
    private $runtime = -1;

    /**
     * @var integer
     */
    private $returnCode;

    /**
     * @var ScheduledCommand
     */
    private $command;

    /**
     * @var string
     */
    private $output;

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
     * Set id
     *
     * @param int $id
     *
     * @return Execution
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return ScheduledCommand
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * @param ScheduledCommand $command
     *
     * @return Execution
     */
    public function setCommand($command)
    {
        $this->command = $command;
        return $this;
    }

    /**
     * Set executionDate
     *
     * @param \DateTime $executionDate
     *
     * @return Execution
     */
    public function setExecutionDate($executionDate)
    {
        $this->executionDate = $executionDate;

        return $this;
    }

    /**
     * Get executionDate
     *
     * @return \DateTime
     */
    public function getExecutionDate()
    {
        return $this->executionDate;
    }

    /**
     * Set runtime
     *
     * @param integer $runtime
     *
     * @return Execution
     */
    public function setRuntime($runtime)
    {
        $this->runtime = $runtime;

        return $this;
    }

    /**
     * Get runtime
     *
     * @return integer
     */
    public function getRuntime()
    {
        return $this->runtime;
    }

    /**
     * Set returnCode
     *
     * @param integer $returnCode
     *
     * @return Execution
     */
    public function setReturnCode($returnCode)
    {
        $this->returnCode = $returnCode;

        return $this;
    }

    /**
     * Get returnCode
     *
     * @return integer
     */
    public function getReturnCode()
    {
        return $this->returnCode;
    }


    /**
     * @return string
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @param string $output
     *
     * @return Execution
     */
    public function setOutput($output)
    {
        $this->output = $output;
        return $this;
    }

    /**
     * set members to values in object
     * @param array $data data to be stored in object
     */
    public function setData($data){
        foreach($data as $key => $val){
            $this->$key = $val;
        }
    }
}
