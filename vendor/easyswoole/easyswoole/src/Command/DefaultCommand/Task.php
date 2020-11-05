<?php


namespace EasySwoole\EasySwoole\Command\DefaultCommand;


use EasySwoole\Bridge\Package;
use EasySwoole\Command\AbstractInterface\CommandHelpInterface;
use EasySwoole\Command\AbstractInterface\CommandInterface;
use EasySwoole\Command\CommandManager;
use EasySwoole\EasySwoole\Command\Utility;
use EasySwoole\EasySwoole\Core;
use EasySwoole\Utility\ArrayToTextTable;
use Swoole\Coroutine\Scheduler;

class Task implements CommandInterface
{
    public function commandName(): string
    {
        return 'task';
    }

    public function desc(): string
    {
        return 'Task manager';
    }

    public function help(CommandHelpInterface $commandHelp): CommandHelpInterface
    {
        $commandHelp->addAction('status', 'status of the task');
        return $commandHelp;
    }

    public function exec(): ?string
    {
        Core::getInstance()->initialize();
        $action = CommandManager::getInstance()->getArg(0);
        $run = new Scheduler();
        $run->add(function () use (&$result, $action) {
            if (method_exists($this, $action) && $action != 'help') {
                $result = $this->{$action}();
                return;
            }

            $result = CommandManager::getInstance()->displayCommandHelp($this->commandName());
        });
        $run->start();
        return $result;
    }

    protected function status()
    {
        return Utility::bridgeCall($this->commandName(), function (Package $package) {
            return new ArrayToTextTable($package->getArgs());
        }, 'info');
    }
}
