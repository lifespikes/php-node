<?php

namespace LifeSpikes\PHPNode;

use LifeSpikes\PHPNode\Exceptions\NodeInstanceException;

class Pipe
{
    public function __construct(
        public $pipe
    ) {
        if (!is_resource($pipe)) {
            throw new NodeInstanceException('Pipe is not a resource.');
        }
    }

    public function write(string|array $contents): bool|int
    {
        if (gettype($contents) === 'array') {
            $contents = json_encode($contents);
        }

        $written = fwrite($this->pipe, $contents);
        fclose($this->pipe);

        return $written;
    }

    public function read(): string|array
    {
        $output = trim(stream_get_contents($this->pipe));
        fclose($this->pipe);

        if (is_array(($payload = json_decode($output, true)))) {
            return $payload;
        }

        return $output;
    }
}
