<?php

namespace app\models;

/**
 * Class BaseReader
 * @package app\models
 */
abstract class BaseReader extends \XMLReader implements \Iterator
{
    protected $isOpened = false;

    protected $uri;
    protected $encoding;
    protected $options = 0;

    protected $current;
    protected $isValid = true;

    /**
     * Convert DOMElement to array.
     * @return array
     */
    abstract protected function readElement(): array;

    /**
     * Whether or not current element is XMLReader::ELEMENT
     * @return bool
     */
    abstract protected function isElementStart(): bool;

    /**
     * @inheritdoc
     */
    public function open($URI, $encoding = null, $options = 0): bool
    {
        $this->isOpened = parent::open($URI, $encoding, $options);
        if ($this->isOpened) {
            $this->uri = $URI;
            $this->encoding = $encoding;
            $this->options = $options;
        }
        return $this->isOpened;
    }

    /**
     * @inheritdoc
     */
    public function read()
    {
        $this->isValid = parent::read();
        if ($this->isValid === false) {
            return false;
        }
        $this->isValid = true;
        return $this->isValid;
    }

    public function rewind() {

//        echo __METHOD__ . '<br>';

        $this->current = null;

        if ($this->isOpened) {
            $this->isOpened = !$this->close();
        }
        if ($this->uri !== null) {
            $this->open($this->uri, $this->encoding, $this->options);
        }
        $this->internalNext();
    }

    public function current() {
        return $this->current;
    }

    public function key() {
        return $this->current['id'];
    }

    public function next($localname = null) {
        $this->internalNext();
    }

    public function valid() {
        if ($this->isOpened === false) {
            return false;
        }
        if ($this->isValid === false) {
            $this->isOpened = !$this->close();
            return false;
        }
        return true;
    }

    protected function internalNext(): void
    {
        while ($this->read()) {
            if ($this->isElementStart()) {
                $this->current = $this->readElement();
                break;
            }
        }
    }
}
