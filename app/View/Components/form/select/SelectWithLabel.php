<?php

namespace App\View\Components\Form\Select;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SelectWithLabel extends Component
{
    public string $name;
    public string $label;
    public array $options;
    public mixed $selected;
    public string $placeholder;
    public bool $required;
    public bool $disabled;
    public bool $multiple;
    public ?string $hint;
    public ?string $error;

    public function __construct(
        string $name,
        string $label = '',
        array $options = [],
        mixed $selected = null,
        string $placeholder = 'Select Option',
        bool $required = false,
        bool $disabled = false,
        bool $multiple = false,
        ?string $hint = null,
        ?string $error = null,
    ) {
        $this->name = $name;
        $this->label = $label;
        $this->options = $options;
        $this->selected = old($name, $selected);
        $this->placeholder = $placeholder;
        $this->required = $required;
        $this->disabled = $disabled;
        $this->multiple = $multiple;
        $this->hint = $hint;
        $this->error = $error;
    }

    public function render(): View|Closure|string
    {
        return view('components.form.select.select-with-label');
    }
}