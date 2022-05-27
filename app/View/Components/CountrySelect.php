<?php

namespace App\View\Components;

use Illuminate\View\Component;

class CountrySelect extends Component
{
    /**
     * The country code.
     *
     * @var string
     */
    public $country;

    /**
     * The list of available countries
     *
     * @var array
     */
    public $countries;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($country = 'all')
    {
        $this->countries = get_countries();
        $this->country = isset($this->countries[$country]) ? $country : 'all';
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.country-select');
    }
}
