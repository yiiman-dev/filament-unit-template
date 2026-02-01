<?php

namespace EightyNine\Approvals\Forms;

use EightyNine\Approvals\Forms\Actions\ApproveAction;
use EightyNine\Approvals\Forms\Actions\DiscardAction;
use EightyNine\Approvals\Forms\Actions\RejectAction;
use EightyNine\Approvals\Forms\Actions\SubmitAction;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Actions;
use Filament\Support\Enums\ActionSize;
use MongoDB\Laravel\Eloquent\Model;

class ApprovalActions
{
    public static function make(Actions\Action|array $action, $alwaysVisibleActions = []): Actions
    {

        $actions = [];

        if(is_array($action)) {
            foreach($action as $a) {
                $actions[] = $a->visible(fn (Model $record) => $record->isApprovalCompleted());
            }
        } else {
            $actions[] = $action->visible(fn (Model $record) => $record->isApprovalCompleted());
        }

        return Actions::make(array_merge($actions,[
            SubmitAction::make(),
            ApproveAction::make(),
            DiscardAction::make(),
            RejectAction::make(),
        ]));
//            ->label(__('filament-approvals::approvals.actions.approvals'))
//            ->icon('heroicon-m-ellipsis-vertical')
//            ->size(ActionSize::Small)
//            ->color('primary')
//            ->button();
    }
}
