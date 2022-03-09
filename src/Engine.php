<?php

namespace LifeSpikes\PHPNode;

use LifeSpikes\PHPNode\Exceptions\NodeInstanceException;

class Engine
{
    static function spawn(string $executable, array $args = [], ?string $nodeBinary = null): Instance
    {
        if (!file_exists($executable)) {
            throw new NodeInstanceException("No such file $executable");
        }

        return new Instance(
            ...self::instanceParams($executable, $args, $nodeBinary)
        );
    }

    public static function instanceParams(string $executable, array $args = [], ?string $nodeBinary = null): array
    {
        return [
            'node' => ($nodeBinary ?? self::getNodeBinary()),
            'args' => [$executable, ...array_values($args)]
        ];
    }

    protected static function getNodeBinary(): ?string
    {
        if (file_exists(getenv('NODE_BINARY'))) {
            return getenv('NODE_BINARY');
        }

        /* Fallback to Node from PATH */

        exec('which node', $output);
        $output = trim(implode('', $output));

        if (file_exists($output)) {
            return $output;
        }

        return null;
    }
}
