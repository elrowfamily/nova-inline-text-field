<?php

namespace Outl1ne\NovaInlineTextField;

use Laravel\Nova\Fields\Text;

class InlineText extends Text
{
    public $component = 'inline-text-field';

    protected array $styles = [];

    protected bool $isEditable = true;
    protected bool $isViewable = true;

    protected function resolveAttribute($resource, $attribute)
    {
        $this->withMeta(['resourceId' => $resource->getKey()]);
        return parent::resolveAttribute($resource, $attribute);
    }

    public function addCss(string $cssRule): InlineText
    {

        $this->styles[] = $cssRule;
        return $this;
    }

    /**
     * Check if field should show the value at index
     * @param callable|bool $flag
     * @return $this
     */
    public function viewable(callable|bool $flag = true): static
    {
        $this->isViewable = is_callable($flag) ? $flag() : $flag;
        return $this;
    }

    /**
     * Check if field should be editable at index
     * @param callable|bool $flag
     * @return $this
     */
    public function editable(callable|bool $flag = true): static
    {
        $this->isEditable = is_callable($flag) ? $flag() : $flag;
        return $this;
    }

    public function jsonSerialize(): array
    {
        $this->withMeta([
            'style' => join(';', $this->styles),
            'editable' => $this->isEditable,
            'viewable' => $this->isViewable
        ]);

        return parent::jsonSerialize();
    }

}
