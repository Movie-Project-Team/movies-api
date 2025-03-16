<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class TemplateMailable extends Mailable
{
    /**
     * The name of the view.
     *
     * @var string
     */
    public $viewName;

    /**
     * The data for the view.
     *
     * @var array
     */
    public $viewData;

    /**
     * Constructor.
     *
     * @param string $viewName
     * @param array $data
     */
    public function __construct(string $viewName, array $data = [])
    {
        $this->viewName = $viewName;
        $this->viewData = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->view = [
            'html' => view($this->viewName, $this->viewData)->render(),
            'text' => strip_tags(view($this->viewName, $this->viewData)->render()),
        ];

        return $this;
    }

    /**
     * Get view content.
     *
     * @param string $type
     * @return string
     */
    public function getView($type = 'html')
    {
        $this->build();
        return $this->view[$type];
    }

    /**
     * Get email subject.
     *
     * @return string
     */
    public function getSubject(): string
    {
        return $this->viewData['subject'] ?? 'Default Subject';
    }
}
