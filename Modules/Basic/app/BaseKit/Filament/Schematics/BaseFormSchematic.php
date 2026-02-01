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

use Filament\Forms\Components\Actions\Concerns\BelongsToComponent;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Concerns\BelongsToLivewire;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ViewRecord;
use Filament\Resources\Resource;
use Illuminate\Support\Arr;
use Modules\Basic\BaseKit\Filament;
use Modules\Basic\BaseKit\Filament\Schematics\Concerns\InteractWithForm;

/**
 * @property BaseFormSchematic $remoteClass
 */
abstract class BaseFormSchematic extends Schematic implements Filament\Schematics\Contracts\FilamentFormSchemaContract
{
    use InteractWithForm;
    use Filament\Schematics\Concerns\SortPatternParser;


    public Form $editForm;
    public Form $createForm;
    public Form $commonForm;
    private array $hints = [];
    private array $placeholders = [];
    private array $defaults = [];
    protected EditRecord $edit_resource;
    protected ViewRecord $view_resource;
    protected CreateRecord $create_resource;
    protected Resource $common_resource;

    private array $helper_texts = [];
    private array $sorted_attributes = [];
    private string $_scenario;

    public function setEditResource(EditRecord $edit_resource): self
    {
        $this->edit_resource = $edit_resource;
        return $this;
    }
    public function setViewResource(ViewRecord $view_resource): self
    {
        $this->view_resource = $view_resource;
        return $this;
    }

    public function setCreateResource(CreateRecord $create_resource): self
    {
        $this->create_resource = $create_resource;
        return $this;
    }

    public function setCommonResource(Resource $common_resource): self
    {
        $this->common_resource = $common_resource;
        return $this;
    }

    public function getCommonResource(): Resource
    {
        if (empty($this->common_resource) && !empty($this->remoteClass)) {
            $this->common_resource = $this->remoteClass->getCommonResource();
        }
        return $this->common_resource;
    }

    public function getCreateResource(): CreateRecord
    {
        if (empty($this->create_resource) && !empty($this->remoteClass)) {
            $this->create_resource = $this->remoteClass->getCreateResource();
        }
        return $this->create_resource;
    }

    public function getEditResource(): EditRecord
    {
        if (empty($this->edit_resource) && !empty($this->remoteClass)) {
            $this->edit_resource = $this->remoteClass->getEditResource();
        }
        return $this->edit_resource;
    }

    public function getViewResource(): ViewRecord
    {
        if (empty($this->view_resource) && !empty($this->remoteClass)) {
            $this->view_resource = $this->remoteClass->getViewResource();
        }
        
        return $this->view_resource;
    }


    public function __construct(Form $form = null, string $scenario = 'common', $need_init_attributes = true)
    {
        $this->initSchema();

        if ($need_init_attributes) {
            $this->defaults = $this->attributeDefaults();
            $this->placeholders = $this->attributePlaceholders();
            $this->hints = $this->attributeHints();
            $this->helper_texts = $this->attributeHelperTexts();
            $this->attributes = array_merge_recursive($this->listAttributes(), $this->attributes);
        }
        if (!empty($form)) {
            $this->editForm = $form;
            $this->commonForm = $form;
            $this->createForm = $form;
            $this->_scenario = $scenario;
        }
    }


    private function generateForm(): void
    {
        if (empty($this->commonForm) && empty($this->createForm) && empty($this->editForm)) {
            return;
        }
        switch ($this->_scenario) {
            case 'common':
                $this->commonForm = $this->commonForm->schema($this->commonFormSchema());
                break;
            case 'edit':
                $this->editForm = $this->editForm->schema($this->editFormSchema());
                break;
            case 'create':
                $this->createForm = $this->createForm->schema($this->createFormSchema());
                break;
        }
    }

    /**
     * @param Form $form
     * @param string $scenario common/edit/create
     * @return self
     * @throws \Exception
     */
    public static function makeForm(Form $form, string $scenario = 'common'): self
    {
        static::checkScenarioException($scenario);
        return (new static($form, $scenario, false));
    }

    private static function checkScenarioException($scenario): void
    {
        $scenario = str($scenario)->trim()->lower()->toString();
        if (!Arr::exists(['common' => 0, 'edit' => 0, 'create' => 0], $scenario)) {
            throw new \Exception(
                'makeForm() argument should be one of this values [common,edit,create],  ' . $scenario . ' received'
            );
        }
    }

