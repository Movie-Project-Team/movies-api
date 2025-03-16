<?php

namespace App\Mail;

use Illuminate\Contracts\View\View;
use Illuminate\Contracts\Support\Htmlable;

class StringViewMail implements Htmlable, View
{
    protected $viewName; // Tên view Blade
    protected $data; // Dữ liệu được truyền vào view

    /**
     * Constructor.
     *
     * @param string $viewName
     * @param array $data
     */
    public function __construct(string $viewName, array $data = [])
    {
        $this->viewName = $viewName;
        $this->data = $data;
    }

    /**
     * Get content as a string of HTML.
     *
     * @return string
     */
    public function toHtml(): string
    {
        return view($this->viewName, $this->data)->render();
    }

    /**
     * Get the evaluated contents of the object.
     *
     * @return string
     */
    public function render(): string
    {
        return view($this->viewName, $this->data)->render();
    }

    /**
     * Get the name of the view.
     *
     * @return string
     */
    public function name(): string
    {
        return $this->viewName;
    }

    /**
     * Add additional data to the view.
     *
     * @param array|string $key
     * @param mixed $value
     * @return StringViewMail
     */
    public function with($key, $value = null)
    {
        if (is_array($key)) {
            $this->data = array_merge($this->data, $key);
        } else {
            $this->data[$key] = $value;
        }

        return $this;
    }

    /**
     * Get the view data.
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }
}
