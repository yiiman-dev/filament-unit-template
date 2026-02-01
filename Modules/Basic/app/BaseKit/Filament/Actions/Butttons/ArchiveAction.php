<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          8/13/25, 12:28 AM
 */

namespace Modules\Basic\BaseKit\Filament\Actions\Butttons;

use Closure;
use Enums\ProjectIconsEnum;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Support\Colors\Color;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;

class ArchiveAction extends Action
{

    public static function getDefaultName(): ?string
    {
        return 'archive';
    }
    private string $resource;

    public function setResource(string $resource):self
    {
        $this->resource=$resource;
        return $this;
    }

    public function setUrl(): self
    {
        $resource = $this->resource;
        $this->url(fn ($record) => $resource::getUrl('archive', ['record' => $record]));
        return $this;
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->color(Color::Red);
        $this->tooltip('آرشیو');

        $this->label(false);

        $this->icon(ProjectIconsEnum::ARCHIVE->value);
    }

}
