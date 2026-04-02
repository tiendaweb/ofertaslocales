(function () {
    const isValidUrlValue = (value) => value.startsWith('/') || /^https?:\/\//i.test(value);

    const setupInlineEditMode = (config) => {
        if (!config.isAdmin) {
            return;
        }

        const toggleButton = document.querySelector('[data-inline-edit-toggle]');
        const panel = document.querySelector('[data-inline-edit-panel]');
        const feedbackNode = document.querySelector('[data-inline-edit-feedback]');
        const saveButton = document.querySelector('[data-inline-edit-save]');
        const cancelButton = document.querySelector('[data-inline-edit-cancel]');
        const editableNodes = Array.from(document.querySelectorAll('[data-editable-key]'));

        if (!toggleButton || !panel || editableNodes.length === 0) {
            return;
        }

        const editors = new Map();
        let isEditing = false;

        const setFeedback = (message, tone = 'neutral') => {
            if (!feedbackNode) {
                return;
            }

            feedbackNode.textContent = message;
            feedbackNode.classList.remove('text-indigo-600', 'text-emerald-700', 'text-rose-700');
            feedbackNode.classList.add(tone === 'success' ? 'text-emerald-700' : (tone === 'error' ? 'text-rose-700' : 'text-indigo-600'));
        };

        const clearFieldErrors = () => {
            editors.forEach((editor) => {
                editor.errorNode.textContent = '';
                editor.input.classList.remove('border-rose-400');
            });
        };

        const setFieldError = (key, message) => {
            const editor = editors.get(key);
            if (!editor) {
                return;
            }

            editor.errorNode.textContent = message;
            editor.input.classList.add('border-rose-400');
        };

        const validateInput = (editor, value) => {
            if (value === '') {
                return 'Este campo no puede quedar vacío.';
            }

            if (editor.type === 'url' && !isValidUrlValue(value)) {
                return 'Ingresá una URL válida (http/https) o una ruta interna (/ruta).';
            }

            return null;
        };

        const applyValue = (editor, value) => {
            if (editor.attr === 'href') {
                editor.node.setAttribute('href', value);
                return;
            }

            editor.node.textContent = value;
        };

        editableNodes.forEach((node) => {
            const key = node.dataset.editableKey || '';
            const attr = node.dataset.editableAttr || 'text';
            const type = node.dataset.editableType || 'text';
            const initialValue = attr === 'href' ? (node.getAttribute('href') || '') : (node.textContent || '').trim();
            const input = type === 'textarea' ? document.createElement('textarea') : document.createElement('input');

            if (type !== 'textarea') {
                input.setAttribute('type', type === 'url' ? 'url' : 'text');
            }

            input.value = initialValue;
            input.hidden = true;
            input.className = 'mt-2 w-full rounded-xl border border-indigo-200 bg-white/90 px-3 py-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/40';

            const errorNode = document.createElement('p');
            errorNode.className = 'mt-1 text-xs font-medium text-rose-700';

            node.insertAdjacentElement('afterend', input);
            input.insertAdjacentElement('afterend', errorNode);
            editors.set(key, { key, node, input, errorNode, attr, type, originalValue: initialValue });
        });

        const rollback = () => {
            editors.forEach((editor) => {
                applyValue(editor, editor.originalValue);
                editor.input.value = editor.originalValue;
            });
        };

        const setMode = (enabled) => {
            isEditing = enabled;
            panel.classList.toggle('hidden', !enabled);
            toggleButton.textContent = enabled ? 'Desactivar Modo Edición' : 'Activar Modo Edición';
            clearFieldErrors();

            editors.forEach((editor) => {
                editor.input.hidden = !enabled;
                editor.errorNode.hidden = !enabled;
                editor.node.classList.toggle('ring-2', enabled);
                editor.node.classList.toggle('ring-indigo-300', enabled);
                editor.node.classList.toggle('rounded-md', enabled);
            });

            setFeedback(enabled ? 'Editá los campos marcados y guardá para aplicar cambios.' : '');
        };

        const saveChanges = async () => {
            const fields = {};
            const validationErrors = {};
            clearFieldErrors();

            editors.forEach((editor) => {
                const nextValue = editor.input.value.trim();
                const validationError = validateInput(editor, nextValue);
                if (validationError !== null) {
                    validationErrors[editor.key] = validationError;
                    return;
                }

                applyValue(editor, nextValue);
                if (nextValue !== editor.originalValue) {
                    fields[editor.key] = nextValue;
                }
            });

            Object.entries(validationErrors).forEach(([key, message]) => setFieldError(key, message));
            if (Object.keys(validationErrors).length > 0) {
                rollback();
                setFeedback('Hay campos con errores. Corregilos antes de guardar.', 'error');
                return;
            }

            if (Object.keys(fields).length === 0) {
                setFeedback('No hay cambios para guardar.');
                return;
            }

            saveButton?.setAttribute('disabled', 'disabled');

            try {
                const response = await fetch(config.endpoint, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: JSON.stringify({ fields }),
                    credentials: 'same-origin',
                });
                const payload = await response.json();

                const rejected = payload.rejected || {};
                Object.entries(rejected).forEach(([key, reason]) => setFieldError(key, String(reason)));

                if (!response.ok || !payload.ok) {
                    rollback();
                    setFeedback(payload.message || 'No se pudieron guardar los cambios.', 'error');
                    return;
                }

                (payload.updated || []).forEach((key) => {
                    const editor = editors.get(key);
                    if (editor) {
                        editor.originalValue = editor.input.value.trim();
                    }
                });

                setFeedback(payload.message || 'Cambios guardados correctamente.', Object.keys(rejected).length === 0 ? 'success' : 'error');
                if (Object.keys(rejected).length === 0) {
                    setMode(false);
                }
            } catch (error) {
                rollback();
                setFeedback('Error de red al guardar. Se revirtieron los cambios visuales.', 'error');
            } finally {
                saveButton?.removeAttribute('disabled');
            }
        };

        toggleButton.addEventListener('click', () => setMode(!isEditing));
        saveButton?.addEventListener('click', () => void saveChanges());
        cancelButton?.addEventListener('click', () => {
            rollback();
            setMode(false);
        });
    };

    window.PublicPagesInline = {
        init() {
            setupInlineEditMode(window.inlineEditConfig || {});
        },
    };
})();
