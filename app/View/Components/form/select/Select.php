<?php

namespace App\View\Components\Form\Select;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class Select extends Component
{
    public string $name;
    public string $label;
    public array $options;
    public mixed $selected;
    public string $placeholder;
    public bool $multiple;

    public function __construct(
        string $name,
        string $label = '',
        array|Collection $options = [],
        mixed $selected = null,
        string $placeholder = 'Select option',
        bool $multiple = false
    ) {
        $this->name = $name;
        $this->label = $label;

        // ✅ FIX: support Collection + array
        $this->options = $this->normalizeOptions($options);

        $this->multiple = $multiple;
        $this->placeholder = $placeholder;

        // auto normalize selected
        $this->selected = $multiple
            ? (array) $selected
            : $selected;
    }

    /**
     * Normalize options agar selalu array
     */
    private function normalizeOptions(array|Collection $options): array
    {
        if ($options instanceof Collection) {
            return $options->values()->toArray();
        }

        return $options;
    }

    public function render(): View|Closure|string
    {
        return view('components.form.select.select');
    }
}