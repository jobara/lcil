<?php

namespace App\View\Components;

use Hearth\Traits\AriaDescribable;
use Hearth\Traits\HandlesValidation;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SubdivisionSelect extends Component
{
    use AriaDescribable;
    use HandlesValidation;

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
     * The name of the form input.
     *
     * @var string
     */
    public string $name;

    /**
     * Whether the form input has validation errors.
     *
     * @var bool
     */
    public bool $invalid;

    /**
     * Whether the form input has a hint associated with it, or the id of the hint.
     *
     * @var bool|string
     */
    public bool|string $hinted;

    /**
     * The error bag associated with the form input.
     *
     * @var ?string
     */
    public ?string $bag;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        ?string $country = null,
        ?string $subdivision = '',
        string $name = 'subdivision',
        string $bag = 'default',
        $hinted = false
    ) {
        $this->country = $country ?? '';
        $this->subdivisions = $country ? get_subdivisions($country) : [];
        $this->subdivision = isset($this->subdivisions[$subdivision]) ? $subdivision : '';
        $this->name = $name;
        $this->bag = $bag;
        $this->hinted = $hinted;
        $this->invalid = $this->hasErrors($this->name, $this->bag);
    }

    public function render(): View|\Closure|string
    {
        return view('components.subdivision-select');
    }
}
