<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SubdivisionSelect extends Component
{
    /**
     * The country code.
     *
     * @var string
     */
    public $country;

    /**
     * An ISO 3166-1 alpha-2 or ISO-3166-2 code.
     *
     * @var string
     */
    public $subdivision;

    /**
     * The list of available subdivisions
     *
     * @var array
     */
    public $subdivisions;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($country = 'all', $subdivision = '')
    {
        $this->country = $country;
        $this->subdivisions = $country === 'all' ? [] : get_subdivisions($country);
        $this->subdivision = isset($this->subdivisions[$subdivision]) ? $subdivision : '';
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return View|\Closure|string
     */
    public function render(): View|\Closure|string
    {
        return view('components.subdivision-select');
    }
}
