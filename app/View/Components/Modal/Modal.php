<?php

namespace App\View\Components\Modal;

use Illuminate\View\Component;

class Modal extends Component
{
    public function __construct(
        public string $show,
        public string $title = '',
        public string $description = '',
        public string $maxWidth = 'max-w-[700px]'
    ) {}

    public function render()
    {
        return view('components.modal.modal');
    }
}