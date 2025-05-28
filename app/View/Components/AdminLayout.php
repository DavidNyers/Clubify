<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AdminLayout extends Component
{
    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        // Statt 'components.admin-layout' verweisen wir auf 'layouts.admin' im 'admin' Ordner
        return view('admin.layouts.admin');
    }
}