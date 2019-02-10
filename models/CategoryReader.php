<?php

namespace app\models;

/**
 * Class CategoryReader
 * @package app\models
 */
class CategoryReader extends BaseReader
{
    /**
     * @inheritdoc
     */
    protected function readElement(): array
    {
        /** @var \DOMElement $node */
        $node = $this->expand();
        $parent = $node->getAttribute('parentId');
        $parent = !$parent ? null : $parent;
        return [
            'id' => $node->getAttribute('id'),
            'parent_id' => $parent,
            'name' => $node->textContent,
        ];
    }

    /**
     * @inheritdoc
     */
    protected function isElementStart(): bool
    {
        return $this->nodeType == \XMLReader::ELEMENT && $this->name == 'category';
    }
}
