<?php

namespace LifeSpikes\PHPNode\Enums;

/**
 * We rely on standard usage of exit codes based on TLDP docs,
 * but developers are not under any requirement to follow them
 * so use the names of this enum at your own risk.
 *
 * @see https://tldp.org/LDP/abs/html/exitcodes.html
 */
enum ProcessStatus: int
{
    case PHP_PROC_ERROR = -1;

    case SUCCESS = 0;
    case GENERAL_ERROR = 1;
    case SHELL_ERROR = 2;
    case CANNOT_EXECUTE = 126;
    case NOT_FOUND = 127;
    case BASH_EXIT_MISUSE = 128;
    case SIGINT_EXIT = 130;
}
