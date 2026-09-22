<form method="POST" action="<?= url('login') ?>"
      class="rounded-2xl p-8 space-y-5"
      style="background: linear-gradient(180deg, rgba(24,18,15,0.9), rgba(10,8,7,0.95)); border: 1px solid rgba(202,166,87,0.22); box-shadow: 0 20px 60px rgba(0,0,0,0.55);">
    <?= csrf_field() ?>

    <div>
        <label for="email" class="block text-[11px] uppercase mb-2" style="color: #a3895a; letter-spacing: 0.2em;">Correo</label>
        <input type="email" name="email" id="email" required autofocus
               autocomplete="username" inputmode="email" autocapitalize="none" autocorrect="off"
               class="w-full rounded-lg px-4 py-2.5 text-sm focus:outline-none transition"
               style="background: #0a0807; border: 1px solid #362c22; color: #ecdfc0;"
               onfocus="this.style.borderColor='#caa657'" onblur="this.style.borderColor='#362c22'">
    </div>
    <div>
        <label for="password" class="block text-[11px] uppercase mb-2" style="color: #a3895a; letter-spacing: 0.2em;">Contraseña</label>
        <input type="password" name="password" id="password" required
               autocomplete="current-password" enterkeyhint="go"
               class="w-full rounded-lg px-4 py-2.5 text-sm focus:outline-none transition"
               style="background: #0a0807; border: 1px solid #362c22; color: #ecdfc0;"
               onfocus="this.style.borderColor='#caa657'" onblur="this.style.borderColor='#362c22'">
    </div>

    <button type="submit"
            class="w-full text-sm tracking-wide py-2.5 rounded-lg font-semibold transition"
            style="background: linear-gradient(135deg, #a97e46, #d8bd85, #a97e46); color: #1a1410;">
        Entrar
    </button>
</form>
