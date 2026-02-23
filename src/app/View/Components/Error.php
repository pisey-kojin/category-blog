<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Error extends Component
{

    /**
     * エラーフィールド名
     */
    public string $field;

    /**
     * 追加のCSSクラス
     */
    public string $class;

    /**
     * Create a new component instance.
     */
    public function __construct(string $field, string $class = 'mt-1 text-sm text-red-600')
    {
        $this->field = $field;
        $this->class = $class;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.error');
    }
}
