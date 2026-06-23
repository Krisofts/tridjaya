<?php

namespace App\View\Components\Form\Input;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class InputWithLabel extends Component
{
    public string $name;
    public string $label;
    public string $type;
    public mixed $value;
    public string $placeholder;
    public bool $required;
    public bool $readonly;
    public ?string $hint;

    public function __construct(
        string $name,
        string $label = '',
        string $type = 'text',
        mixed $value = null,
        string $placeholder = '',
        bool $required = false,
        bool $readonly = false,
        ?string $hint = null,
    ) {
        $this->name = $name;
        $this->label = $label;
        $this->type = $type;
        $this->value = $value; // jangan old() di sini
        $this->placeholder = $placeholder;
        $this->required = $required;
        $this->readonly = $readonly;
        $this->hint = $hint;
    }

    public function render(): View|Closure|string
    {
        return view('components.form.input.input-with-label');
    }
}