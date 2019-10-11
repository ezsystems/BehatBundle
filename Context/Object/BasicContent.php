<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\BehatBundle\Context\Object;

/**
 * * @deprecated in 7.0, will be removed in 8.0.
 *
 * Sentences for Fields.
 *
 * @method \EzSystems\BehatBundle\ObjectManager\BasicContent getBasicContentManager
 */
trait BasicContent
{
    /**
     * @Given a/an :path folder exists
     */
    public function createBasicFolder($path)
    {
        $fields = ['name' => $this->getTitleFromPath($path)];

        return $this->getBasicContentManager()->createContentwithPath($path, $fields, 'folder');
    }

    /**
     * @Given a/an :path article exists
     */
    public function createBasicArticle($path)
    {
        $fields = [
            'title' => $this->getTitleFromPath($path),
            'intro' => $this->getDummyXmlText(),
        ];

        return $this->getBasicContentManager()->createContentwithPath($path, $fields, 'article');
    }

    /**
     * @Given a/an :path article draft exists
     */
    public function createArticleDraft($path)
    {
        $fields = [
            'title' => $this->getTitleFromPath($path),
            'intro' => $this->getDummyXmlText(),
        ];

        return $this->getBasicContentManager()->createContentDraft(2, 'article', $fields);
    }

    private function getTitleFromPath($path)
    {
        $parts = explode('/', rtrim($path, '/'));

        return end($parts);
    }

    /**
     * @return string
     */
    private function getDummyXmlText()
    {
        return '<?xml version="1.0" encoding="UTF-8"?><section xmlns="http://docbook.org/ns/docbook" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:ezxhtml="http://ez.no/xmlns/ezpublish/docbook/xhtml" xmlns:ezcustom="http://ez.no/xmlns/ezpublish/docbook/custom" version="5.0-variant ezpublish-1.0"><para>This is a paragraph.</para></section>';
    }
}
