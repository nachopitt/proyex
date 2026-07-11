import { ref } from 'vue';

export type ToastType = 'success' | 'error' | 'warning' | 'info';

export interface Toast {
    id: number;
    type: ToastType;
    message: string;
}

// Module-level singleton — shared across all component instances.
let nextId = 0;
const toasts = ref<Toast[]>([]);

export function useToast() {
    /**
     * Add a toast notification.
     *
     * @param message  The text to display.
     * @param type     Visual style: 'success' | 'error' | 'warning' | 'info'. Defaults to 'info'.
     * @param duration Auto-dismiss delay in ms. Pass 0 to disable auto-dismiss. Defaults to 4000.
     */
    function addToast(message: string, type: ToastType = 'info', duration = 4000): void {
        const id = ++nextId;
        toasts.value.push({ id, type, message });

        if (duration > 0) {
            setTimeout(() => removeToast(id), duration);
        }
    }

    /**
     * Remove a toast immediately by its id.
     */
    function removeToast(id: number): void {
        toasts.value = toasts.value.filter((t) => t.id !== id);
    }

    return { toasts, addToast, removeToast };
}
