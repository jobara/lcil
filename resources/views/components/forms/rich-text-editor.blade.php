<div
    id="{{ $id }}-editor"
    x-data="editor('{{ $slot }}', {
        @if ($disabled)
            'enabled': false,
        @endif
        editorProps: {
            attributes: {
                @if ($required)
                    'aria-required': true,
                @endif
                @if ($autofocus)
                    'autofocus': '',
                @endif
                @if ($disabled)
                    'aria-disabled': true,
                    'tabindex': -1,
                @endif
                @if ($attributes->has('aria-label'))
                    'aria-label': '{{ $attributes->get('aria-label') }}',
                @endif
                @if ($attributes->has('aria-labelledby') && !$attributes->has('aria-label'))
                    'aria-labelledby': '{{ $attributes->get('aria-labelledby') }}',
                @endif
                @if ($describedBy())
                    'aria-describedby': '{{ $describedBy() }}',
                @endif
                @if ($invalid)
                    'aria-invalid': true,
                @endif
                'id': '{{ $id }}-editable',
                'aria-multiline': true,
                'role': 'textbox'
            }
        }
    })"
>
    <div
        id="{{ $id }}-toolbar"
        role="toolbar"
        @if ($disabled)
            aria-disabled=true
        @endif
        @if ($attributes->has('aria-label'))
            aria-label="{{ $attributes->get('aria-label') }}"
        @endif
        @if ($attributes->has('aria-labelledby') && !$attributes->has('aria-label'))
            aria-labelledby="{{ $attributes->get('aria-labelledby') }}"
        @endif
        aria-controls="{{ $id }}-editable"
        @keyup.home="focusFirst($event.target)"
        @keyup.end="focusLast($event.target)"
    >
        <button
            type="button"
            @disabled($disabled)
            @click="getEditor().chain().toggleBold().focus().run()"
            @focus="updateTabindexes($event.target)"
            @keyup.right="focusNext($event.target)"
            x-bind:aria-pressed="updatedAt && getEditor().isActive('bold')"
        >
            {{ __('bold') }}
        </button>
        <button
            type="button"
            tabindex="-1"
            @disabled($disabled)
            @click="getEditor().chain().toggleItalic().focus().run()"
            @focus="updateTabindexes($event.target)"
            @keyup.right="focusNext($event.target)"
            @keyup.left="focusPrev($event.target)"
            x-bind:aria-pressed="updatedAt && getEditor().isActive('italic')"
        >
            {{ __('italic') }}
        </button>
        <button
            type="button"
            tabindex="-1"
            @disabled($disabled)
            @click="getEditor().chain().toggleUnderline().focus().run()"
            @focus="updateTabindexes($event.target)"
            @keyup.right="focusNext($event.target)"
            @keyup.left="focusPrev($event.target)"
            x-bind:aria-pressed="updatedAt && getEditor().isActive('underline')"
        >
            {{ __('underline') }}
        </button>
        <button
            type="button"
            tabindex="-1"
            @disabled($disabled)
            @click="getEditor().chain().toggleStrike().focus().run()"
            @focus="updateTabindexes($event.target)"
            @keyup.right="focusNext($event.target)"
            @keyup.left="focusPrev($event.target)"
            x-bind:aria-pressed="updatedAt && getEditor().isActive('strike')"
        >
            {{ __('strike') }}
        </button>
        <button
            type="button"
            tabindex="-1"
            @disabled($disabled)
            @click="getEditor().chain().toggleBulletList().focus().run()"
            @focus="updateTabindexes($event.target)"
            @keyup.right="focusNext($event.target)"
            @keyup.left="focusPrev($event.target)"
            x-bind:aria-pressed="updatedAt && getEditor().isActive('bulletList')"
        >
            {{ __('bullet list') }}
        </button>
        <button
            type="button"
            tabindex="-1"
            @disabled($disabled)
            @click="getEditor().chain().toggleOrderedList().focus().run()"
            @focus="updateTabindexes($event.target)"
            @keyup.left="focusPrev($event.target)"
            x-bind:aria-pressed="updatedAt && getEditor().isActive('orderedList')"
        >
            {{ __('ordered list') }}
        </button>
    </div>

    <div x-ref="editorReference"></div>

    <x-hearth-input
        type="hidden"
        x-model="content"
        id="{{ $id }}"
        name="{{ $name }}"
        :required="$required"
        :disabled="$disabled"
        :hinted="$hinted"
    />
</div>
