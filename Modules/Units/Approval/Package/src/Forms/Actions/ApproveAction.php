<?php

namespace EightyNine\Approvals\Forms\Actions;

use Closure;
use Filament\Forms\Components\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ApproveAction extends Action
{

    public static function getDefaultName(): ?string
    {
        return 'Approve';
    }


    /**
     * @throws \Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->color('primary')
            ->action('Approve')
            ->icon('heroicon-m-check')
            ->label(__('filament-approvals::approvals.actions.approve'))
            ->visible(
                function (Model $record) {
                    $canApprove = $record->canBeApprovedBy(Auth::user());
                    $isSubmitted = $record->isSubmitted();
                    $isApprovalComplete = !$record->isApprovalCompleted();
                    $isDiscadrd = !$record->isDiscarded();
                    return $canApprove &&
                        $isSubmitted &&
                        $isApprovalComplete &&
                        $isDiscadrd;
                }
            )
            ->requiresConfirmation()
            ->modalDescription(__('filament-approvals::approvals.actions.approve_confirmation_text'));
    }


    public function action(Closure|string|null $action): static
    {
        if ($action !== 'Approve') {
            throw new \Exception('You\'re unable to override the action for this plugin');
        }

        $this->action = $this->approveModel();

        return $this;
    }


    /**
     * Approve data function.
     *
     */
    private function approveModel(): Closure
    {
        return function (array $data, Model $record): bool {
            $record->approve(comment: null, user: Auth::user());
            Notification::make()
                ->title('تایید با موفقیت انجام شد')
                ->success()
                ->send();
            return true;
        };
    }
}