    public static function makeSchema($scenario = 'common'): self
    {
        return (new static(null, $scenario, false));
    }

    public function returnCommonForm(): Form
    {
        $this->generateForm();
        return $this->commonForm;
    }


    /**
     * @throws \Exception
     */
    public function returnMappedSchema($sort = 'f', $scenario = 'common'): array
    {
        static::checkScenarioException($scenario);
        $this->attributes = $this->listAttributes();
        if (!empty($this->remoteClass)) {
            /**
             * No need to mapping because, mapping called from remote class
             */
            if (!empty($this->remoteClass->remoteClass)) {
                return $this->attributes;
            }
        }


        if ($sort == 'f') {
            return $this->attributes;
        }
        return $this->map($sort);
    }
//    private function map(array|string $pattern):array
//    {
//        switch ($pattern){
//            case 'f':
//                return $this->attributes;
//            case 'c':
//                return [Card::make()->schema($this->attributes)];
//            case 's':
//                return [Section::make()->schema($this->attributes)];
//            default:
//                $pattern=str($pattern);
//                if ($pattern->length()==3 && $pattern->before('.')->is('g') && $pattern->after('.')->toInteger()){
//                    return [Grid::make($pattern->after('.')->toInteger())->schema($this->attributes)];
//                }
//                if ($pattern->length())
//        }
//
//    }


    public function returnCommonSchema()
    {
        return $this->commonFormSchema();
    }

    private function isOdd($number): bool
    {
        if ($number % 2 == 1) {
            return true;
        } else {
            return false;
        }
    }

    public function returnEditForm(): Form
    {
        $this->generateForm();
        if (!empty($this->editForm)) {
            return $this->editForm;
        }
        return $this->returnCommonForm();
    }

    public function returnCreateForm(): Form
    {
        $this->generateForm();
        if (!empty($this->createForm)) {
            return $this->createForm;
        }
        return $this->returnCommonForm();
    }

    public function getAttributeHint(string $attribute): string|null
    {
        return Arr::get($this->hints, $attribute, '');
    }

    public function getAttributeDefault(string $attribute, $default = null): mixed
    {
        return Arr::get($this->defaults, $attribute, $default);
    }

    public function getAttributePlaceholder($attribute): string|null
    {
        return Arr::get($this->placeholders, $attribute, '');
    }

    public function getAttributeHelperText($attribute): string|null
    {
        return Arr::get($this->helper_texts, $attribute, '');
    }

    public function attributeDefaults(): array
    {
        return [];
    }

    public function attributeHints(): array
    {
        return [];
    }

    public function disableAllAttributes($save = false): self
    {
        $this->attributes = array_merge_recursive($this->listAttributes(), $this->attributes);
        foreach ($this->attributes as $attribute => $component) {
            $this->disableAttribute($attribute, $save);
        }
        return $this;
    }

    public function invisibleAllAttributes(): self
    {
        foreach ($this->attributes as $attribute => $component) {
            $this->invisibleAttribute($attribute);
        }
        return $this;
    }


    private function getComponentChildren($component)
    {
        /**
         * @var Grid|Section $component
         */
        $reflect = new \ReflectionClass($component);
        if ($reflect->hasMethod('getChildComponents')) {
            return $component->getChildComponents();
        }
        return null;
    }

    private function listAttributes($components = null): array
    {
        $attributes = [];
        if (empty($components)) {
            $components = $this->commonFormSchema();
        }

        foreach ($components as $component) {
            /**
             * @var $component
             */
            if (is_subclass_of($component, Field::class)) {
                $attributes[$component->getName()] = $component;
            } else {
                $attributes = array_merge_recursive(
                    $attributes,
                    $this->listAttributes($this->getComponentChildren($component))
                );
            }
        }
        return $attributes;
    }

    public function attributePlaceholders(): array
    {
        return [];
    }

    public function attributeHelperTexts(): array
    {
        return [];
    }

    public function disableAttributes(): array
    {
        return [];
    }

    public function editFormSchema(): array|null
    {
        return [];
    }

    public function createFormSchema(): array|null
    {
        return [];
    }

    public function invisibleAttributes(): array
    {
        return [];
    }

    public function commonFormSchema(): array
    {
        return [];
    }

    public function attributeLabels(): array
    {
        return [];
    }

}
