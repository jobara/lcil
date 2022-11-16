@props(['level' => 3])
<h{{ clamp($level, 1, 6) }}>
   {{  $slot }}
</h{{ clamp($level, 1, 6) }}>
