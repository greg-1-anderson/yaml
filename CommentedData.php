<?php

namespace Symfony\Component\Yaml;

/**
 * CommentedData wraps a nested array, each element of which
 * may contain a comment.
 *
 * @author Greg Anderson <greg.1.anderson@greenknowe.org>
 */
class CommentedData implements \ArrayAccess
{
    /**
     * The nested data.  The elements here can be either scalars,
     * regular php arrays, or CommentedData objects.  If an item
     * is a regular array, then none of its elements may be commented.
     *
     * @var array
     */
    protected $data = [];

    /**
     * The comment for this element datum; the "top-level" comment.
     */
    protected $comment = '';

    /**
     * Comments for the top-level elements of $data.  Always an
     * array of strings.
     */
    protected $comments = [];

    /**
     * Constructor.  Create commented data with the top-level
     * comments specified as an array of strings.
     */
    public function __construct($data = [], $comment = '', $comments = [])
    {
        $this->data = $data;
        $this->comment = $comment;
        // TODO: confirm that all elements in $comments are strings?
        $this->comments = (array) $comments;
    }

    /**
     * Replace the data.
     *
     * @param array $data the new data.
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * Get the data out of this object as a simple nested array with no comments.
     *
     * @return array
     */
    public function getData()
    {
        $result = [];
        foreach ($this->data as $key => $value) {
            if ($value instanceof CommentedData) {
                $value = $value->getData();
            }
        }
        return $result;
    }

    function setComment($comment)
    {
        $this->comment = $comment;
    }

    function getComment()
    {
        return $this->comment;
    }

    /**
     * Return the comment associated with a particular index.
     *
     * @param int|string $offset
     * @return string
     */
    public function getCommentFor($offset)
    {
        if (isset($this->data[$offset]) && ($this->data[$offset] instanceof CommentedData)) {
            return $this->data[$offset]->getComment();
        }
        if (isset($this->comments[$offset])) {
            return $this->comments[$offset];
        }
    }

    /**
     * Return the comment associated with a particular index.
     *
     * @param int|string $offset
     * @param string $value
     */
    public function setCommentFor($offset, $value)
    {
        if (isset($this->data[$offset]) && ($this->data[$offset] instanceof CommentedData)) {
            $this->data[$offset]->setComment($value);
        }
        else {
            // TODO: should we fail if $this->data[$offset] is not set?
            $this->comments[$offset] = $value;
        }
    }

    /**
     * Accessor for `isset()`.
     *
     * @param int|string $offset
     */
    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    /**
     * Accessor for [] operator.
     *
     * @param int|string $offset
     */
    public function offsetGet($offset)
    {
        // Any code that touches an array index with the [] operator
        // automatically converts that object into CommentedData, so that
        // setComment() may be called.
        if (!isset($this->data[$offset])) {
            $this->data[$offset] = new CommentedData();
        }
        if (is_array($this->data[$offset])) {
            $this->data[$offset] = new CommentedData($this->data[$offset], isset($this->comments[$offset]) ? $this->comments[$offset] : null);
        }
        return $this->data[$offset];
    }

    /**
     * Accessor for [] = operator.
     *
     * @param int|string $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        // Assigning to a data element does NOT change its comment.
        $this->data[$offset] = $value;
    }

    /**
     * Accessor for `unset()`.
     *
     * @param int|string $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
        unset($this->comments[$offset]);
    }
}

