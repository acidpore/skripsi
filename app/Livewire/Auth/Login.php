<?php

declare(strict_types=1);

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Login extends Component
{
    public string $email = '';

    public string $password = '';

    public ?string $pesanError = null;

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak sah.',
            'password.required' => 'Kata sandi wajib diisi.',
        ];
    }

    public function masuk(): void
    {
        $this->pesanError = null;
        $this->validate();

        if (!Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
            $this->pesanError = 'Email atau kata sandi salah.';

            return;
        }

        session()->regenerate();

        $this->redirectRoute('rekomendasi.index');
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
