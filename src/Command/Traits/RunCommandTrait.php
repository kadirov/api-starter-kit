<?php declare(strict_types=1);

namespace App\Command\Traits;

use App\Command\Interfaces\GetOutputInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;

/**
 * Trait RunCommandsTrait
 *
 * @mixin Command
 * @mixin GetOutputInterface
 * @package App\Command\Traits
 */
trait RunCommandTrait
{
    private function runCommand(string $name, array $arguments = []): int
    {
        $args = new ArrayInput($arguments);
        $args->setInteractive(false);

        return $this
            ->findCommand($name)
            ->run($args, $this->getOutput());
    }

    private function runCommandAndNotify(
        string $name,
        array $arguments = [],
        string $successText = null,
        string $failText = null
    ): void {
        if ($this->runCommand($name, $arguments) === 0) {
            $this->getSymfonyStyleOutput()->success($successText ?? "Command '$name' is successfully finished");
        } else {
            $this->getSymfonyStyleOutput()->error($failText ?? "Failed on run command '$name'");
        }
    }

    private function runSystemCommandAndNotify(
        string $command,
        string $successText = null,
        string $failText = null
    ): void {
        if (system($command) === false) {
            $this->getSymfonyStyleOutput()->error($failText ?? "Failed on run command '$command'");
        } else {
            $this->getSymfonyStyleOutput()->success($successText ?? "Command '$command' is successfully finished");
        }
    }

    private function findCommand(string $name): Command
    {
        return $this->getApplication()->find($name);
    }
}
