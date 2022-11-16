<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>@if (isset($title) && $title){{ $title }} &mdash; @endif{{ config('app.name') }}</title>

<meta name="description" content="{{ __('The :name (:abbr) is a tool for assessing the inclusivity of legal regimes regulating legal capacity by evaluating main sources of law to established measures.', ['name' => config('app.name'),'abbr' => config('app.abbr')]) }}">
<meta name="theme-color" content="#fff" media="(prefers-color-scheme: light)">
<meta name="theme-color" content="#000" media="(prefers-color-scheme: dark)">

<!-- Manifest -->
<link rel="manifest" href="/manifest.webmanifest">

<!-- Icons -->
<link rel="icon" href="/favicon.ico">
<link rel="icon" href="/icon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

<!-- Styles -->
@vite('resources/css/app.css')
@googlefonts

<!-- Scripts -->
<script>document.documentElement.className = document.documentElement.className.replace("no-js", "js");</script>

