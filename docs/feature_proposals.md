# Proyex Feature Proposals

We analyzed the current codebase of **Proyex** and found a solid foundation:
- **Backend Stack**: Laravel 12, PHP 8.4, MariaDB/MySQL database.
- **Frontend Stack**: Vue 3, Inertia.js, Tailwind CSS v4, and Lucide icons.
- **Data Models**: `User`, `Project` (supporting parent-child hierarchy, start/due dates, priority, status), `ProjectUpdate` (progress tracking logs), and `Tag`.

Here are five next-level feature proposals to enhance Proyex, ranging from visual upgrades to workflow automation.

---

## Comparison of Proposed Features

| Feature Proposal | User Experience Value | Visual Impact | Backend Complexity | Frontend Complexity | Key Files Involved |
| :--- | :---: | :---: | :---: | :---: | :--- |
| **1. Interactive Dashboard & Metrics** | ⭐⭐⭐⭐⭐ | 🟢 High | 🟡 Medium | 🟡 Medium | [Dashboard.vue](file:///home/nachopitt/proyex/resources/js/pages/Dashboard.vue), routes, new Stats controller |
| **2. Visual Kanban Board View** | ⭐⭐⭐⭐⭐ | 🟢 High | 🔴 Low | 🔴 High | [Index.vue](file:///home/nachopitt/proyex/resources/js/pages/projects/Index.vue), Kanban subcomponent |
| **3. Automated Subtask Progress Rollup** | ⭐⭐⭐⭐ | 🔴 Low | 🟢 High | 🔴 Low | [Project.php](file:///home/nachopitt/proyex/app/Models/Project.php), `ProjectUpdate` observer/events |
| **4. Gantt Timeline Scheduler** | ⭐⭐⭐⭐ | 🟢 High | 🔴 Low | 🔴 High | Timeline component, [Index.vue](file:///home/nachopitt/proyex/resources/js/pages/projects/Index.vue) |
| **5. Global Command Palette (`Ctrl+K`)** | ⭐⭐⭐⭐ | 🟡 Medium | 🔴 Low | 🟡 Medium | App Layout, spotlight search component |

---

## Detailed Proposals

### 📊 1. Interactive Dashboard & Metrics
Currently, `Dashboard.vue` is a shell rendering static placeholder cards. Replacing it with a dynamic, data-driven dashboard will immediately give Proyex a polished, premium feel.

*   **Key Features**:
    *   **KPI Cards**: Counters for active projects, subtasks, overdue tasks, and tasks due today/this week.
    *   **Dynamic Progress**: Custom SVG-based circular progress dials or progress bars showing overall portfolio completion.
    *   **Priority & Status Breakdown**: Charts (e.g. donut/bar layout) dividing tasks by priority levels (`High`, `Medium`, `Low`) and status (`Backlog`, `In Progress`, etc.).
    *   **Recent Updates Feed**: Live feed showing the last 5 project progress logs across all projects.
    *   **Upcoming Deadlines**: A list of projects with due dates in the next 7 days, with quick links to their pages.

---

### 📋 2. Visual Kanban Board View
A Kanban board is a standard and highly requested view for task managers. Since Proyex already models project status (`Backlog`, `In Progress`, `Completed`, etc.), a board view is the perfect fit.

*   **Key Features**:
    *   Columns representing project statuses.
    *   Project cards showing title, priority, due date, assignee, and tags.
    *   Interactive state updates (e.g. clicking buttons on cards or dragging them between columns to trigger Inertia PATCH requests).
    *   Quick filters by assignee, priority, or tag directly on the board.

---

### 🔄 3. Automated Subtask Progress Rollup & Status Cascade
Since Proyex supports parent-child project hierarchies (subtasks/milestones), manual progress calculation becomes tedious when projects scale.

*   **Key Features**:
    *   **Auto-Calculated Progress**: When a child task progress is updated, the parent project's `current_progress_percentage` automatically recalculates as the weighted average of its children.
    *   **Status Cascading**: 
        *   If any subtask changes to `In Progress`, the parent automatically updates to `In Progress`.
        *   If all subtasks are `Completed`, the parent automatically transitions to `Completed`.
    *   **Logs**: Automatic system-generated `ProjectUpdate` logs detailing the recalculation.

---

### 📅 4. Gantt / Timeline Scheduler
Projects have `start_date` and `due_date` columns, but they are currently only displayed as text fields. A calendar/timeline scheduler lets users visualize overlap and workloads.

*   **Key Features**:
    *   A grid-based calendar or weekly/monthly timeline timeline.
    *   Horizontal project bars mapped from `start_date` to `due_date`.
    *   Visual indicators of overdue projects (past due date, not completed).
    *   Group projects by assignee to see who is overloaded.

---

### ⌨️ 5. Global Command Palette (`Ctrl+K`)
Add a spotlight search and quick-actions interface to enable high-efficiency navigation.

*   **Key Features**:
    *   Global keyboard listener for `Ctrl + K` or `Cmd + K`.
    *   Search projects, tags, and subtasks instantly.
    *   Quick Actions: "Create Project", "Add Tag", "Log Progress Update", "View My Tasks".
    *   Keyboard navigation (arrows and Enter) for rapid workflow execution.

---

> [!TIP]
> **Recommended Path**: Since the **Interactive Dashboard** is currently a blank canvas in the code, implementing it first will yield the highest visual impact and tie together the existing project list, updates, and user assignments beautifully.
