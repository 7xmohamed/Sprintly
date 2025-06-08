<?php

namespace App\Livewire\Modals;

use Livewire\Component;

class ModalManager extends Component
{
    public $show = false;
    public $component = null;
    public $arguments = [];

    protected $listeners = ['openModal' => 'openModal', 'closeModal' => 'closeModal'];

    public function openModal($params = [])
    {
        if (is_string($params)) {
            $this->component = $params;
            $this->arguments = [];
        } else {
            $this->component = $params['component'] ?? null;
            $this->arguments = $params['arguments'] ?? [];
        }
        $this->show = true;
    }

    public function closeModal()
    {
        $this->show = false;
        $this->component = null;
        $this->arguments = [];
    }

    public function render()
    {
        return view('livewire.modals.modal-manager');
    }
} 