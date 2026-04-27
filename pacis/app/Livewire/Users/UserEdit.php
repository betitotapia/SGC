<?php

namespace App\Livewire\Users;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\Permission\Models\Role;

#[Layout('components.layouts.app')]
#[Title('Usuario')]
class UserEdit extends Component
{
    public ?User $user = null;

    public string $name = '';
    public string $email = '';
    public ?string $phone = null;
    public string $password = '';
    public string $password_confirmation = '';
    public bool $active = true;
    public array $roles = [];

    public function mount(?User $user = null): void
    {
        if ($user?->exists) {
            $this->user = $user;
            $this->fill($user->only(['name','email','phone','active']));
            $this->roles = $user->roles->pluck('name')->toArray();
        }
    }

    protected function rules(): array
    {
        return [
            'name'  => ['required','string','max:255'],
            'email' => ['required','email','max:255',
                Rule::unique('users','email')->ignore($this->user?->id),
            ],
            'phone' => ['nullable','string','max:20'],
            'password' => [
                $this->user ? 'nullable' : 'required',
                'confirmed','min:8',
            ],
            'active' => ['boolean'],
            'roles'  => ['array'],
            'roles.*'=> ['string','exists:roles,name'],
        ];
    }

    public function save()
    {
        $data = $this->validate();

        $payload = collect($data)->only(['name','email','phone','active'])->all();
        if (! empty($this->password)) {
            $payload['password'] = Hash::make($this->password);
        }

        if ($this->user) {
            $this->user->update($payload);
        } else {
            $this->user = User::create($payload);
        }

        $this->user->syncRoles($this->roles);

        session()->flash('success', 'Usuario guardado.');
        return redirect()->route('users.index');
    }

    public function render()
    {
        return view('livewire.users.edit', [
            'availableRoles' => Role::orderBy('name')->pluck('name'),
        ]);
    }
}
