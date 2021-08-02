<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Element;

use Ibexa\Behat\Browser\Assert\ElementAssert;

interface ElementInterface extends BaseElementInterface
{
    public function isVisible(): bool;

    public function getText(): string;

    public function setValue($value): void;

    public function clear(): void;

    public function click(): void;

    public function hasAttribute(string $attribute): bool;

    public function getAttribute(string $attribute): string;

    public function hasClass(string $class): bool;

    public function mouseOver(): void;

    public function getValue();

    public function attachFile($filePath): void;

    public function getOuterHtml(): string;

    public function assert(): ElementAssert;

    public function isValid(): bool;

    public function selectOption(string $option): void;

    public function getXPath(): string;
}
