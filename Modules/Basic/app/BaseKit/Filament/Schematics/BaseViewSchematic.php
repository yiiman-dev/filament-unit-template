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

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Infolists\Components\Entry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Resources\Resource;
use Illuminate\Support\Arr;
use Modules\Basic\BaseKit\Filament;


abstract class BaseViewSchematic extends Schematic implements
    Filament\Schematics\Contracts\FilamentInfoListSchemaContract
{
    use Filament\Schematics\Concerns\InteractWithInfoList;
    use Filament\Schematics\Concerns\SortPatternParser;

    public Infolist $infoList;

    private array $hints = [];

    public ViewRecord $view_resource;

    private array $helper_texts = [];
    private array $sorted_attributes = [];


    public function setViewResource(ViewRecord $view_resource): self
    {
        $this->view_resource = $view_resource;
        return $this;
    }

    public function getViewResource(): ViewRecord
    {
        return $this->view_resource;
    }


    public function __construct(Infolist $infoList = null, $need_init_attributes = true)
    {
        $this->infoList=$infoList;
        $this->initSchema();

        if ($need_init_attributes) {
            $this->hints = $this->attributeHints();
            $this->helper_texts = $this->attributeHelperTexts();
            $this->attributes = array_merge_recursive($this->listAttributes(), $this->attributes);
        }

    }


    private function generateForm(Infolist $infoList): void
    {
        $this->infoList = $infoList->schema($this->infoListSchema());
    }

    /**
     * @param Infolist $infoList
     * @return self
     * @throws \Exception
     */
    public static function makeInfoList(Infolist $infoList,$need_init_attributes = true): self
    {
        return (new static($infoList,$need_init_attributes ));
    }


    public static function makeSchema(): self
    {
        return (new static(null));
    }

    public function returnInfoList(): Infolist
    {
        if (!empty($this->infoList)) {
            $this->generateForm($this->infoList);
        }
        return $this->infoList;
    }


    /**
     * @throws \Exception
     */
    public function returnMappedSchema($sort = 'f'): array
    {
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

    private function isOdd($number): bool
    {
        if ($number % 2 == 1) {
            return true;
        } else {
            return false;
        }
    }


    public function getAttributeHint(string $attribute): string|null
    {
        return Arr::get($this->hints, $attribute, '');
    }

    public function getAttributeHelperText($attribute): string|null
    {
        return Arr::get($this->helper_texts, $attribute, '');
    }


    public function attributeHints(): array
    {
        return [];
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
            $components = $this->infoListSchema();
        }

        foreach ($components as $component) {
            /**
             * @var $component
             */
            if (is_subclass_of($component, Entry::class)) {
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


    public function attributeHelperTexts(): array
    {
        return [];
    }



    public function attributeLabels(): array
    {
        return [];
    }

    public function invisibleAttributes(): array
    {
        return [];
    }

    public function disableAttributes(): array
    {
        return [];
    }


}
