<?php

namespace Tests\Fixtures;

class MultiEventTask extends SimpleTask
{
    /**
     * @return void
     */
    protected function initEvents()
    {
        parent::initEvents();

        $this->addBeforeEvent(function () {
            static::mark('before');
        });

        $this->addAfterEvent(function () {
            static::mark('after');
        });
    }
}