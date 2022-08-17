<?php

namespace Outl1ne\NovaInlineTextField;

use Laravel\Nova\Fields\Text;

class InlineText extends Text
{
    public $component = 'inline-text-field';

    protected array $styles = [];

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

    public function jsonSerialize(): array
    {
        $this->withMeta(['style' => join(';', $this->styles)]);
        return parent::jsonSerialize();
    }

}
