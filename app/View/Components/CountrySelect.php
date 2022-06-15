<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CountrySelect extends Component
{
    /** An ISO 3166-1 alpha-2 code.*/
    public string $country;

    /**
     * The list of available countries
     *
     * @var array<string, string>
     */
    public array $countries;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(?string $country = '')
    {
        $this->countries = get_countries();
        $this->country = isset($this->countries[$country]) ? $country : '';
    }

    public function render(): View|\Closure|string
    {
        return view('components.country-select');
    }
}
