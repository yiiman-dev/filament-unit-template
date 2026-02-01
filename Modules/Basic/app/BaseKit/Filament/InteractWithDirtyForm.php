<?php

namespace Modules\Basic\BaseKit\Filament;

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Modules\Basic\BaseKit\Filament\Components\Forms\TextInput;

trait InteractWithDirtyForm
{
    /**
     * Get the changed fields between original and current data
     *
     * @return array
     * @see \FlowTest\CorporateRegistering\CorporateRegisteringActivationTest::it_shows_changed_fields_in_confirmation_dialog()
     */
    private function getChangedFields(): array
    {
        $changes = [];
        $current_data = $this->data;

        foreach ($current_data as $key => $value) {
            if (isset($this->original_data[$key]) && $this->original_data[$key] !== $value) {
                if (empty($this->original_data[$key]) && empty($value)){
                    continue;
                }

                $changes[$key] = [
                    'old' => $this->original_data[$key],
                    'new' => $value
                ];
            }
        }

        return $changes;
    }
    private function hasDirtyChangeAlert()
    {
        if (!empty($this->getChangedFields())) {
            $this->alert_error('لطفا ابتدا تغییرات خود را ذخیره نمایید٬ یا صفحه را بازنشانی کنید.');
            return true;
        } else {
            return false;
        }
    }


    /**
     * یک فیلد سکشن بازگردانی میکند که حاوی تغییرات اعمال شده در فرم است
     * @return Section
     */
    public function getChangedFiledsSection()
    {
        $changed_fields=$this->getChangedFields();
        return Section::make('تغییرات اعمال شده')
            ->schema(function () use ($changed_fields) {
                $fields = [];
                foreach ($changed_fields as $field => $values) {
                    // Get field label and description
                    $fieldLabel = $this->getFieldLabel($field);
                    $fieldDescription = $this->getFieldDescription($field);

                    // Create section for each field
                    $fields[] = Section::make($fieldLabel)
                        ->description($fieldDescription)
                        ->schema([
                            Grid::make(2)
                                ->schema([
                                    TextInput::make("old_{$field}")
                                        ->label('مقدار قبلی')
                                        ->default($values['old'] ?? '')
                                        ->disabled(),
                                    TextInput::make("new_{$field}")
                                        ->label('مقدار جدید')
                                        ->default($values['new'])
                                        ->disabled(),
                                ])
                        ])
                        ->collapsed(false)
                        ->collapsible();
                }
                return $fields;
            });
    }
}
