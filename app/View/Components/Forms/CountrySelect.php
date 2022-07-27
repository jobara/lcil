<?php

namespace App\View\Components\Forms;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Spatie\LaravelOptions\Options;

class CountrySelect extends Component
{
    /** An ISO 3166-1 alpha-2 code.*/
    public string $country;

    /**
     * The list of available countries
     *
     * @var array[]
     */
    public array $countries;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(?string $country = '', ?string $placeholder = '')
    {
        $this->countries = Options::forArray(get_countries())->nullable($placeholder ?? '')->toArray();
        $this->country = isset($country) && in_array($country, array_column($this->countries, 'value')) ? $country : '';
    }

    public function render(): View|\Closure|string
    {
        return view('components.forms.country-select');
    }
}
