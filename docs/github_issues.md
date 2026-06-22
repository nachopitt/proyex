# GitHub Issue Templates: Proyex Features

Below are the feature requests structured exactly according to the template defined in [.github/ISSUE_TEMPLATE/feature_request.md](.github/ISSUE_TEMPLATE/feature_request.md), ordered from 1 to 5 as originally proposed.

---

## 📊 1. Interactive Dashboard & Metrics (Completed)

```markdown
---
name: Feature Request
about: Use the standard feature request format
title: "Feature Request: Interactive Dashboard & Metrics"
labels: ["enhancement"]
assignees: []
---

# Feature Request: Interactive Dashboard & Metrics

## Problem
The dashboard screen (`Dashboard.vue`) was an empty template showing only static placeholder pattern boxes. This left users with no visual, centralized overview of their workspace projects, milestones, overdue deadlines, and recent progress updates.

## Use Case
- **Scenario 1**: A user lands on the dashboard and wants to immediately check their total project completion rate, count of active projects, and see if any tasks are currently overdue.
- **Scenario 2**: A team member wants to scan a timeline feed of recent workspace activity (progress logs) to see what other updates have been submitted.

## Requirements
- Query backend database metrics for top-level projects, subtasks, overdue tasks, and completion percentages.
- Display KPI counters (Active Projects, Completed Projects, Overdue Tasks, Subtasks).
- Display a dynamic circular SVG dial showing the overall completion rate.
- Display status distribution list and priority profile percentage bars.
- Display a list of the top 5 upcoming project deadlines.
- Display a timeline feed showing the last 5 workspace progress updates.

## Acceptance Criteria
- [ ] The `/dashboard` route queries live database aggregates and returns them to the Inertia page view.
- [ ] KPI counters match the database status and are formatted correctly.
- [ ] The SVG circular progress stroke adjusts dynamically based on the completion rate.
- [ ] The "Upcoming Deadlines" feed lists active projects with future due dates.
- [ ] The "Workspace Activity" timeline renders log descriptions, authors, and project names.

## Definition of Done
- [ ] Create `App\Http\Controllers\DashboardController` and map the route in `routes/web.php`.
- [ ] Overhaul `resources/js/pages/Dashboard.vue` with responsive layouts matching light/dark theme specifications.
- [ ] Write features tests in `tests/Feature/DashboardTest.php` asserting route availability and correct Inertia props payload.
- [ ] Compile assets cleanly under production settings (`npm run build`).
```

---

## 📋 2. Visual Kanban Board View

```markdown
---
name: Feature Request
about: Use the standard feature request format
title: "Feature Request: Visual Kanban Board View"
labels: ["enhancement"]
assignees: []
---

# Feature Request: Visual Kanban Board View

## Problem
Currently, projects and tasks are presented solely in a list/table format in `projects/Index.vue`. While clean, it does not provide an intuitive visual representation of work in progress across status workflows, making it difficult to spot pipeline bottlenecks or easily update project statuses.

## Use Case
- **Scenario 1**: A user wants to quickly scan all projects to see which are "On Hold" versus "In Progress".
- **Scenario 2**: A user wants to move a project from "Planned" to "In Progress" with a single drag-and-drop or column selection without opening the full edit page.

## Requirements
- Add a view toggle (List View vs. Board View) to the projects index page.
- Render five columns corresponding to the status enum values (Planned, In Progress, On Hold, Completed, Cancelled).
- Render project cards displaying key details: Title, Priority, Due Date, Assignee, and Tags.
- Allow updating project status directly from the board view.

## Acceptance Criteria
- [ ] Toggle buttons are visible at the top of the project index page.
- [ ] Column headers reflect status labels from `App\Enums\Status`.
- [ ] Project cards show priority color styling and due date alerts.
- [ ] Dragging or updating a card's status column successfully saves to the database via an Inertia PATCH request.
- [ ] Active search and filters (`ProjectFilter`) apply dynamically to the board cards.

## Definition of Done
- [ ] Frontend code compiled with TypeScript and lint checks passing (`eslint`).
- [ ] State transitions are covered by backend integration tests in `ProjectControllerTest`.
- [ ] Board UI adheres to light/dark themes.
```

---

## 🔄 3. Automated Subtask Progress Rollup & Status Cascade

```markdown
---
name: Feature Request
about: Use the standard feature request format
title: "Feature Request: Automated Subtask Progress Rollup & Status Cascade"
labels: ["enhancement"]
assignees: []
---

# Feature Request: Automated Subtask Progress Rollup & Status Cascade

## Problem
Currently, parent projects and child subtasks have independent progress percentages and statuses. When subtasks are completed or updated, a user has to manually calculate and update the parent project's status and progress, which scales poorly and leads to data inconsistencies.

## Use Case
- **Scenario 1**: A user has a parent project "Website Redesign" containing 4 subtasks. As they mark each subtask as completed (increasing its progress to 100%), the parent project's completion progress updates automatically.
- **Scenario 2**: The parent project status automatically transitions to "In Progress" as soon as a user starts work (progress > 0% or status changed to In Progress) on any of its subtasks.

## Requirements
- Listen to progress and status changes on child projects (subtasks).
- Calculate and update the parent's `current_progress_percentage` dynamically when a child changes.
- Cascade status updates from subtasks up to the parent.
- Log automatically generated progress updates to tracking history.

## Acceptance Criteria
- [ ] Changing a subtask's progress updates the parent project's progress to the average progress of all its subtasks.
- [ ] If parent has no subtasks, progress remains directly editable.
- [ ] If any subtask transitions to `in-progress`, the parent transitions to `in-progress` (unless already completed).
- [ ] If all subtasks transition to `completed`, the parent transitions to `completed` and sets progress to 100%.
- [ ] Automated progress changes generate a `ProjectUpdate` history log.

## Definition of Done
- [ ] Backend logic implemented using a `ProjectObserver` or model event listeners.
- [ ] Automated rollup logic and status cascading are covered by unit and integration tests.
- [ ] Circular dependency check prevents recursive calculation errors.
```

