<?php

namespace Mautic\CoreBundle\Event;

use Symfony\Bundle\FrameworkBundle\Templating\PhpEngine;
use Symfony\Component\EventDispatcher\Event;

class SidebarCanvasEvent extends Event
{
    /**
     * @var array
     */
    private $sections = ['header', 'footer', 'content'];

    /**
     * @var array
     */
    private $left = [];

    /**
     * @var array
     */
    private $right = [];

    /**
     * @var PhpEngine
     */
    private $templating;

    /**
     * @var array
     */
    private $main = [];

    public function __construct(PhpEngine $templating)
    {
        $this->templating = $templating;
    }

    /**
     * Insert content into left canvas.
     */
    public function pushToLeftCanvas(array $sections)
    {
        $this->setCanvasSection('left', $sections);
    }

    /**
     * Insert content into right canvas.
     */
    public function pushToRightCanvas(array $sections)
    {
        $this->setCanvasSection('right', $sections);
    }

    /**
     * Insert content into main canvas.
     *
     * Note that header is not allowed for main
     */
    public function pushToMainCanvas(array $sections)
    {
        $this->setCanvasSection('main', $sections);
    }

    /**
     * @param $canvas
     * @param $sections
     */
    private function setCanvasSection($canvas, $sections)
    {
        $canvasSections = [];
        foreach ($this->sections as $section) {
            $canvasSections[$section] = (isset($sections[$section])) ? $sections[$section] : '';
        }

        $this->{$canvas} = $canvasSections;
    }

    /**
     * Get the canvas sections.
     *
     * @param null $canvas
     *
     * @return array
     */
    public function getCanvasContent($canvas = null)
    {
        if ($canvas) {
            return $this->$canvas;
        } else {
            return [
                'left'  => $this->left,
                'right' => $this->right,
                'main'  => $this->main,
            ];
        }
    }

    /**
     * @return mixed
     */
    public function getTemplating()
    {
        return $this->templating;
    }
}
