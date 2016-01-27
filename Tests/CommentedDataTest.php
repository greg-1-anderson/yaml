<?php

namespace Symfony\Component\Yaml\Tests;

use Symfony\Component\Yaml\CommentedData;

class CommentedDataTest extends \PHPUnit_Framework_TestCase
{
    protected $path;

    protected function setUp()
    {
        $this->path = __DIR__.'/Fixtures';
    }

    protected function tearDown()
    {
        $this->path = null;
    }

    public function testCommentedData()
    {
        $a = new CommentedData();

        $a['foo'] = "hello";
        $a['bar'] = "world";

        $a['bucket']['a'] = "first";
        $a['bucket']['b'] = "second";

        $a->setComment('foo', 'This is the comment for foo.');
        $a->setComment('bar', 'This is the comment for bar.');
        $a['bucket']->setComment('a', "This is the comment for bucket/a");
        $a['bucket']->setComment('b', "This is the comment for bucket/b");

        $this->assertEquals("hello", $a['foo']);
        $this->assertEquals("world", $a['bar']);

        $this->assertEquals("first", $a['bucket']['a']);
        $this->assertEquals("second", $a['bucket']['b']);

        $this->assertEquals('This is the comment for foo.', $a->getComment('foo'));
        $this->assertEquals('This is the comment for bar.', $a->getComment('bar'));
        $this->assertEquals("This is the comment for bucket/a", $a['bucket']->getComment('a'));
        $this->assertEquals("This is the comment for bucket/b", $a['bucket']->getComment('b'));
    }
}
