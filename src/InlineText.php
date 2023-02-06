<?php

namespace Outl1ne\NovaInlineTextField;

use Laravel\Nova\Fields\Text;

class InlineText extends Text
{
    public $component = 'inline-text-field';

    protected array $styles = [];

    protected bool $isEditable = true;

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
            'editable' => $this->isEditable
        ]);

        return parent::jsonSerialize();
    }

}
