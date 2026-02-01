<?php

namespace Modules\Basic\BaseKit;
use App\Models\Forms\LogflowForm;

class LogflowService extends BaseService
{
    public function storeComment(array $requestBody, string $commentType = 'user', ?string $authorIdentity = null): void
    {
        $logflowModel = new LogflowForm();

        if (!empty($requestBody['LogFlow']) && $logflowModel->fill($requestBody)) {
            $model = $requestBody['LogFlow']['model']::find($requestBody['LogFlow']['id']);

            $isCommentSaved = $model->addComment(
                [
                    'operationStatus' => $logflowModel->operationStatus,
                    'department' => $logflowModel->department,
                    'commentType' => $commentType,
                    'comment' => $logflowModel->comment,
                ],
                true,
                $authorIdentity
            );

            if ($isCommentSaved) {
                $this->setSuccessResponse(message: __('LogflowForm.commentCreated'));
            } else {
                $this->addError(message: __('LogflowForm.commentNotCreated'));
            }
        }
    }

    public function storeSystemComment($model, string $comment, ?string $authorIdentity = null): void
    {
        $this->storeComment(
            [
                'LogFlow' => ['model' => get_class($model), 'id' => $model->_id],
                'LogflowForm' => [
                    'comment' => $comment
                ],
            ],
            'system',
            $authorIdentity
        );
    }
}



