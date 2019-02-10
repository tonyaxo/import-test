<?php

namespace app\models;

use yii\helpers\Json;

/**
 * Class OfferReader
 * @package app\models
 */
class OfferReader extends BaseReader
{
    /**
     * @inheritdoc
     */
    protected function readElement(): array
    {
//        В звисимости от задач можно делать и так:
//        $m = new Offer([
//            ...
//
//            'available' => $node->getAttribute('available'),
//        ]);
//        if ($m->validate()) {
//            return $m->toArray();
//        }
//        ...

        /** @var \DOMElement $node */
        $node = $this->expand();
        $res = [
            'id' => $node->getAttribute('id'),
            'own_category_id' => null,
            'available' => $node->getAttribute('available') === 'true' ? 1 : 0,
            'url' => null,
            'pictures' => null,
            'price' => 0,
            'currency' => 'EUR',
            'name' => '',
            'description' => '',
            'params' => null,
            'created_at' => time(),
        ];
        /** @var \DOMElement $child */
        foreach ($node->childNodes as $child) {
            if ($child->nodeType !== \XML_ELEMENT_NODE) {
                continue;
            }

            if ($child->tagName === 'url') {
                $res[$child->tagName] = $child->textContent;
            }
            if ($child->tagName === 'price') {
                $res[$child->tagName] = $child->textContent;
            }
            if ($child->tagName === 'name') {
                $res[$child->tagName] = $child->textContent;
            }
            if ($child->tagName === 'description') {
                $res[$child->tagName] = $child->textContent;
            }
            if ($child->tagName === 'currencyId') {
                $res['currency'] = $child->textContent;
            }

            if ($child->tagName === 'categoryId') {
                $res['categories'][] = $child->textContent;
                if ($child->hasAttribute('type') && $child->getAttribute('type') === 'Own') {
                    $res['own_category_id'] = $child->textContent;
                }
            }

            if ($child->tagName === 'param') {
                $res['params'][] = [
                    'name' => $child->getAttribute('name'),
                    'value' => $child->textContent,
                ];
            }

            if ($child->tagName === 'pictures' && $child->hasChildNodes()) {
                /** @var \DOMNode $picture */
                foreach ($child->childNodes as $picture) {
                    if ($picture->nodeType !== \XML_ELEMENT_NODE) {
                        continue;
                    }
                    $res['pictures'][] = $picture->textContent;
                }
            }
        }
        $res['params'] = Json::encode($res['params']);
        $res['pictures'] = Json::encode($res['pictures']);
        return $res;
    }

    /**
     * @inheritdoc
     */
    protected function isElementStart(): bool
    {
        return $this->nodeType == \XMLReader::ELEMENT && $this->name == 'offer';
    }
}
