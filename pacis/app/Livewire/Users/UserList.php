<?php

namespace App\Livewire\Users;

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Usuarios')]
class UserList extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    public function updatingSearch(): void { $this->resetPage(); }

    public function render()
    {
        $users = User::with('roles')
            ->when($this->search, fn ($q) => $q->where(function ($w) {
                $w->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%");
            }))
            ->orderBy('name')
            ->paginate(20);

        return view('livewire.users.list', compact('users'));
    }

    public function toggle(User $user): void
    {
        $user->update(['active' => ! $user->active]);
    }
}
