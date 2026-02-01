<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/7/25, 2:19 AM
 */

namespace App\Filament\Forms\Components;

use Closure;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Concerns\HasColors;
use Filament\Forms\Components\Concerns\HasOptions;

class Button extends Component
{
    use HasColors;
    use HasOptions;
    protected mixed $url=null;

    protected string | Closure | null $defaultView='filament.forms.components.button';


    public function url($url)
    {
        $this->url=$url;
        return $this;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->dehydrated(false);

        $this->columnSpanFull();
    }

    public static function make(): static
    {
        $static = app(static::class);
        $static->configure();

        return $static;
    }


    public function getUrl(): mixed
    {
        return $this->evaluate($this->url);
    }


}
