<?php

namespace EightyNine\Approvals\Tables\Columns;

use Closure;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\ViewField;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\Column;

class ApprovalStatusColumn extends Column
{
    protected string $view = 'filament-approvals::tables.columns.approval-status-column';

    protected function setUp(): void
    {
        parent::setUp();
        $this->action(
            ViewAction::make('Approval History')
                ->modalHeading(__('filament-approvals::approvals.actions.approval_history'))
                ->slideOver()
                ->form(function($record) {
                    $data = $record->approvals()->orderBy('created_at', 'desc')->get();
                    return [
                        ViewField::make('Approval History')
                            ->hiddenLabel()
                            ->view('filament-approvals::tables.columns.approval-status-column-action-view', ['data' => $data, 'record' => $record])
                    ];
                })
        );
    }
}
