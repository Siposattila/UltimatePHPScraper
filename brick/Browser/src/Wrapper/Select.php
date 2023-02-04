<?php

declare(strict_types=1);

namespace Brick\Browser\Wrapper;

use Brick\Browser\Element;
use Brick\Browser\By;
use Brick\Browser\Exception\NoSuchElementException;
use Brick\Browser\Exception\UnexpectedElementException;

/**
 * A select element.
 */
class Select extends FormControl
{
    /**
     * @param Element $element
     *
     * @return Select
     *
     * @throws UnexpectedElementException
     */
    public static function create(Element $element) : Select
    {
        if (! $element->is('select')) {
            throw new UnexpectedElementException('Expected select element, got ' . $element->getTagName());
        }

        return new Select($element);
    }

    /**
     * @return bool
     */
    public function isMultiple() : bool
    {
        return $this->element->hasAttribute('multiple');
    }

    /**
     * Selects the option at the given index.
     *
     * @param int $index
     *
     * @return void
     */
    public function selectByIndex(int $index) : void
    {
        $option = $this->findByIndex($index);
        if ($option) {
            $this->selectOption($option);
        }
    }

    /**
     * Selects all options that have a value matching the argument.
     *
     * @param string $value
     *
     * @return void
     */
    public function selectByValue(string $value) : void
    {
        foreach ($this->findByValue($value) as $option) {
            $this->selectOption($option);
        }
    }

    /**
     * Select all options that display text matching the argument.
     *
     * @param string $text
     *
     * @return void
     */
    public function selectByVisibleText(string $text) : void
    {
        foreach ($this->findByVisibleText($text) as $option) {
            $this->selectOption($option);
        }
    }

    /**
     * Deselects the option at the given index.
     *
     * @param int $index
     *
     * @return void
     */
    public function deselectByIndex(int $index) : void
    {
        $option = $this->findByIndex($index);
        if ($option) {
            $this->deselectOption($option);
        }
    }

    /**
     * Deselects all options that have a value matching the argument.
     *
     * @param string $value
     *
     * @return void
     */
    public function deselectByValue(string $value) : void
    {
        foreach ($this->findByValue($value) as $option) {
            $this->deselectOption($option);
        }
    }

    /**
     * Deselects all options that display text matching the argument.
     *
     * @param string $text
     *
     * @return void
     */
    public function deselectByVisibleText(string $text) : void
    {
        foreach ($this->findByVisibleText($text) as $option) {
            $this->deselectOption($option);
        }
    }

    /**
     * Deselects all the options.
     *
     * @return void
     */
    public function deselectAll() : void
    {
        foreach ($this->getOptions() as $option) {
            $this->deselectOption($option);
        }
    }

    /**
     * @return int
     */
    private function getDisplaySize() : int
    {
        $size = $this->element->getAttribute('size');

        return ctype_digit($size) ? (int) $size : 1;
    }

    /**
     * Returns all the selected options belonging to this select tag.
     *
     * @return Element[]
     */
    public function getAllSelectedOptions() : array
    {
        $options = $this->filterOptions(static function(Element $option) {
            return $option->hasAttribute('selected') && ! $option->hasAttribute('disabled');
        });

        if (count($options) !== 0) {
            return $options;
        }

        /**
         * If the multiple attribute is absent and the element's display size is 1,
         * then whenever there are no option elements in the select element's list of options
         * that have their selectedness set to true, the user agent must set the selectedness
         * of the first option element in the list of options in tree order that is not disabled,
         * if any, to true.
         */
        if (! $this->element->hasAttribute('multiple') && $this->getDisplaySize() === 1) {
            $options = $this->filterOptions(static function(Element $option) {
                return ! $option->hasAttribute('disabled');
            });

            if (count($options) !== 0) {
                return array_slice($options, 0, 1);
            }
        }

        return [];
    }

    /**
     * Returns the first selected option in this select tag.
     *
     * @return Element
     *
     * @throws \Brick\Browser\Exception\NoSuchElementException
     */
    public function getFirstSelectedOption() : Element
    {
        $options = $this->getAllSelectedOptions();

        if (count($options) === 0) {
            throw new NoSuchElementException();
        }

        return $options[0];
    }

    /**
     * Returns the option at the given index, or null if the index does not match an option.
     *
     * @param int $index
     *
     * @return Element|null
     */
    private function findByIndex(int $index) : ?Element
    {
        $options = $this->getOptions();

        return isset($options[$index]) ? $options[$index] : null;
    }

    /**
     * Returns all options that have a value matching the argument.
     *
     * @param string $value
     *
     * @return Element[]
     */
    private function findByValue(string $value) : array
    {
        return $this->filterOptions(static function(Element $option) use ($value) {
            return $option->getAttribute('value') === $value;
        });
    }

    /**
     * Returns all options that display text matching the argument.
     *
     * @param string $text
     *
     * @return Element[]
     */
    private function findByVisibleText(string $text) : array
    {
        return $this->filterOptions(static function(Element $option) use ($text) {
            return $option->getText() === $text;
        });
    }

    /**
     * Returns all the options inside this select.
     *
     * @return Element[]
     */
    public function getOptions() : array
    {
        return $this->element->find(By::tagName('option'))->all();
    }

    /**
     * Returns all the options inside this select that match the given filter.
     *
     * @param \Closure $filter
     *
     * @return Element[]
     */
    private function filterOptions(\Closure $filter) : array
    {
        return array_values(array_filter($this->getOptions(), $filter));
    }

    /**
     * @param Element $element
     *
     * @return void
     */
    private function selectOption(Element $element) : void
    {
        if (! $this->isMultiple()) {
            foreach ($this->getOptions() as $option) {
                $this->deselectOption($option);
            }
        }

        $element->setAttribute('selected', 'selected');
    }

    /**
     * @param Element $element
     *
     * @return void
     */
    private function deselectOption(Element $element) : void
    {
        $element->removeAttribute('selected');
    }
}
