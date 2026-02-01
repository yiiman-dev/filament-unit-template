<?php

namespace Modules\Basic\BaseKit\Filament\Components\Forms;

use Filament\Forms\Components\TextInput as BaseTextInput;
use Modules\Basic\BaseKit\Filament\Concerns\HandleModelLabels;

class TextInput extends BaseTextInput
{
    use HandleModelLabels;


    public function ltr():self
    {
        $this->extraAlpineAttributes(['tabindex' => 1, 'style' => 'text-align:left;direction:ltr']);
        return $this;
    }

    public function rtl():self
    {
        $this->extraAlpineAttributes(['tabindex' => 1, 'style' => 'text-align:right;direction:rtl']);
        return $this;
    }
}
