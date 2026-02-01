<?php

namespace Guava\FilamentKnowledgeBase\Actions;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Guava\FilamentKnowledgeBase\Contracts\Documentable;
use Guava\FilamentKnowledgeBase\Facades\KnowledgeBase;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class HelpAction extends Action
{
//    protected function setUp(): void
//    {
//    }

    public function generic(): HelpAction
    {
        return $this->label(__('filament-knowledge-base::translations.help'))
            ->icon('heroicon-o-question-mark-circle')
            ->iconSize('lg')
            ->color('gray')
            ->button();
    }

    public static function forDocumentable(Documentable|string $documentable): HelpAction
    {
        $documentable = KnowledgeBase::documentable($documentable);
        return static::make(uniqid())
            ->modalHeading('ldhelh')
            ->modalDescription('lhjwl')
            ->modalContent(new HtmlString('kjehfkjhwejkh'))
            ->action(function () {
                Notification::make('danger_'.uniqid())
                    ->danger()
                    ->title('danger')
                    ->body('کد تایید اشتباه است.')
                    ->send();
            });
        return static::make("help.{$documentable->getId()}")
            ->label($documentable->getTitle())
            ->icon($documentable->getIcon())
            ->when(
                KnowledgeBase::companion()->hasModalPreviews(),
                fn(HelpAction $action) => $action
                    ->modal()
                    ->modalHeading('lwijdqil')
                    ->modalDescription(new HtmlString('test'))
//                    ->action(fn () => dd('test'))
//                    ->alpineClickHandler('$dispatch(\"open-modal\", {id: "' . $documentable->getId() . '"})')
                    ->when(
                        KnowledgeBase::companion()->hasSlideOverPreviews(),
                        fn(HelpAction $action) => $action->slideOver()
                    ),
                fn(HelpAction $action) => $action->url($documentable->getUrl())
            );
    }
}
