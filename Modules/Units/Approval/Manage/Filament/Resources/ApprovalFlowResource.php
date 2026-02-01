<?php

namespace Units\Approval\Manage\Filament\Resources;

use Guava\FilamentKnowledgeBase\Contracts\HasKnowledgeBase;
use Units\Approval\Manage\Docs\ApprovalConceptDoc;
use Units\Approval\Manage\Filament\Resources\ApprovalFlowResource\Pages;
use Units\Approval\Manage\Filament\Resources\ApprovalFlowResource\RelationManagers\StepsRelationManager;
use Units\Approval\Manage\Models\ProcessApprovalFlow;
use Units\Approval\Manage\Services\ModelScannerService;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;


class ApprovalFlowResource extends Resource implements HasKnowledgeBase
{
    protected static ?string $model = ProcessApprovalFlow::class;

    protected static ?string $modelLabel = 'جریان کاری';

    protected static ?string $pluralModelLabel = 'جریان های کاری';


    public static function getNavigationIcon(): ?string
    {
        return  config('approvals.navigation.icon', 'heroicon-o-clipboard-document-check');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return config('approvals.navigation.should_register_navigation', true);
    }

    public static function getLabel(): string
    {
        return __('filament-approvals::approvals.navigation.label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament-approvals::approvals.navigation.group');
    }

    public static function getNavigationSort(): ?int
    {
        return config('approvals.navigation.sort', 1);
    }

    public static function getPluralLabel(): string
    {
        return __('filament-approvals::approvals.navigation.plural_label');
    }
    public static function form(Form $form): Form
    {
        $models = (new ModelScannerService())->getApprovableModels();

        return $form
            ->columns(12)
            ->schema([
                TextInput::make("name")
                    ->columnSpan(fn($context) => $context === 'create' ? 12 : 6)
                    ->label('نام جریان کاری')
                    ->required(),
                Select::make('approvable_type')
                    ->label('نوع جریان کاری')
                    ->columnSpan(fn($context) => $context === 'create' ? 12 : 6)
                    ->options(function() use ($models) {
                        // remove 'App\Models\' from the value of models
                        $models = array_map(function($model) {
                            return $model::flowLabel();
                        }, $models);
                        return $models;
                    })
                    ->required(),
                Forms\Components\Placeholder::make('warning')
                    ->visible(fn() => empty($models))
                    ->columnSpanFull()
                    ->content(new HtmlString('سیستم مدل های مربوط به جریان کار را در یونیت های کارگزار پیدا نکرد!! <br> لطفا مجددا امتحان کنید و اگر خطا دوباره ظاهر شد٬ به پشتیبانی اطلاع دهید'))
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make("name")->label('نام جریان کاری'),
                TextColumn::make("approvable_type")
                    ->formatStateUsing(function ($state) {
                        return $state::flowLabel();
                    })
                    ->label('نوع اقدام'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            StepsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApprovalFlows::route('/'),
//            'create' => Pages\CreateApprovalFlow::route('/create'),
            'edit' => Pages\EditApprovalFlow::route('/{record}/edit'),
        ];
    }

    public static function getDocumentation(): array|string
    {
        return [
            ApprovalConceptDoc::make()
        ];
    }
}
