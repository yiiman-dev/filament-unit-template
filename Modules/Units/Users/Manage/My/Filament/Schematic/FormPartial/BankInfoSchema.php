<?php

namespace Units\Users\Manage\My\Filament\Schematic\FormPartial;

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Modules\Basic\BaseKit\Filament\Schematics\BaseFormSchematic;
use Units\Auth\My\Models\UserMetadata;
use Units\Auth\My\Models\UserModel;

class BankInfoSchema extends BaseFormSchematic
{
    public function commonFormSchema(): array
    {
        return [
            Section::make('اطلاعات حساب بانکی')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            $this->shebaTextInput('bank_account_sheba')
                                ->formatStateUsing(function (?UserModel $record) {
                                    if (empty($record)) {
                                        return null;
                                    }
                                    $profile = UserMetadata::getProfile($record->national_code);
                                    if (!empty($profile['bank_account_sheba'])) {
                                        return $profile['bank_account_sheba'];
                                    }
                                    return null;
                                })
                                ->visible(),
                            $this->paymentCardTextInput('bank_account_payment_card_no')
                            ->visible()
                                ->formatStateUsing(function (?UserModel $record) {
                                    if (empty($record)) {
                                        return null;
                                    }
                                    $profile = UserMetadata::getProfile($record->national_code);
                                    if (!empty($profile['bank_account_payment_card_no'])) {
                                        return $profile['bank_account_payment_card_no'];
                                    }
                                    return null;
                                })
                        ]),
                ])
        ];
    }
}
