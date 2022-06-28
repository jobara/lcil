@props(['id' => 'error-summary', 'message' => __('Please check the following fields in order to proceed:')])
@if ($errors->any())
    <div {{ $attributes->merge(['id' => $id,'role' => 'alert']) }}>
        <p id="{{ $id . '__message' }}">{{ $message }}</p>
        <ul>
            @foreach($errors->keys() as $key)
                @foreach ($errors->get($key) as $message)
                    <li><a href="#{{ $key }}">{{ $message }}</a></li>
                @endforeach
            @endforeach
        </ul>
    </div>
@endif