---

## 📅 4. Gantt Timeline / Calendar Scheduler

```markdown
---
name: Feature Request
about: Use the standard feature request format
title: "Feature Request: Gantt Timeline / Calendar Scheduler"
labels: ["enhancement"]
assignees: []
---

# Feature Request: Gantt Timeline / Calendar Scheduler

## Problem
Projects contain start dates and due dates, but there is no calendar or timeline visualization tool. This makes it difficult for users to see timeline overlaps, identify deadline conflicts, or schedule tasks chronologically.

## Use Case
- **Scenario 1**: A planner wants to review start and due dates of all child projects to make sure they fit inside the parent project's timeline window.
- **Scenario 2**: A user wants to visualize their monthly task distribution on a grid layout.

## Requirements
- Create a visual timeline component rendering a calendar timeline grid (weekly/monthly increments).
- Display projects with valid `start_date` and `due_date` as horizontal blocks.
- Highlight overdue tasks (due date in the past and status is not completed).
- Display detailed tooltip information on project hover.

## Acceptance Criteria
- [ ] A timeline view is accessible from the projects index page.
- [ ] Horizontal project duration bars map accurately from their start date to their due date.
- [ ] Color styling represents project priority or current status.
- [ ] Overdue items display warning states (e.g. red dashed outlines or alert icons).

## Definition of Done
- [ ] Calendar grid renders correctly across both mobile and desktop viewports.
- [ ] UI successfully built under production parameters (`npm run build`).
```

---

## ⌨️ 5. Global Spotlight Command Palette (Ctrl+K)

```markdown
---
name: Feature Request
about: Use the standard feature request format
title: "Feature Request: Global Spotlight Command Palette (Ctrl+K)"
labels: ["enhancement"]
assignees: []
---

# Feature Request: Global Spotlight Command Palette (Ctrl+K)

## Problem
Navigating the application requires clicking through multiple sidebar options and loading index list pages, which is slow for keyboard-centric power users.

## Use Case
- **Scenario 1**: A user wants to press `Ctrl + K` from anywhere in the app to search and quickly jump directly to a project's details page.
- **Scenario 2**: A user wants to trigger a workflow (like "Create New Project" or "Toggle Dark Mode") using keyboard navigation.

## Requirements
- Global keyboard listener for `Ctrl + K` and `Cmd + K`.
- Overlay spotlight modal showing instant results as the user types.
- Support full keyboard navigation.
- Implement quick actions and page navigation routes.

## Acceptance Criteria
- [ ] Spotlight modal toggles overlay display on `Ctrl + K` or `Cmd + K`.
- [ ] Real-time search queries projects and tags.
- [ ] Command list supports quick actions: "Create Project", "Switch Theme", "Navigate home".
- [ ] Keyboard Arrow Up/Down and Enter keys allow full navigation and select actions.

## Definition of Done
- [ ] Command palette styles adhere to light/dark themes.
- [ ] Dialog meets modern accessibility standards (ARIA attributes and focus locking).
- [ ] Build compiles cleanly without TypeScript errors.
```

---

## 🧹 6. Remove Laravel Starter Kit Leftovers

```markdown
---
name: Feature Request
about: Use the standard feature request format
title: "Feature Request: Remove Laravel Starter Kit Leftovers"
labels: ["refactor", "housekeeping"]
assignees: []
---

# Feature Request: Remove Laravel Starter Kit Leftovers

## Problem
The application was scaffolded using a Laravel Starter Kit, which introduces multiple boilerplate files, placeholder components, default logos, and unused routes. Leaving them in the codebase increases visual noise, increases the project's payload, and poses a potential security risk (e.g. active registration routes when public registration is intended to be disabled).

## Use Case
- **Scenario 1**: A developer is editing frontend pages and wants to only see components and page views that are actively used by Proyex, without wading through boilerplate code (like unused layouts or default logos).
- **Scenario 2**: An administrator wants to ensure that unused authentication routes (like user self-registration) are fully disabled at the routing level to avoid exploit attempts.

## Requirements
- Identify and remove unused starter kit views, layouts, and components.
- Replace the boilerplate `Welcome.vue` screen with a custom landing page for Proyex.
- Audit and disable/clean up unneeded auth routes (like registration, if public registration is disabled).
- Delete unused placeholder components (like `PlaceholderPattern.vue` if no longer used).

## Acceptance Criteria
- [ ] Clean up `routes/auth.php` to remove or comment out unused routes (such as user self-registration).
- [ ] Replace default Laravel marketing text in `Welcome.vue` with a custom, branded landing page for Proyex.
- [ ] Delete unused placeholder layouts and components that are not imported by active views.
- [ ] Verify database seeders (`DatabaseSeeder.php`, `UserSeeder.php`) don't contain default sample admin accounts that shouldn't be deployed.

## Definition of Done
- [ ] The application compiles cleanly with no missing asset/component import errors.
- [ ] Linter check passes with zero errors (`npm run lint`).
- [ ] All automated feature and unit tests pass successfully.
```

