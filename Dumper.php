<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Yaml;

/**
 * Dumper dumps PHP variables to YAML strings.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Dumper
{
    /**
     * The amount of spaces to use for indentation of nested nodes.
     *
     * @var int
     */
    protected $indentation = 4;

    /**
     * Sets the indentation.
     *
     * @param int $num The amount of spaces to use for indentation of nested nodes.
     */
    public function setIndentation($num)
    {
        $this->indentation = (int) $num;
    }

    /**
     * Dumps a PHP value to YAML.
     *
     * @param mixed $input                  The PHP value
     * @param int   $inline                 The level where you switch to inline YAML
     * @param int   $indent                 The level of indentation (used internally)
     * @param bool  $exceptionOnInvalidType true if an exception must be thrown on invalid types (a PHP resource or object), false otherwise
     * @param bool  $objectSupport          true if object support is enabled, false otherwise
     *
     * @return string The YAML representation of the PHP value
     */
    public function dump($input, $inline = 0, $indent = 0, $exceptionOnInvalidType = false, $objectSupport = false)
    {
        $output = '';
        $prefix = $indent ? str_repeat(' ', $indent) : '';

        if (static::checkInline($inline, $input)) {
            $output .= $prefix.Inline::dump($input, $exceptionOnInvalidType, $objectSupport);
        } else {
            $isAHash = static::isAHash($input);
            foreach ($input as $key => $value) {
                $willBeInlined = static::checkInline($inline - 1, $value);
                $comment = ($input instanceof CommentedData) ? $input->getCommentFor($key) : '';

                $output .= sprintf('%s%s%s%s%s',
                    static::formatComment($comment, $indent),
                    $prefix,
                    $isAHash ? Inline::dump($key, $exceptionOnInvalidType, $objectSupport).':' : '-',
                    $willBeInlined ? ' ' : "\n",
                    $this->dump($value, $inline - 1, $willBeInlined ? 0 : $indent + $this->indentation, $exceptionOnInvalidType, $objectSupport)
                ).($willBeInlined ? "\n" : '');
            }
        }

        return $output;
    }

    /**
     * Check to see if the given value should be inlied
     *
     * @param mixed $value
     * @return boolean          'true' if an array or CommentedData
     */
    protected static function checkInline($inline, $value)
    {
        return ($inline <= 0 || (!is_array($value) && (!$value instanceof CommentedData)) || empty($value));
    }

    /**
     * Determine if a given value is a hash (vs a numeric array)
     *
     * @param array $input  An array to test
     * @return boolean      'false' if all keys are numeric, true otherwise.
     */
    protected static function isAHash($input)
    {
        if (is_array($input)) {
            return array_keys($input) !== range(0, count($input) - 1);
        }
        if ($input instanceof CommentedData) {
            return $input->isAHash();
        }
    }

    /**
     * Convert a comment string into an appropriately-indented
     * block of text prefixed with the comment character ("#").
     *
     * @param string $commentString         The comment to format
     * @param int   $indent                 The level of indentation
     */
    protected static function formatComment($commentString, $indent)
    {
        $output = '';
        if (strlen($commentString) > 0) {
            $prefix = $indent ? str_repeat(' ', $indent) : '';
            foreach (explode("\n", $commentString) as $commentLine) {
                $output .= "$prefix# $commentLine\n";
            }
        }
        return $output;
    }
}
