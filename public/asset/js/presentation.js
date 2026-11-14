/**
 * Controla as interações da apresentação temporária do LEBRE.
 *
 * Não consulta serviços nem modifica configurações.
 *
 * @version 1.0.0
 * @since 2026-11-01
 */
(() => {
    'use strict';

    document.querySelectorAll('[data-copy-target]').forEach((button) => {
        button.addEventListener('click', async () => {
            const target = document.getElementById(button.dataset.copyTarget);
            const status = button.closest('.toolbar')?.querySelector('[data-copy-status]');

            if (!target) {
                if (status) status.textContent = 'Conteúdo não encontrado.';
                return;
            }

            try {
                if (!navigator.clipboard?.writeText) {
                    throw new Error('clipboard-unavailable');
                }

                await navigator.clipboard.writeText(target.textContent ?? '');
                if (status) status.textContent = 'Conteúdo copiado.';
            } catch {
                if (status) {
                    status.textContent = 'Não foi possível copiar automaticamente. '
                        + 'Selecione o conteúdo e copie manualmente.';
                }
            }
        });
    });
})();
