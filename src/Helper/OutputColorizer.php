<?php

declare(strict_types=1);

namespace Timesplinter\Oyster\Helper;

/**
 * @author Pascal Muenst <pascal@timesplinter.ch>
 */
class OutputColorizer
{
    private static $availableForegroundColors = [
        'black' => ['set' => 30, 'unset' => 39],
        'red' => ['set' => 31, 'unset' => 39],
        'green' => ['set' => 32, 'unset' => 39],
        'yellow' => ['set' => 33, 'unset' => 39],
        'blue' => ['set' => 34, 'unset' => 39],
        'magenta' => ['set' => 35, 'unset' => 39],
        'cyan' => ['set' => 36, 'unset' => 39],
        'white' => ['set' => 37, 'unset' => 39],
        'default' => ['set' => 39, 'unset' => 39],
    ];

    private static $availableBackgroundColors = [
        'black' => ['set' => 40, 'unset' => 49],
        'red' => ['set' => 41, 'unset' => 49],
        'green' => ['set' => 42, 'unset' => 49],
        'yellow' => ['set' => 43, 'unset' => 49],
        'blue' => ['set' => 44, 'unset' => 49],
        'magenta' => ['set' => 45, 'unset' => 49],
        'cyan' => ['set' => 46, 'unset' => 49],
        'white' => ['set' => 47, 'unset' => 49],
        'default' => ['set' => 49, 'unset' => 49],
    ];

    private static $availableOptions = [
        'bold' => ['set' => 1, 'unset' => 22],
        'underscore' => ['set' => 4, 'unset' => 24],
        'blink' => ['set' => 5, 'unset' => 25],
        'reverse' => ['set' => 7, 'unset' => 27],
        'conceal' => ['set' => 8, 'unset' => 28],
    ];

    /**
     * @param string $str
     * @return string
     */
    public static function colorize(string $str): string
    {
        $str = preg_replace_callback('/<(fg|bg):(\w+)>/', function ($m) {
            $codeTable = 'bg' === $m[1] ? self::$availableBackgroundColors : self::$availableForegroundColors;
            return "\e[".$codeTable[$m[2]]['set']."m";
        }, $str);

        $str = preg_replace_callback('/<\/(fg|bg):(\w+)>/', function ($m) {
            $codeTable = 'bg' === $m[1] ? self::$availableBackgroundColors : self::$availableForegroundColors;
            return "\e[".$codeTable[$m[2]]['unset']."m";
        }, $str);

        return $str;
    }
}
