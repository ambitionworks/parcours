<?php

namespace App\Http\Livewire\User;

use App\Models\Goal;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class GoalsForm extends Component
{
    public bool $visible = false;

    public array $state = [];

    public array $durations = [];

    public User $user;

    public bool $confirmingDelete = false;

    public ?int $toDelete;

    protected $listeners = ['goals:saved' => '$refresh'];

    public function mount()
    {
        $this->buildState();
    }

    public function updatedDurations($update, $key)
    {
        list($index, ) = explode('.', $key);
        $this->state[$index]['goal'] = $this->durations[$index]['h'] * 3600 + $this->durations[$index]['m'] * 60;
    }

    public function confirmDelete(int $index)
    {
        $this->confirmingDelete = true;
        $this->toDelete = $index;
    }

    public function delete()
    {
        $this->user->goals->get($this->toDelete)->delete();
        $this->resetState($this->toDelete);
        $this->confirmingDelete = false;
    }

    public function save()
    {
        $this->resetErrorBag();

        $toSave = [];
        foreach ($this->state as $index => $row) {
            if (empty($row['type']) && empty($row['interval']) && empty($row['goal'])) {
            } else if (!empty($row['type']) && !empty($row['interval']) && !empty($row['goal'])) {
                $toSave[] = $index;
            } else {
                throw ValidationException::withMessages([__('Goal #:goal is incomplete', ['goal' => $index + 1])]);
            }
        }

        foreach ($toSave as $index) {
            if ($this->user->goals->get($index)) {
                $this->user->goals->get($index)->update([
                    'type' => $this->state[$index]['type'],
                    'interval' => $this->state[$index]['interval'],
                    'goal' => $this->state[$index]['goal'],
                ]);
            } else {
                $goal = new Goal([
                    'type' => $this->state[$index]['type'],
                    'interval' => $this->state[$index]['interval'],
                    'goal' => $this->state[$index]['goal'],
                ]);
                $goal->user()->associate($this->user);
                $goal->save();
            }
        }

        $this->buildState();

        $this->emit('goals:saved');
    }

    public function toggle()
    {
        $this->visible = !$this->visible;
    }

    public function render()
    {
        return view('profile.goals-form');
    }

    private function buildState()
    {
        $this->state = [];
        foreach ($this->user->goals()->get() as $index => $goal) {
            $this->state[$index] = [
                'type' => $goal->type,
                'interval' => $goal->interval,
                'goal' => $goal->goal,
            ];

            if ($goal->type === 'duration') {
                $this->durations[$index] = [
                    'h' => $goal->goal / 3600,
                    'm' => ($goal->goal % 3600) / 60,
                ];
            }
        }

        for ($i = 0; $i  < 5; $i ++) {
            if (!isset($this->state[$i])) {
                $this->resetState($i);
            }
        }
    }

    private function resetState(int $index)
    {
        $this->state[$index] = [
            'type' => '',
            'interval' => '',
            'goal' => '',
        ];
        $this->durations[$index] = [
            'h' => 0,
            'm' => 0,
        ];
    }
}
