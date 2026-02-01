<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          8/4/25, 2:57 PM
 */

namespace Modules\Basic\BaseKit\Filament\Schematics;

use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Forms\Form;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Table;
use Modules\Basic\BaseKit\Filament;
use Modules\Basic\BaseKit\Filament\Components\Table\TextColumn;
use Modules\Basic\BaseKit\Filament\Form\Components\MoneyColumn;
use Modules\Basic\BaseKit\Filament\Schematics\Concerns\InteractWithTable;
abstract class BaseTableSchematic extends Schematic implements Filament\Schematics\Contracts\FilamentTableSchemaContract
{
    use InteractWithTable;
    protected Table $table;

    public function __construct(Table $table)
    {
        $this->initSchema();
        $this->table = $this->tableSchema($table);
    }


    public function returnTable(): Table
    {
        return $this->table
            ->actions($this->getActions())
            ->bulkActions($this->getBulkActions());
    }

    public function getBulkActions():BulkAction|array
    {
        return [];
    }

    public static function makeTable(Table $table): self
    {
        return (new static($table));
    }

    abstract public function tableSchema(Table $table): Table;

    public function editAction():EditAction
    {
        return EditAction::make();
    }

    public function deleteAction():DeleteAction
    {
        return DeleteAction::make();
    }

    public function viewAction():ViewAction
    {
        return ViewAction::make();
    }

    public function disableAttributes(): array
    {
        return  [];
    }

    public function invisibleAttributes(): array
    {
        return [];
    }
}
