<div dir="rtl" class="chat-container" style="direction: rtl;">
    <meta charset="UTF-8">
    <!-- Chat Messages -->
    <div class="chat-messages" style="max-height: 400px; overflow-y: auto; margin-bottom: 16px;">
        @forelse($messages as $message)
            @php
                $action='';
                $isSender = $message['sender_id'] == $senderId;
                $cssClass = $isSender ? 'message-sender rtl' : 'message-receiver float-left rtl';
                switch (true){
                    case isset($message['meta']['action']) && $message['meta']['action']=='approve':
                        $cssClass .= ' approve';
                        $action='اقدام برای تایید';
                        break;
                    case isset($message['meta']['action']) && $message['meta']['action']=='reject':
                        $cssClass .= ' reject';
                        $action='اقدام برای مخالفت';
                        break;
                    case isset($message['meta']['action']) && $message['meta']['action']=='save':
                        $cssClass .= ' save';
                        $action='ذخیره اطلاعات';
                        break;
                    case isset($message['meta']['action']) && $message['meta']['action']=='seen':
                        $cssClass .= ' seen';
                        $action='مشاهده اطلاعات';
                        break;
                    case isset($message['meta']['action']) && $message['meta']['action']=='return':
                        $cssClass .= ' return';
                        $action='بازگردانی شده';
                        break;

                }

                $textAlign = $isSender ? 'text-right ml-auto' : 'text-right mr-auto';
                $float = $isSender ? 'float-left' : 'float-right';
            @endphp

            <div class="message-bubble flex nowrap {{ $cssClass }} {{ $textAlign }}"
                 style="margin: 8px 0; padding: 8px 12px; border-radius: 12px; max-width: 70%;  clear: both;">
                <div class="message-avatar">
                    <img src="{!! Avatar::create($message['sender_type'])->toBase64() !!}"
                         alt="{{$message['sender_type']}}" class="rounded-full w-10 h-10">
                </div>
                <div class="message-content">
                    <div class="message-header flex nowrap">
                        <div class="flex-1 sender flex-start">
                            {{ $message['sender_type'] }}
                        </div>
                        <div class="flex-2 date">
                            {{ \Morilog\Jalali\Jalalian::fromDateTime($message['created_at'])->ago() }}
                        </div>
                    </div>

                    <div class="message-body">{!! $message['content'] !!}</div>
                    @if(!empty($action))
                        <div class="message-action">
                            {{$action}}
                        </div>
                    @endif
                </div>
            </div>
            <div style="clear: both;"></div>
        @empty
            <div class="no-messages" style="text-align: center; color: #66; padding: 16px;">
                @if($persona==\Units\Chat\Common\Enums\ChatPersonaEnum::COMMENT->value)
                    نظری ثبت نشده است
                @else
                    هیچ پیامی وجود ندارد
                @endif
            </div>
        @endforelse
    </div>

    <!-- Typing Indicator -->
    @if($isTyping)
        <div class="typing-indicator" style="margin-bottom: 8px; font-size: 0.875rem; color: #666;">
            {{ $typingUser }} در حال نوشتن...
        </div>
    @endif

    <!-- Message Input -->
    <div class="chat-input" style="display: flex;flex-wrap: wrap; gap: 8px;">
        @if($persona==\Units\Chat\Common\Enums\ChatPersonaEnum::COMMENT->value)
            <div style="flex: auto;">
                @php
                    $tinyEditorComponent = \AmidEsfahani\FilamentTinyEditor\TinyEditor::make('chat');
                @endphp
                <div wire:ignore>
                <textarea
                    id="tiny-editor-message-{{ str_replace(':', '-', $this->getId()) }}"
                    wire:model="newMessage"
                    wire:keydown="startTyping"
                    wire:blur="stopTyping"
                    placeholder="پیام خود را بنویسید..."
                    style="width: 100%; min-height: 100px; padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; direction: rtl; text-align: right; resize: vertical;"
                >{{ $newMessage }}</textarea>
                </div>

                <script>
                    (function () {
                        // Create isolated scope for this component instance
                        var componentId = '{{ str_replace(':', '-', $this->getId()) }}';
                        var editorId = 'tiny-editor-message-' + componentId;
                        var isEditorInitialized = false;
                        var pollingInterval = null;

                        // Function to initialize TinyMCE editor
                        function initializeTinyMCE() {
                            if (typeof tinymce === 'undefined' || isEditorInitialized) {
                                return false;
                            }

                            // Destroy any existing editor instance with this ID first
                            if (tinymce.get(editorId)) {
                                tinymce.get(editorId).remove();
                            }

                            tinymce.init({
                                selector: '#' + editorId,
                                plugins: 'accordion autoresize codesample directionality advlist link image lists preview pagebreak searchreplace wordcount code fullscreen insertdatetime media table emoticons',
                                toolbar: 'undo redo removeformat | bold italic underline | rtl ltr | alignjustify alignleft aligncenter alignright | numlist bullist outdent indent | forecolor backcolor | blockquote table hr | image link media codesample emoticons | wordcount fullscreen',
                                // language: 'fa',
                                directionality: 'rtl',
                                height: 120,
                                menubar: false,
                                statusbar: false,
                                toolbar_sticky: true,
                                content_style: 'body { font-family: Tahoma, Arial, sans-serif; direction: rtl; text-align: right; }',
                                setup: function (editor) {
                                    editor.on('input', function () {
                                    @this.set('newMessage', editor.getContent())
                                        ;
                                    });

                                    editor.on('keydown', function (e) {
                                        if (e.key === 'Enter' && !e.shiftKey) {
                                            e.preventDefault();
                                        @this.call('sendMessage')
                                            ;
                                        }
                                    });
                                },
                                init_instance_callback: function (editor) {
                                    isEditorInitialized = true;
                                    console.log('TinyMCE editor initialized for component: ' + componentId);
                                }
                            });

                            return true;
                        }

                        // Initialize on DOM content load
                        document.addEventListener('DOMContentLoaded', function () {
                            initializeTinyMCE();
                        });

                        // Initialize on Livewire updates
                        if (typeof Livewire !== 'undefined') {
                            Livewire.on('morphdom:finished', function () {
                                // Small delay to ensure DOM is fully updated
                                setTimeout(function () {
                                    if (!isEditorInitialized && document.getElementById(editorId)) {
                                        initializeTinyMCE();
                                    }
                                }, 100);
                            });

                            // Listen for chat-message-sent event specifically for this component
                            Livewire.on('chat-message-sent-' + componentId, function () {
                            @this.call('refreshMessages')
                                ;
                            });

                            // Set up polling for this component instance
{{--                            @if(isset($polling) && $polling)--}}
                                pollingInterval = setInterval(function () {

                                @this.call('pollMessages');

                            }, 3000); // Poll every 3 seconds
{{--                            @endif--}}

                            // Clean up all resources when component is destroyed
                            Livewire.hook('component.dehydrate', (component) => {
                                if (component.fingerprint.id === '{{ $this->getId() }}') {
                                    // Clean up TinyMCE instance
                                    var editorId = 'tiny-editor-message-{{ str_replace(':', '-', $this->getId()) }}';
                                    if (typeof tinymce !== 'undefined' && tinymce.get(editorId)) {
                                        tinymce.get(editorId).remove();
                                    }

                                    // Clean up polling interval
                                    if (pollingInterval) {
                                        clearInterval(pollingInterval);
                                    }

                                    // Reset initialization flag
                                    isEditorInitialized = false;
                                }
                            });
                        }
                    })();
                </script>
            </div>
            <button

                type="button"
                wire:click="sendMessage"

                style="width: 100%;padding: 8px 16px;border: 1px solid #60A5FA; background-color: #BFDBFE; color: #1E40AF; border-radius: 6px; cursor: pointer; align-self: flex-start; margin-top: 26px;"
                onclick="event.preventDefault();"
            >
                {{ $button_label }}
            </button>
        @endif
        @if($persona!=\Units\Chat\Common\Enums\ChatPersonaEnum::COMMENT->value)
            <input
                type="text"
                wire:model="newMessage"
                wire:keydown.enter.prevent="sendMessage"
                wire:keydown="startTyping"
                wire:blur="stopTyping"
                placeholder="پیام خود را بنویسید..."
                style="flex: auto; padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; direction: rtl; text-align: right;"
                x-on:keydown.enter.prevent="$wire.sendMessage()"
            >
            <button

                type="button"
                wire:click="sendMessage"
                style="width:100%;padding: 8px 16px; border: 1px solid #60A5FA; background-color: #BFDBFE; color: #1E40AF; border-radius: 6px; cursor: pointer; align-self: flex-start; margin-top: 26px;"
                onclick="event.preventDefault();"
            >
                {{ $button_label }}
            </button>
        @endif
    </div>
    <style>
        .message-bubble{
            margin-bottom: 30px !important;
        }
        .chat-messages{
            padding-bottom: 40px;
        }
        .message-bubble>.message-avatar{
            width: 40px;
            height: 40px;

            /* Grey/900 */
            background: #171717;
            border-radius: 100px;
            flex: none;
            order: 0;
            align-self: stretch;
            flex-grow: 0;
        }
        .message-bubble>.message-content{
            order: 1;
            float: right;
            margin-right: 4px;
            align-self: stretch;
            flex-grow: 0;
        }
        .message-content>.message-header>.sender{
            /*font-family: 'Shabnam FD';*/
            font-style: normal;
            font-weight: 400;
            font-size: 14px;
            line-height: 22px;
            /* identical to box height, or 157% */
            text-align: right;
            color: #171717;
            flex: none;
            order: 1;
            margin-right: 14px;
            gap: 3px;
            flex-grow: 0;
        }
        .message-content>.message-header>.date{
            /*font-family: 'Shabnam FD';*/
            font-style: normal;
            font-weight: 400;
            font-size: 12px;
            margin-right: 4px;
            line-height: 22px;
            /* identical to box height, or 183% */
            text-align: right;
            color: #737373;
            /* Inside auto layout */
            flex: none;
            order: 2;
            gap: 3px;
            flex-grow: 0;
        }
        .message-content>.message-body{
            /* Detail */
            /*font-family: 'Shabnam FD';*/
            font-style: normal;
            font-weight: 400;
            font-size: 12px;
            line-height: 22px;
            /* or 183% */
            text-align: right;
            color: #737373;
            margin-right: 14px;
            /* Inside auto layout */
            flex: none;
            order: 1;
            align-self: stretch;
            flex-grow: 0;
        }
        .message-bubble.reject{
            border: dashed 2px red;
            padding: 10px;
            max-width: 100% !important;
        }
        .message-bubble.approve{
            border: dashed 2px lawngreen;
            padding: 10px;
            max-width: 100% !important;
        }
        .message-bubble.save{
            border: dashed 2px dodgerblue;
            padding: 10px;
            max-width: 100% !important;
        }
        .message-bubble.seen{
            border: dashed 2px yellow;
            padding: 10px;
            max-width: 100% !important;
        }
        .message-bubble.return{
            border: dashed 2px midnightblue;
            padding: 10px;
            max-width: 100% !important;
        }

        .message-bubble.reject .message-action{
            background: indianred;
            color: whitesmoke;
        }
        .message-bubble.approve .message-action{
            background: darkgreen;
            color: whitesmoke;
        }
        .message-bubble.save .message-action{
            background: cornflowerblue;
            color: whitesmoke;
        }
        .message-bubble.seen .message-action{
            background: darkorange;
            color: whitesmoke;
        }
        .message-bubble.return .message-action{
            background: midnightblue;
            color: whitesmoke;
        }

        .message-bubble .message-action{
            font-size: small;
            padding: 4px;
            display: block;
            margin-bottom: -23px;
            margin-right: -40px;
            width: fit-content;
            border-radius: 4px;
            margin-top: 10px;
        }


    </style>
</div>
