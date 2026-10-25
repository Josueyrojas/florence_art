<form method="POST" action="<?= url('login') ?>"
      class="auth-glass-card rounded-2xl p-8 space-y-5">
    <?= csrf_field() ?>

    <div>
        <label for="email" class="block text-[11px] uppercase mb-2" style="color: #a3895a; letter-spacing: 0.2em;">Correo</label>
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6b5c44" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 6h18v12H3z"/>
                <path d="M3 7l9 6 9-6"/>
            </svg>
            <input type="email" name="email" id="email" required autofocus
                   autocomplete="username" inputmode="email" autocapitalize="none" autocorrect="off"
                   class="auth-input auth-glass-input w-full rounded-lg pl-9 pr-4 py-2.5 text-sm"
                   style="background: #0a0807; border: 1px solid #362c22; color: #ecdfc0;">
        </div>
    </div>
    <div>
        <label for="password" class="block text-[11px] uppercase mb-2" style="color: #a3895a; letter-spacing: 0.2em;">Contraseña</label>
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6b5c44" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                <rect x="4" y="11" width="16" height="9" rx="1.5"/>
                <path d="M8 11V8a4 4 0 0 1 8 0v3"/>
            </svg>
            <input type="password" name="password" id="password" required
                   autocomplete="current-password" enterkeyhint="go"
                   class="auth-input auth-glass-input w-full rounded-lg pl-9 pr-10 py-2.5 text-sm"
                   style="background: #0a0807; border: 1px solid #362c22; color: #ecdfc0;">
            <button type="button" aria-label="Mostrar u ocultar contraseña"
                    class="auth-eye-toggle absolute right-2.5 top-1/2 -translate-y-1/2 p-1"
                    style="color: #6b5c44;"
                    onclick="
                        const i = document.getElementById('password');
                        i.type = i.type === 'password' ? 'text' : 'password';
                        this.querySelector('.eye-open').classList.toggle('hidden');
                        this.querySelector('.eye-closed').classList.toggle('hidden');
                    ">
                <svg class="eye-open" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/>
                    <circle cx="12" cy="12" r="3"/>
                </svg>
                <svg class="eye-closed hidden" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17.94 17.94A10.94 10.94 0 0 1 12 19c-7 0-11-7-11-7a21.3 21.3 0 0 1 5.06-5.94M9.9 4.24A10.94 10.94 0 0 1 12 4c7 0 11 7 11 7a21.3 21.3 0 0 1-2.69 3.9M14.12 14.12a3 3 0 1 1-4.24-4.24"/>
                    <path d="M1 1l22 22"/>
                </svg>
            </button>
        </div>
    </div>

    <button type="submit"
            class="auth-submit w-full text-sm tracking-wide py-2.5 rounded-lg font-semibold"
            style="background: linear-gradient(135deg, #a97e46, #d8bd85, #a97e46); color: #1a1410;">
        Entrar
    </button>

    <p class="text-center text-xs" style="color: #6b5c44;">
        Al continuar aceptas nuestro
        <a href="<?= url('privacidad') ?>" class="underline" style="color: #a3895a;">Aviso de Privacidad</a>.
    </p>
</form>
