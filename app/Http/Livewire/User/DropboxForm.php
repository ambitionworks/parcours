<?php

namespace App\Http\Livewire\User;

use App\Models\User;
use Livewire\Component;
use Dcblogdev\Dropbox\Facades\Dropbox;
use Dcblogdev\Dropbox\Models\DropboxToken;
use Illuminate\Support\Facades\Auth;

class DropboxForm extends Component
{
    /**
     * The user for this component.
     *
     * @var User
     */
    public User $user;

    public bool $confirmingDisable = false;

    public bool $hasToken = false;

    public array $dropboxUser;

    public function mount()
    {
        $this->hasToken = is_string(Dropbox::getAccessToken(true));
        if ($this->hasToken) {
            $this->dropboxUser = Dropbox::post('users/get_current_account');
        }
    }

    public function confirmDisable() {
        $this->confirmingDisable = true;
    }

    public function disable()
    {
        DropboxToken::where('user_id', Auth::id())->delete();
        $this->hasToken = false;
        $this->dropboxUser = [];
        $this->confirmingDisable = false;
        $this->emit('disabled');
    }

    public function render()
    {
        return view('profile.dropbox-form');
    }
}
