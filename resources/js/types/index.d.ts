import { InertiaLinkProps } from '@inertiajs/vue3';
import type { LucideIcon } from 'lucide-vue-next';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    icon?: LucideIcon;
    isActive?: boolean;
}

export type AppPageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    sidebarOpen: boolean;
};

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

export type BreadcrumbItemType = BreadcrumbItem;

export interface ProjectUpdate {
    id: number;
    description: string;
    status: number;
    status_label: string;
    progress_percentage: number;
    updater_user: User;
    project: Project;
}

export interface Project {
    id: number;
    title: string;
    description: string;
    priority: number;
    priority_label: string;
    start_date: string;
    due_date: string;
    end_date: string;
    reporter_user: User;
    assigned_user: User;
    assigned_user_id: number;
    project_updates: ProjectUpdate[];
    tags: Tag[];
}

export interface Priority {
    id: number;
    name: string;
}

export interface Status {
    id: number;
    name: string;
}

export interface Tag {
    id: number;
    name: string;
}
