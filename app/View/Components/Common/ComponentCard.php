<?php

namespace App\View\Components\Common;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ComponentCard extends Component
{
    public ?string $title;
    public ?string $desc;

    /**
     * Create a new component instance.
     */
    public function __construct(
        ?string $title = null,
        ?string $desc = null
    ) {
        $this->title = $title;
        $this->desc = $desc;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.common.component-card');
    }
}