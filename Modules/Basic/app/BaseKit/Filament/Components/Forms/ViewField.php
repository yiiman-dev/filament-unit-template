<?php

namespace Modules\Basic\BaseKit\Filament\Components\Forms;

use \Filament\Forms\Components\ViewField as BaseViewField;
use Illuminate\Contracts\Support\Htmlable;
use Modules\Basic\BaseKit\Filament\Concerns\HandleModelLabels;

class ViewField extends BaseViewField
{
    use HandleModelLabels;
    private $content;
    public static function make(?string $name = null): static
    {
        $component = parent::make($name);

        if ($name) {
            $component->applyModelLabels($name);
        }

        return $component;
    }

    public function ltr():self
    {
        $this->extraAttributes(['tabindex' => 1, 'style' => 'text-align:left;direction:ltr']);
        return $this;
    }

    public function content(string | Htmlable | \Closure | null $content):self
    {
        $this->content=$content;
        return $this;
    }

    public function getContent()
    {
        return $this->evaluate($this->content);
    }

    public function rtl():self
    {
        $this->extraAttributes(['tabindex' => 1, 'style' => 'text-align:right;direction:rtl']);
        return $this;
    }
}
