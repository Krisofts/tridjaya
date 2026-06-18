<?php

namespace App\View\Components\Form\Textarea;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TextareaWithLabel extends Component
{
    public string $name;
    public string $label;
    public mixed $value;
    public string $placeholder;
    public int $rows;
    public bool $required;
    public bool $readonly;
    public ?string $hint;
    public ?string $error;

    public function __construct(
        string $name,
        string $label = '',
        mixed $value = null,
        string $placeholder = '',
        int $rows = 5,
        bool $required = false,
        bool $readonly = false,
        ?string $hint = null,
        ?string $error = null,
    ) {
        $this->name = $name;
        $this->label = $label;
        $this->value = old($name, $value);
        $this->placeholder = $placeholder;
        $this->rows = $rows;
        $this->required = $required;
        $this->readonly = $readonly;
        $this->hint = $hint;
        $this->error = $error;
    }

    public function render(): View|Closure|string
    {
        return view('components.form.textarea.textarea-with-label');
    }
}