<?php

namespace App\View\Components;

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
     * The subdivision code.
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
     * The list of available countries
     *
     * @var array
     */
    protected $subdivisionRepository;

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
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.subdivision-select');
    }
}
