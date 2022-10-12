@error($field, $bag)
<p class="field__error" id="{{ $for }}-error">
    @svg('gmdi-error-outline', 'icon-inline', ['aria-hidden' => 'true'])
    {{ $message }}
</p>
@elseif($slot)
<p class="field__error" id="{{ $for }}-error">
    {{ $slot }}
</p>
@enderror
