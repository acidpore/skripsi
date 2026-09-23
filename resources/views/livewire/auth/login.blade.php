<div class="mx-auto max-w-md py-6 sm:py-12">
    <p class="eyebrow">Masuk</p>
    <h1 class="font-display mt-2 text-3xl font-medium tracking-tight">Akun operator SPPG</h1>
    <p class="mt-2 text-sm text-ink-2">Gunakan email yang didaftarkan admin untuk satuan pelayanan Anda.</p>

    @if ($pesanError)
        <div class="mt-6 flex gap-4 border-l-2 border-ember bg-ember-light px-4 py-3 text-sm">
            <span class="eyebrow mt-0.5 !text-ember">Galat</span>
            <span>{{ $pesanError }}</span>
        </div>
    @endif

    <form wire:submit="masuk" class="rule-strong mt-8">
        <div class="py-5 rule first:border-t-0">
            <label for="email" class="block font-medium">Email</label>
            <input id="email" type="email" wire:model="email" autocomplete="username" placeholder="nama@sppg.id" class="field mt-2">
            @error('email') <p class="mt-2 text-sm text-ember">{{ $message }}</p> @enderror
        </div>
        <div class="py-5 rule">
            <label for="password" class="block font-medium">Kata sandi</label>
            <input id="password" type="password" wire:model="password" autocomplete="current-password" class="field mt-2">
            @error('password') <p class="mt-2 text-sm text-ember">{{ $message }}</p> @enderror
        </div>
        <div class="rule pt-5">
            <button type="submit" class="btn btn-ink w-full py-3" wire:loading.attr="disabled" wire:target="masuk">
                <span wire:loading.remove wire:target="masuk" class="flex items-center gap-2"><svg class="ico" viewBox="0 0 24 24"><path d="M14 4h5v16h-5M4 12h11M11 8l4 4-4 4"/></svg>Masuk</span>
                <span wire:loading wire:target="masuk" class="sweep w-full">Memeriksa akun</span>
            </button>
        </div>
    </form>
</div>
