<?php
/**
 * Extension of Symfony\Component\Console\Output\Output to gather outputs and forward them to a default output
 */

namespace JMose\CommandSchedulerBundle\Component;

use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Output\OutputInterface;

class CommandOutput extends Output
{
    /** @var array */
    private $buffer = array();
    /** @var OutputInterface */
    private $defaultOutput;

    /**
     * @inheritdoc
     */
    public function doWrite($message, $newline)
    {
        array_push($this->buffer, $message . ($newline ? "\n" : ""));

        if (method_exists($this->defaultOutput, 'doWrite')) {
            $this->defaultOutput->doWrite($message, $newline);
        }
    }

    /**
     * get complete message buffer as array or string
     *
     * @param string $type array|string
     *
     * @return mixed
     */
    public function getBuffer($type = 'array')
    {
        $result = null;

        if ($type == 'array') {
            $result = $this->buffer;
        } else if ($type == 'string') {
            $result = implode('', $this->buffer);
        }

        return $result;
    }

    /**
     * get default Output
     * @return Output
     */
    public function getDefaultOutput()
    {
        return $this->defaultOutput;
    }

    /**
     * set default output handler
     *
     * @param OutputInterface $defaultOutput
     * @return CommandOutput
     */
    public function setDefaultOutput(OutputInterface $defaultOutput)
    {
        $this->defaultOutput = $defaultOutput;
        return $this;
    }
}
