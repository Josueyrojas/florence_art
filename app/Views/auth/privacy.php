<div class="rounded-2xl p-8"
     style="background: #f5f0e6; border: 1px solid rgba(202,166,87,0.3); box-shadow: 0 20px 60px rgba(0,0,0,0.5); color: #2a2118;">

    <h1 class="font-display text-2xl mb-1" style="color: #1a1410;">Aviso de Privacidad</h1>
    <p class="text-xs mb-6" style="color: #7a6b52;">Última actualización: <?= e(date('d/m/Y')) ?></p>

    <div class="space-y-4 text-sm leading-relaxed">
        <p>
            <strong><?= e($settings['business_name']) ?></strong> (en adelante, "el Taller"), con domicilio
            para efectos de este aviso el que se indica en la sección de contacto, es responsable del
            tratamiento de los datos personales que se recaban a través de este sistema interno de
            control financiero y gestión de proyectos, de conformidad con la Ley Federal de Protección de
            Datos Personales en Posesión de los Particulares (LFPDPPP).
        </p>

        <h2 class="font-semibold pt-2" style="color: #1a1410;">1. Datos personales que se recaban</h2>
        <p>
            Este sistema es de uso interno y captura datos de <strong>clientes</strong> (nombre, teléfono,
            correo electrónico, dirección) y <strong>proveedores</strong> (nombre o razón social, persona de
            contacto, teléfono, correo, dirección) con motivo de la relación comercial existente con el
            Taller. No se recaban datos personales sensibles.
        </p>

        <h2 class="font-semibold pt-2" style="color: #1a1410;">2. Finalidad del tratamiento</h2>
        <p>Los datos se utilizan exclusivamente para:</p>
        <ul class="list-disc pl-5 space-y-1">
            <li>Dar seguimiento a proyectos, cotizaciones y órdenes de trabajo.</li>
            <li>Registrar abonos, saldos pendientes y el historial de pagos.</li>
            <li>Gestionar compras y pagos a proveedores.</li>
            <li>Contactar al cliente o proveedor sobre el estado de su proyecto o pedido.</li>
        </ul>
        <p>No se utilizan los datos con fines de mercadotecnia, publicidad o prospección comercial
           distinta a la relación ya existente, salvo que se cuente con el consentimiento expreso del titular.</p>

        <h2 class="font-semibold pt-2" style="color: #1a1410;">3. Transferencia de datos</h2>
        <p>
            Los datos capturados en este sistema no se transfieren a terceros, salvo obligación legal.
            El sistema es de acceso restringido, únicamente para personal autorizado del Taller.
        </p>

        <h2 class="font-semibold pt-2" style="color: #1a1410;">4. Medidas de seguridad</h2>
        <p>
            El acceso al sistema requiere usuario y contraseña individuales; las contraseñas se almacenan
            cifradas y nunca en texto plano. Las sesiones se cierran automáticamente por inactividad y
            solo se permite una sesión activa por usuario. El tráfico entre tu navegador y el servidor
            viaja cifrado (HTTPS).
        </p>

        <h2 class="font-semibold pt-2" style="color: #1a1410;">5. Derechos ARCO</h2>
        <p>
            Como titular de tus datos personales, tienes derecho a Acceder, Rectificar, Cancelar u
            Oponerte (derechos ARCO) al tratamiento de tus datos. Para ejercerlos, contáctanos por
            cualquiera de los medios señalados en la sección de contacto de este aviso.
        </p>

        <h2 class="font-semibold pt-2" style="color: #1a1410;">6. Cookies utilizadas</h2>
        <p>
            Este sistema solo utiliza una cookie técnica, estrictamente necesaria para mantener tu sesión
            iniciada mientras usas la aplicación. No se utilizan cookies de rastreo, publicidad o análisis
            de terceros. Esa cookie se elimina automáticamente al cerrar sesión, por inactividad, o al
            cerrar el navegador.
        </p>

        <h2 class="font-semibold pt-2" style="color: #1a1410;">7. Cambios a este aviso</h2>
        <p>
            Cualquier modificación a este aviso de privacidad se publicará en esta misma página.
        </p>

        <h2 class="font-semibold pt-2" style="color: #1a1410;">8. Contacto</h2>
        <p>
            <?php if ($settings['email']): ?>Correo: <?= e($settings['email']) ?><br><?php endif; ?>
            <?php if ($settings['phone']): ?>Teléfono: <?= e($settings['phone']) ?><br><?php endif; ?>
            <?php if ($settings['address']): ?>Dirección: <?= e($settings['address']) ?><?php endif; ?>
        </p>
    </div>

    <div class="mt-8 pt-4" style="border-top: 1px solid rgba(202,166,87,0.3);">
        <a href="<?= url('login') ?>" class="text-sm" style="color: #8a6d2f;">&larr; Volver al inicio de sesión</a>
    </div>
</div>
