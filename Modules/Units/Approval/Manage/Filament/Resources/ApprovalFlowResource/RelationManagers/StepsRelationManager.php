<?php

namespace Units\Approval\Manage\Filament\Resources\ApprovalFlowResource\RelationManagers;

use App\Models\Permission;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Units\Approval\Manage\Models\ProcessApprovalFlow;
use Units\Approval\Manage\Models\ProcessApprovalFlowStep;
use Units\Shield\Manage\Models\Role;

class StepsRelationManager extends RelationManager
{
    protected static string $relationship = 'steps';
    protected static ?string $label = 'مراحل';
    protected static ?string $modelLabel = 'مرحله';
    protected static ?string $pluralLabel = 'مراحل';
    protected static ?string $pluralModelLabel = 'مراحل';

    public function form(Form $form): Form
    {
        return $form
            ->columns(12)
            ->schema([
                Select::make('panel')
                    ->options(function () {
                        return config('filament.panels');
                    })
                    ->live()
                    ->columnSpan(4)
                    ->label('پرسونا'),
                Select::make("role_id")
                    ->visible(function ($get) {
                        switch (true) {
                            case empty($get('panel')):
                                return false;
                            case $get('panel') == 'manage':
                                return true;
                            default:
                                return false;
                        }
                    })
                    ->live()
                    ->searchable()
                    ->label("نقش کاربری")
                    ->helperText("چه نقش کاربری باید این مرحله را تایید کند؟")
                    ->options(fn() => Role::get()
                        ->map(fn($model) => [
                            "name" => str($model->name)
                                ->replace("_", " ")
                                ->title()
                                ->toString(),
                            "id" => $model->id
                        ])->pluck("name", "id"))
                    ->columnSpan(6)
                    ->native(false),
                Select::make("action")
                    ->helperText("چه اتفاقی باید در این مرحله رخ دهد؟")
                    ->native(false)
                    ->label('اقدام')
                    ->default("APPROVE")
                    ->columnSpan(4)
                    ->live()
                    ->suffixAction(function ($state) {
                        if (empty($state)){
                            return null;
                        }
                        if (!isset($this->ownerRecord->approvable_type::flowActionDescriptions()[$state])){
                            return null;
                        }
                        return Forms\Components\Actions\Action::make('action-help')
                            ->modal(true)
                            ->modalSubmitAction(false)
                            ->modalCancelActionLabel('متوجه شدم')
                            ->modalHeading(new HtmlString('<div class="flex gap-1">'.'توضیحات در مورد اقداماتی که در زمان  <div class="w-auto fi-badge flex items-center justify-center rounded-md font-medium ring-1 ring-inset px-2  py-1 fi-color-green bg-green-50 text-green-600 ring-green-600/10 dark:bg-green-400/10 dark:text-green-400 dark:ring-green-400/30 fi-color-primary">'.$this->ownerRecord->approvable_type::flowActions()[$state].'</div> رخ می دهد:'.'</div>'))
                            ->modalContent(new HtmlString($this->ownerRecord->approvable_type::flowActionDescriptions()[$state]))
                            ->icon('heroicon-s-information-circle')
                            ->color('info');
                    })
                    ->options(fn()=> $this->ownerRecord->approvable_type::flowActions()),
                TextInput::make('order')
                    ->label('ترتیب')
                    ->type('number')
                    ->columnSpan(2)
                    ->default(fn($livewire) => $livewire->ownerRecord->steps->count() + 1)
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->reorderable("order")
            ->defaultSort('order', 'asc')
            ->columns([
                Tables\Columns\TextColumn::make('order')
                    ->label('ترتیب'),
                Tables\Columns\TextColumn::make('panel')
                    ->formatStateUsing(function ($state) {
                        return config('filament.panels')[$state];
                    })
                    ->label('پرسونا'),
                Tables\Columns\TextColumn::make('role_id')
                    ->formatStateUsing(function ($record) {
                        return Role::where(['id' => $record->role_id])?->get('name')?->first()?->name;
                    })
                    ->label('نقش کاربر'),
                Tables\Columns\TextColumn::make('action')
                    ->label('اقدام')
                    ->formatStateUsing(function ($state) {
                        return __('filament-approvals::approvals.actions.' . Str($state)->lower()->toString());
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->icon('heroicon-s-plus')
                    ->label(__('filament-approvals::approvals.actions.add_step')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
