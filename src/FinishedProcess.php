<?php

namespace LifeSpikes\PHPNode;

use LifeSpikes\PHPNode\Enums\ProcessStatus;
use LifeSpikes\PHPNode\Exceptions\NodeExitCodeException;

class FinishedProcess
{
    public function __construct(
        public array|string $output,
        public array|string $errors,

        public int $exitCode
    ) {
        $this->throwIfFailed();
    }

    public function status(): ProcessStatus|int
    {
        return ProcessStatus::tryFrom($this->exitCode) ?? $this->exitCode;
    }

    private function prettifyPipe(array|string $str): string
    {
        $str = trim(var_export($str, true), "'");

        $content = trim(implode(PHP_EOL, array_map(
            fn ($l) => "\t$l",
            explode(PHP_EOL, $str)
        )));

        return !strlen($content) ? "\tEmpty" : "\t$content";
    }

    private function throwIfFailed()
    {
        $title = fn ($s) => str_pad(" $s ", 24, '=', STR_PAD_BOTH);

        if ($this->status() !== ProcessStatus::SUCCESS || $this->exitCode > 128) {
            throw new NodeExitCodeException(implode(PHP_EOL, [
                "Non-success or fatal error exit code: $this->exitCode.",

                $title('STDOUT Contents'),
                $this->prettifyPipe($this->output),

                $title('STDERR Contents'),
                $this->prettifyPipe($this->errors),
            ]));
        }
    }
}
