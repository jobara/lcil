<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Duration extends Component
{
    /**
     * The options to pass into the underlying duration alpine component
     *
     * @var array[]
     */
    public array $options;

    public function getDurationMarkup(): string
    {
        return '<span x-text="duration.text"></span>';
    }

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(?array $options = [])
    {
        $this->options = array_merge([
            'unitText' => [
                'years' => [
                    'singular' => __('year'),
                    'plural' => __('years'),
                ],
                'months' => [
                    'singular' => __('month'),
                    'plural' => __('months'),
                ],
                'days' => [
                    'singular' => __('day'),
                    'plural' => __('days'),
                ],
                'hours' => [
                    'singular' => __('hour'),
                    'plural' => __('hours'),
                ],
                'minutes' => [
                    'singular' => __('minute'),
                    'plural' => __('minutes'),
                ],
                'seconds' => [
                    'singular' => __('second'),
                    'plural' => __('seconds'),
                ],
            ],
        ], $options ?? []);
    }

    public function render(): View|\Closure|string
    {
        return view('components.duration');
    }
}
