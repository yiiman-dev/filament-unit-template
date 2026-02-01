<?php

namespace EightyNine\Approvals\Actions;

use Closure;


use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class SubmitAction extends Action
{

    public static function getDefaultName(): ?string
    {
        return 'Submit';
    }


    protected function setUp(): void
    {
        parent::setUp();

        $this->color('primary')
            ->action('Submit')
            ->label(__('filament-approvals::approvals.actions.submit'))
            ->icon('heroicon-m-check')
            ->visible()
            ->requiresConfirmation()
            ->modalDescription(__('filament-approvals::approvals.actions.submit_confirmation_text'));
    }


    public function action(Closure | string | null $action): static
    {
        if ($action !== 'Submit') {
            throw new \Exception('You\'re unable to override the action for this plugin');
        }

        $this->action = $this->submitModel();

        return $this;
    }


    /**
     * Submit data function.
     *
     */
    private function submitModel(): Closure
    {
        return function ($record): bool {
            $record->submit(Auth::user());
            Notification::make()
                ->title('با موفقیت ثبت شد')
                ->success()
                ->send();
            return true;
        };
    }
}
