<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SubdivisionSelect extends Component
{
    /**
     * An ISO 3166-1 alpha-2 country code.
     *
     * @var string
     */
    public string $country;

    /**
     * An ISO 3166-1 alpha-2 or ISO-3166-2 code.
     *
     * @var string
     */
    public string $subdivision;

    /**
     * The list of available subdivisions
     *
     * @var array<string, string>
     */
    public array $subdivisions;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(string $country = 'all', string $subdivision = '')
    {
        $this->country = $country;
        $this->subdivisions = $country === 'all' ? [] : get_subdivisions($country);
        $this->subdivision = isset($this->subdivisions[$subdivision]) ? $subdivision : '';
    }

    public function render(): View|\Closure|string
    {
        return view('components.subdivision-select');
    }
}
