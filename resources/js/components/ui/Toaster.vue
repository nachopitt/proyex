<script setup lang="ts">
import { type ToastType, useToast } from '@/composables/useToast';
import { usePage } from '@inertiajs/vue3';
import { AlertTriangle, CheckCircle2, Info, X, XCircle } from 'lucide-vue-next';
import { watch } from 'vue';

const { toasts, addToast, removeToast } = useToast();
const page = usePage();

// Map each flash key to a toast type. 'status' uses info styling.
const flashTypeMap: Record<string, ToastType> = {
    success: 'success',
    error: 'error',
    warning: 'warning',
    info: 'info',
    status: 'info',
};

// Watch page.props.flash on every Inertia navigation and fire toasts.
// The deep flag ensures nested changes are caught; immediate is false so the
// initial SSR-rendered page does not re-fire toasts on hydration.
watch(
    () => page.props.flash as Record<string, string | null | undefined>,
    (flash) => {
        if (!flash) return;
        for (const [key, message] of Object.entries(flash)) {
            if (message && flashTypeMap[key]) {
                addToast(message, flashTypeMap[key]);
            }
        }
    },
    { deep: true },
);

const iconMap = {
    success: CheckCircle2,
    error: XCircle,
    warning: AlertTriangle,
    info: Info,
};

const styleMap: Record<ToastType, { border: string; bg: string; icon: string; text: string }> = {
    success: {
        border: 'border-emerald-500/40',
        bg: 'bg-emerald-500/10',
        icon: 'text-emerald-500',
        text: 'text-emerald-700 dark:text-emerald-400',
    },
    error: {
        border: 'border-rose-500/40',
        bg: 'bg-rose-500/10',
        icon: 'text-rose-500',
        text: 'text-rose-700 dark:text-rose-400',
    },
    warning: {
        border: 'border-amber-500/40',
        bg: 'bg-amber-500/10',
        icon: 'text-amber-500',
        text: 'text-amber-700 dark:text-amber-400',
    },
    info: {
        border: 'border-sky-500/40',
        bg: 'bg-sky-500/10',
        icon: 'text-sky-500',
        text: 'text-sky-700 dark:text-sky-400',
    },
};
</script>

<template>
    <!-- Fixed overlay at top-right, above all page content -->
    <div
        class="pointer-events-none fixed top-4 right-4 z-50 flex w-80 flex-col gap-2"
        aria-live="polite"
        aria-label="Notifications"
    >
        <TransitionGroup
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="translate-x-full opacity-0"
            enter-to-class="translate-x-0 opacity-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="translate-x-0 opacity-100"
            leave-to-class="translate-x-full opacity-0"
            move-class="transition duration-200"
        >
            <div
                v-for="toast in toasts"
                :key="toast.id"
                class="pointer-events-auto flex items-start gap-3 rounded-xl border px-4 py-3 shadow-lg backdrop-blur-sm"
                :class="[styleMap[toast.type].border, styleMap[toast.type].bg]"
                role="alert"
            >
                <!-- Icon -->
                <component
                    :is="iconMap[toast.type]"
                    class="mt-0.5 size-5 shrink-0"
                    :class="styleMap[toast.type].icon"
                    aria-hidden="true"
                />

                <!-- Message -->
                <p class="flex-1 text-sm font-medium" :class="styleMap[toast.type].text">
                    {{ toast.message }}
                </p>

                <!-- Dismiss button -->
                <button
                    type="button"
                    class="shrink-0 rounded-md p-0.5 opacity-60 transition-opacity hover:opacity-100 focus:outline-none focus:ring-2 focus:ring-current"
                    :class="styleMap[toast.type].text"
                    :aria-label="`Dismiss notification: ${toast.message}`"
                    @click="removeToast(toast.id)"
                >
                    <X class="size-4" />
                </button>
            </div>
        </TransitionGroup>
    </div>
</template>
