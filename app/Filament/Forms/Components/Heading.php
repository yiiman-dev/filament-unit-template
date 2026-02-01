<?php

/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/7/25, 1:47 AM
 */

namespace App\Filament\Forms\Components;

use Closure;
use Filament\Forms\Components\Component;

class Heading extends Component
{
    protected mixed $content = null;

    protected string | Closure | null $defaultView = 'filament.forms.components.heading-one';

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

    private function lsk()
    {

    }
    public function one(): static
    {
        $this->view = 'filament.forms.components.heading-one';

        return $this;
    }

    public function two(): static
    {
        $this->view = 'filament.forms.components.heading-two';

        return $this;
    }

    public function three(): static
    {
        $this->view = 'filament.forms.components.heading-three';

        return $this;
    }

    public function content(mixed $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getId(): string
    {
        return parent::getId() ?? $this->getStatePath();
    }

    public function getContent(): mixed
    {
        return $this->evaluate($this->content);
    }
}
