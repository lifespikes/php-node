<?php

namespace LifeSpikes\PHPNode;

use LifeSpikes\PHPNode\Exceptions\NodeInstanceException;

class Instance
{
    private array $spec = [
        ['pipe', 'r'],
        ['pipe', 'w'],
        ['pipe', 'w']
    ];

    private $process;

    private Pipe $stdin;
    private Pipe $stdout;
    private Pipe $stderr;

    private array $payload = [];
    private array $args = [];

    public function __construct(string $node, array $args)
    {
        if (!file_exists($node)) {
            throw new NodeInstanceException("No file found at $node");
        }

        $this->args = [$node, ...$args];
    }

    public function run(): FinishedProcess
    {
        $this->instantiate();
        $this->stdin->write($this->payload);

        return new FinishedProcess(
            $this->stdout->read(),
            $this->stderr->read(),
            proc_close($this->process)
        );
    }

    public function with(array $contents): FinishedProcess
    {
        $this->payload = $contents;
        return $this->run();
    }

    private function instantiate(): void
    {
        $process = proc_open($this->args, $this->spec, $pipes);
        $this->setHandles($process, $pipes);
    }

    private function setHandles($process, array $pipes): void
    {
        if (!is_resource($process)) {
            throw new NodeInstanceException("Node process is not a resource");
        }

        $this->process = $process;
        [$this->stdin, $this->stdout, $this->stderr] = array_map(
            fn ($r) => new Pipe($r), $pipes
        );
    }
}
