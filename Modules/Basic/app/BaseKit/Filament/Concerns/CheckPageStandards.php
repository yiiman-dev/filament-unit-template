<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/11/25, 6:44 PM
 */

namespace Modules\Basic\BaseKit\Filament\Concerns;

trait CheckPageStandards
{
    public function checkDevelopentStandards()
    {
        if (app()->hasDebugModeEnabled()){
            $this->checkClassCommentStandards();
        }
    }

    protected function checkClassCommentStandards()
    {
        // Get the child class name
        $childClass = get_called_class();

        // Use Reflection to inspect the child class
        $reflection = new \ReflectionClass($childClass);

        // Get the doc comment (if any) from the child class
        $docComment = $reflection->getDocComment();

        // Strings to check for in the doc comment
        $requiredStrings = ['@url https://www.figma.com'];

        // If no doc comment exists, raise an exception
        if (!$docComment) {
            throw new \Exception("The child class '$childClass' must have a doc comment.");
        }

        // Check if all required strings are present in the doc comment
        foreach ($requiredStrings as $string) {
            if (strpos($docComment, $string) === false) {
                throw new \Exception(
                    "The child class '$childClass' is missing the required string '$string' in its doc comment."
                );
            }
        }
    }
}
