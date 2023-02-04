<?php

declare(strict_types=1);

namespace Brick\Browser\Wrapper;

use Brick\Browser\Element;
use Brick\Browser\By;

/**
 * A form.
 *
 * @todo handle file inputs
 * @todo handle html5 form attribute on form elements.
 * @todo when clicking a submit button, its value must be sent
 */
class Form extends AbstractWrapper
{
    /**
     * @param Element $element
     *
     * @return Form
     */
    public static function create(Element $element) : Form
    {
        return new Form($element);
    }

    /**
     * Returns the action attribute of the form.
     *
     * @return string
     */
    public function getAction() : string
    {
        return $this->element->getAttribute('action');
    }

    /**
     * Returns the method of the form, GET or POST.
     *
     * @return string
     */
    public function getMethod() : string
    {
        return $this->isPost() ? 'POST' : 'GET';
    }

    /**
     * @return bool
     */
    public function isGet() : bool
    {
        return ! $this->isPost();
    }

    /**
     * @return bool
     */
    public function isPost() : bool
    {
        return strtolower($this->element->getAttribute('method')) === 'post';
    }

    /**
     * @return string
     */
    public function getRawData() : string
    {
        $values = [];

        foreach ($this->findTextControls() as $textControl) {
            $name = $textControl->getName();
            $value = $textControl->getValue();
            $values[] = rawurlencode($name) . '=' . rawurlencode($value);
        }

        foreach ($this->findToggleButton() as $toggleButton) {
            if ($toggleButton->isChecked()) {
                $name = $toggleButton->getName();
                $value = $toggleButton->getValue();
                $values[] = rawurlencode($name) . '=' . rawurlencode($value);
            }
        }

        foreach ($this->findSelect() as $select) {
            $name = $select->getName();
            foreach ($select->getAllSelectedOptions() as $option) {
                $value = $option->hasAttribute('value') ? $option->getAttribute('value') : $option->getText();
                $values[] = rawurlencode($name) . '=' . rawurlencode($value);
            }
        }

        return implode('&', $values);
    }

    /**
     * Returns named text controls in the form.
     *
     * @return \Brick\Browser\Wrapper\TextControl[]
     */
    private function findTextControls() : array
    {
        $textControls = [];

        $elements = $this->findBySelector('
            textarea[name]:not([disabled]),
            input[type=text][name]:not([disabled]),
            input[type=password][name]:not([disabled]),
            input[type=hidden][name]:not([disabled]),
            input[type=email][name]:not([disabled]),
            input[type=number][name]:not([disabled]),
            input[type=search][name]:not([disabled]),
            input[type=tel][name]:not([disabled]),
            input[type=url][name]:not([disabled])
        ');

        foreach ($elements as $element) {
            $textControls[] = $element->toTextControl();
        }

        return $textControls;
    }

    /**
     * Returns named check boxes & radio buttons in the form.
     *
     * @return \Brick\Browser\Wrapper\ToggleButton[]
     */
    private function findToggleButton() : array
    {
        $toggleButtons = [];

        $elements = $this->findBySelector('
            input[type=checkbox][name]:not([disabled]),
            input[type=radio][name]:not([disabled])
        ');

        foreach ($elements as $element) {
            $toggleButtons[] = $element->toToggleButton();
        }

        return $toggleButtons;
    }

    /**
     * Returns named selects in the form.
     *
     * @return \Brick\Browser\Wrapper\Select[]
     */
    private function findSelect() : array
    {
        $selects = [];
        $elements = $this->findBySelector('select[name]:not([disabled])');

        foreach ($elements as $element) {
            $selects[] = $element->toSelect();
        }

        return $selects;
    }

    /**
     * @param string $selector
     *
     * @return \Brick\Browser\Element[]
     */
    private function findBySelector(string $selector) : array
    {
        return $this->element->find(By::cssSelector($selector))->all();
    }
}
