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

        $a->setComment('This is the top-level comment.');

        $a->setCommentFor('foo', 'This is the comment for foo.');
        $a->setCommentFor('bar', 'This is the comment for bar.');
        $a['bucket']->setComment("This is the comment for bucket");
        $a['bucket']->setCommentFor('a', "This is the comment for bucket/a");
        $a['bucket']->setCommentFor('b', "This is the comment for bucket/b");

        $keys = [];
        foreach ($a as $key => $value) {
            $keys[] = $key;
        }
        $this->assertEquals('foo,bar,bucket', implode(',', $keys));

        $this->assertEquals("hello", $a['foo']);
        $this->assertEquals("world", $a['bar']);

        $this->assertEquals("first", $a['bucket']['a']);
        $this->assertEquals("second", $a['bucket']['b']);

        $this->assertEquals('This is the top-level comment.', $a->getComment());
        $this->assertEquals('This is the comment for foo.', $a->getCommentFor('foo'));
        $this->assertEquals('This is the comment for bar.', $a->getCommentFor('bar'));
        $this->assertEquals("This is the comment for bucket", $a['bucket']->getComment());
        $this->assertEquals("This is the comment for bucket/a", $a['bucket']->getCommentFor('a'));
        $this->assertEquals("This is the comment for bucket/b", $a['bucket']->getCommentFor('b'));

        // Note that we want elements such as $a['foo'] to behave
        // like strings, so we do not support $a['foo']->setComment(...),
        // because we want $a['foo'] to return the native string object.
        // We could potentially return a CommentedData element here as well,
        // but then we would need to be able to typecast to the correct data
        // type as needed. If we *only* supported strings, this would likely
        // be doable via a _toString() method, but it is unclear whether we
        // could support the same for other scalar types.

        // Here are the operations that might be supported if we returned
        // wrapper objects for scalar types.

        // $a['foo']->setComment('This is the comment for foo.');
        // $a['bar']->setComment('This is the comment for bar.');
        // $a['bucket']['a']->setComment("This is the comment for bucket/a");
        // $a['bucket']['b']->setComment("This is the comment for bucket/b");
        // $this->assertEquals('This is the comment for foo.', $a['foo']->getComment());
        // $this->assertEquals('This is the comment for bar.', $a['bar']->getComment());
        // $this->assertEquals("This is the comment for bucket/a", $a['bucket']['a']->getComment());
        // $this->assertEquals("This is the comment for bucket/b", $a['bucket']['b']->getComment());

    }
}
