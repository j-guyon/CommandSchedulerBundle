<?php

namespace JMose\CommandSchedulerBundle\Service;

use JMose\CommandSchedulerBundle\Entity\ScheduledCommand;
use Cron\CronExpression as CronExpressionLib;

/**
 * Provider simplified access to Schedule Commands (ON-Demand)
 *
 * @author Carlos Sosa
 */
class SchedulerService {
    
    /**
     * @var \Doctrine\Bundle\DoctrineBundle\Registry
     */
    private $doctrine;
    
    /**
     *
     * @var string
     */
    private $commandName;
    
    /**
     *
     * @var ScheduledCommand
     */
    private $command;
    
    /**
     * 
     * @param \Doctrine\Bundle\DoctrineBundle\Registry $doctrine
     */
    public function __construct(\Doctrine\Bundle\DoctrineBundle\Registry $doctrine) { $this->doctrine = $doctrine; }
    
    /** Aliases */
    /**
     * @param $commandName
     * @return SchedulerService
     */
    public function command( $commandName) { return $this->cmd($commandName); }

    /**
     * @param $commandName
     * @return SchedulerService
     */
    public function get( $commandName) { return $this->cmd($commandName); }

    /**
     * Set command to handle
     * 
     * @param string $commandName
     * @return SchedulerService
     */
    public function cmd( $commandName) {
        $this->commandName = $commandName;
        
        return $this;
    }

    /**
     * Check if command exists
     *
     * @return bool
     */
    public function exists()
    {
        try {            
            if ( $this->getCommand() ) {
                return true;
            }
            return false;
        } catch (\Exception $ex) {
            // TODO: Improve error handling            
            return false;
        }
    }


    /**
     * Command Actions
     */
    public function run         () { return $this->commandAction('run'); }
    public function stop        () { return $this->commandAction('stop'); }
    public function disable     () { return $this->commandAction('disable'); }
    public function enable      () { return $this->commandAction('enable'); }
    public function setOndemand () { return $this->commandAction( ScheduledCommand::MODE_ONDEMAND); }
    public function setAuto     () { return $this->commandAction( ScheduledCommand::MODE_AUTO); }
    
    /**
     * Command Statuses
     */
    public function isFailing  () { return $this->commandStatus('failing'); }
    public function isRunning  () { return $this->commandStatus('running'); }
    public function isStopped  () { return $this->commandStatus('stopped'); }
    public function isDisabled () { return $this->commandStatus('disabled'); }
    public function isEnabled  () { return $this->commandStatus('enabled'); }
    public function isOndemand () { return $this->commandStatus( ScheduledCommand::MODE_ONDEMAND); }
    public function istAuto    () { return $this->commandStatus( ScheduledCommand::MODE_AUTO); }
    
    /**
     *
     * @return ScheduledCommand
     * @throws \ErrorException
     */
    private function getCommand ( ) {
        if ( $this->command) {
            return $this->command;
        }
        
        if ( !$this->commandName ){
            throw new \ErrorException('Missing Command Name.');
        }
        
        $cmd = $this->doctrine->getRepository(ScheduledCommand::class)->findOneBy([
                    'name' => $this->commandName
                ]);
        
        if ( $cmd instanceof ScheduledCommand) {
            return $cmd;
        }
        
        throw new \ErrorException('Command Not Found.');
    }


    /**
     * Schedule command to be executed in the next execution cycle
     *
     * @param string $action
     * @return bool
     */
    private function commandAction ( string $action ){
        try {            
            $cmd = $this->getCommand();

            switch ( $action ){
                case 'run': $cmd->setExecuteImmediately(true); break;
                case 'stop': $cmd->setExecuteImmediately(false); break;
                case 'disable': $cmd->setDisabled(true); break;
                case 'enable': $cmd->setDisabled(false); break;
                case ScheduledCommand::MODE_ONDEMAND: $cmd->setExecutionMode( ScheduledCommand::MODE_ONDEMAND); break;
                case ScheduledCommand::MODE_AUTO: 
                    if ( CronExpressionLib::isValidExpression( $cmd->getCronExpression()) ) {
                        $cmd->setExecutionMode( ScheduledCommand::MODE_AUTO); 
                    } else {
                        throw new \InvalidArgumentException('Invalid Cron Expression.');
                    }
                    break;
            }
            $this->doctrine->getManager()->persist($cmd);
            $this->doctrine->getManager()->flush($cmd);

            return true;
        } catch (\Exception $ex) {// TODO: Improve error handling            
            return false;
        }
    }

    /**
     * @param string $status
     * @return bool
     */
    private function commandStatus ( string $status ){
        try {            
            $cmd = $this->getCommand();

            switch ( $status ){
                case 'failing': return ( $cmd->getLastReturnCode() == -1 ); 
                case 'running': return ( $cmd->isLocked() || $cmd->getExecuteImmediately()); 
                case 'stopped': return ( ! $cmd->isLocked() && ! $cmd->getExecuteImmediately()); 
                case 'enabled': return ( ! $cmd->isDisabled());
                case 'disabled': return $cmd->isDisabled();
                case ScheduledCommand::MODE_ONDEMAND: ( $cmd->setExecutionMode() == ScheduledCommand::MODE_ONDEMAND);
                case ScheduledCommand::MODE_AUTO: ( $cmd->setExecutionMode() == ScheduledCommand::MODE_AUTO);
            }
            
            return false;
        } catch (\Exception $ex) {
            // TODO: Improve error handling            
            return false;
        }
    }
}
