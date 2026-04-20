<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-stone-100">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel Task Manager') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="h-full bg-[radial-gradient(circle_at_top,_rgba(251,191,36,0.18),_transparent_30%),linear-gradient(180deg,_#f7f4ed_0%,_#f5f5f4_45%,_#e7e5e4_100%)] text-stone-900">
        <div class="min-h-full">
            <nav class="border-b border-stone-200/80 bg-white/90 backdrop-blur">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex size-10 items-center justify-center rounded-2xl bg-amber-500 text-sm font-bold text-white shadow-sm">
                                TM
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-stone-900">Task Manager</p>
                                <p class="text-xs text-stone-500">Laravel 12 + MySQL</p>
                            </div>
                        </div>

                        <div class="hidden items-center gap-3 sm:flex">
                            <span class="inline-flex items-center rounded-full bg-stone-100 px-3 py-1 text-xs font-medium text-stone-700 ring-1 ring-inset ring-stone-200">
                                {{ $projects->count() }} {{ \Illuminate\Support\Str::plural('project', $projects->count()) }}
                            </span>
                            <span class="inline-flex items-center rounded-full bg-amber-50 px-3 py-1 text-xs font-medium text-amber-700 ring-1 ring-inset ring-amber-200">
                                {{ $tasks->count() }} visible {{ \Illuminate\Support\Str::plural('task', $tasks->count()) }}
                            </span>
                        </div>
                    </div>
                </div>
            </nav>

            <main>
                <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
                    <div class="lg:flex lg:items-center lg:justify-between">
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium uppercase tracking-[0.2em] text-amber-600">Assessment Build</p>
                            <h1 class="mt-2 text-3xl font-bold tracking-tight text-stone-900 sm:text-4xl">Task management without the fluff</h1>
                            <p class="mt-3 max-w-3xl text-sm leading-6 text-stone-600 sm:text-base">
                                Create tasks, organize them by project, and keep priorities explicit. Drag-and-drop reordering will slot into this screen next.
                            </p>
                        </div>

                        <div class="mt-6 flex flex-wrap gap-3 lg:mt-0 lg:ml-6">
                            <span class="inline-flex items-center rounded-full bg-white px-4 py-2 text-sm font-medium text-stone-700 shadow-sm ring-1 ring-inset ring-stone-200">
                                Priority #1 stays at the top
                            </span>
                            @if ($selectedProjectId)
                                <span class="inline-flex items-center rounded-full bg-amber-500 px-4 py-2 text-sm font-semibold text-white shadow-sm">
                                    Filtered view
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="mt-8 space-y-4">
                        @if (session('status'))
                            <div class="rounded-md bg-emerald-50 p-4 ring-1 ring-inset ring-emerald-200">
                                <div class="flex items-start gap-3">
                                    <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" class="mt-0.5 size-5 text-emerald-500">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                                    </svg>
                                    <div>
                                        <h2 class="text-sm font-semibold text-emerald-900">Saved</h2>
                                        <p class="mt-1 text-sm text-emerald-800">{{ session('status') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="rounded-md bg-rose-50 p-4 ring-1 ring-inset ring-rose-200">
                                <div class="flex items-start gap-3">
                                    <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" class="mt-0.5 size-5 text-rose-500">
                                        <path fill-rule="evenodd" d="M18 10A8 8 0 1 1 2 10a8 8 0 0 1 16 0Zm-8.75-3.25a.75.75 0 0 1 1.5 0v3a.75.75 0 0 1-1.5 0v-3ZM10 13a.875.875 0 1 0 0 1.75A.875.875 0 0 0 10 13Z" clip-rule="evenodd" />
                                    </svg>
                                    <div>
                                        <h2 class="text-sm font-semibold text-rose-900">Please fix the following</h2>
                                        <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-rose-800">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="mt-10 grid grid-cols-1 gap-6 xl:grid-cols-3">
                        <section class="rounded-3xl bg-white/90 p-6 shadow-sm ring-1 ring-inset ring-stone-200 xl:col-span-1">
                            <div class="border-b border-stone-200 pb-5">
                                <h2 class="text-lg font-semibold text-stone-900">Create task</h2>
                                <p class="mt-1 text-sm text-stone-600">New tasks are appended to the bottom of the selected project.</p>
                            </div>

                            <form action="{{ route('tasks.store') }}" method="POST" class="mt-6 space-y-5">
                                @csrf

                                <div>
                                    <label for="task_name" class="block text-sm font-medium text-stone-900">Task name</label>
                                    <input
                                        id="task_name"
                                        type="text"
                                        name="name"
                                        value="{{ old('name') }}"
                                        required
                                        class="mt-2 block w-full rounded-xl border-0 bg-white px-4 py-3 text-sm text-stone-900 shadow-sm ring-1 ring-inset ring-stone-300 placeholder:text-stone-400 focus:ring-2 focus:ring-inset focus:ring-amber-500"
                                        placeholder="Prepare deployment checklist"
                                    />
                                </div>

                                <div>
                                    <label for="task_project_id" class="block text-sm font-medium text-stone-900">Project</label>
                                    <select
                                        id="task_project_id"
                                        name="project_id"
                                        class="mt-2 block w-full rounded-xl border-0 bg-white px-4 py-3 text-sm text-stone-900 shadow-sm ring-1 ring-inset ring-stone-300 focus:ring-2 focus:ring-inset focus:ring-amber-500"
                                    >
                                        <option value="">No project</option>
                                        @foreach ($projects as $project)
                                            <option value="{{ $project->id }}" @selected((string) old('project_id', $selectedProjectId) === (string) $project->id)>
                                                {{ $project->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <button
                                    type="submit"
                                    class="inline-flex w-full items-center justify-center rounded-xl bg-stone-900 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-stone-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-stone-900"
                                >
                                    Add task
                                </button>
                            </form>
                        </section>

                        <section class="rounded-3xl bg-white/90 p-6 shadow-sm ring-1 ring-inset ring-stone-200 xl:col-span-1">
                            <div class="border-b border-stone-200 pb-5">
                                <h2 class="text-lg font-semibold text-stone-900">Create project</h2>
                                <p class="mt-1 text-sm text-stone-600">Use projects to isolate related task lists.</p>
                            </div>

                            <form action="{{ route('projects.store') }}" method="POST" class="mt-6 space-y-5">
                                @csrf

                                <div>
                                    <label for="project_name" class="block text-sm font-medium text-stone-900">Project name</label>
                                    <input
                                        id="project_name"
                                        type="text"
                                        name="name"
                                        value="{{ old('name') }}"
                                        required
                                        class="mt-2 block w-full rounded-xl border-0 bg-white px-4 py-3 text-sm text-stone-900 shadow-sm ring-1 ring-inset ring-stone-300 placeholder:text-stone-400 focus:ring-2 focus:ring-inset focus:ring-amber-500"
                                        placeholder="Website refresh"
                                    />
                                </div>

                                <button
                                    type="submit"
                                    class="inline-flex w-full items-center justify-center rounded-xl bg-amber-500 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-amber-400 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-amber-500"
                                >
                                    Add project
                                </button>
                            </form>
                        </section>

                        <section class="rounded-3xl bg-stone-900 p-6 text-white shadow-sm ring-1 ring-inset ring-stone-800 xl:col-span-1">
                            <div class="border-b border-white/10 pb-5">
                                <h2 class="text-lg font-semibold">Filter view</h2>
                                <p class="mt-1 text-sm text-stone-300">Limit the list to a single project or show everything.</p>
                            </div>

                            <form action="{{ route('tasks.index') }}" method="GET" class="mt-6 space-y-5">
                                <div>
                                    <label for="project_filter" class="block text-sm font-medium text-white">Project filter</label>
                                    <select
                                        id="project_filter"
                                        name="project"
                                        class="mt-2 block w-full rounded-xl border-0 bg-white/10 px-4 py-3 text-sm text-white shadow-sm ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-amber-400"
                                    >
                                        <option value="" @selected(!$selectedProjectId)>All tasks</option>
                                        @foreach ($projects as $project)
                                            <option value="{{ $project->id }}" @selected((string) $selectedProjectId === (string) $project->id)>
                                                {{ $project->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="flex gap-3">
                                    <button
                                        type="submit"
                                        class="inline-flex flex-1 items-center justify-center rounded-xl bg-white px-4 py-3 text-sm font-semibold text-stone-900 shadow-sm transition hover:bg-stone-100"
                                    >
                                        Apply filter
                                    </button>
                                    <a
                                        href="{{ route('tasks.index') }}"
                                        class="inline-flex items-center justify-center rounded-xl border border-white/15 px-4 py-3 text-sm font-semibold text-white transition hover:bg-white/10"
                                    >
                                        Reset
                                    </a>
                                </div>

                                <dl class="grid grid-cols-2 gap-4 pt-2">
                                    <div class="rounded-2xl bg-white/5 p-4 ring-1 ring-inset ring-white/10">
                                        <dt class="text-xs uppercase tracking-wide text-stone-400">Visible tasks</dt>
                                        <dd class="mt-2 text-2xl font-semibold">{{ $tasks->count() }}</dd>
                                    </div>
                                    <div class="rounded-2xl bg-white/5 p-4 ring-1 ring-inset ring-white/10">
                                        <dt class="text-xs uppercase tracking-wide text-stone-400">Scope</dt>
                                        <dd class="mt-2 text-sm font-semibold">
                                            {{ $selectedProjectId ? 'One project' : 'All projects' }}
                                        </dd>
                                    </div>
                                </dl>
                            </form>
                        </section>
                    </div>

                    <section class="mt-8 rounded-3xl bg-white/75 p-6 shadow-sm ring-1 ring-inset ring-stone-200 backdrop-blur">
                        <div class="flex flex-col gap-4 border-b border-stone-200 pb-6 sm:flex-row sm:items-end sm:justify-between">
                            <div>
                                <h2 class="text-xl font-semibold text-stone-900">Task list</h2>
                                <p class="mt-1 text-sm text-stone-600">
                                    @if ($selectedProjectId)
                                        Showing tasks for the selected project only.
                                    @else
                                        Showing all tasks across every project, ordered by priority.
                                    @endif
                                </p>
                            </div>

                            <div class="flex flex-wrap gap-2">
                                <span class="inline-flex items-center rounded-full bg-stone-100 px-3 py-1 text-xs font-medium text-stone-700 ring-1 ring-inset ring-stone-200">
                                    Priorities are sequential per project
                                </span>
                            </div>
                        </div>

                        @if ($tasks->isEmpty())
                            <div class="mt-6 rounded-3xl border border-dashed border-stone-300 bg-stone-50 px-6 py-14 text-center">
                                <div class="mx-auto flex size-14 items-center justify-center rounded-full bg-amber-100 text-amber-700">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true" class="size-7">
                                        <path d="M12 6v12m6-6H6" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </div>
                                <h3 class="mt-4 text-lg font-semibold text-stone-900">No tasks yet</h3>
                                <p class="mt-2 text-sm text-stone-600">Create your first task from the panel above to populate this list.</p>
                            </div>
                        @else
                            <ul role="list" class="mt-6 space-y-4">
                                @foreach ($tasks as $task)
                                    <li class="overflow-hidden rounded-3xl bg-white px-5 py-5 shadow-sm ring-1 ring-inset ring-stone-200">
                                        <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                                            <div class="min-w-0 flex-1">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <span class="inline-flex items-center rounded-full bg-amber-500 px-2.5 py-1 text-xs font-semibold text-white">
                                                        #{{ $task->priority }}
                                                    </span>

                                                    @if ($task->project)
                                                        <span class="inline-flex items-center rounded-full bg-stone-100 px-2.5 py-1 text-xs font-medium text-stone-700 ring-1 ring-inset ring-stone-200">
                                                            {{ $task->project->name }}
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center rounded-full bg-stone-100 px-2.5 py-1 text-xs font-medium text-stone-500 ring-1 ring-inset ring-stone-200">
                                                            No project
                                                        </span>
                                                    @endif
                                                </div>

                                                <p class="mt-4 text-lg font-semibold text-stone-900">{{ $task->name }}</p>
                                                <p class="mt-1 text-sm text-stone-500">
                                                    Created {{ $task->created_at->diffForHumans() }}
                                                    @if (!$task->created_at->equalTo($task->updated_at))
                                                        · Updated {{ $task->updated_at->diffForHumans() }}
                                                    @endif
                                                </p>
                                            </div>

                                            <div class="w-full lg:max-w-xl space-y-3">
                                                <form action="{{ route('tasks.update', $task) }}" method="POST" class="rounded-2xl bg-stone-50 p-4 ring-1 ring-inset ring-stone-200">
                                                    @csrf
                                                    @method('PUT')

                                                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-5">
                                                        <div class="sm:col-span-3">
                                                            <label for="task_name_{{ $task->id }}" class="block text-sm font-medium text-stone-900">Edit task</label>
                                                            <input
                                                                id="task_name_{{ $task->id }}"
                                                                type="text"
                                                                name="name"
                                                                value="{{ old('name', $task->name) }}"
                                                                required
                                                                class="mt-2 block w-full rounded-xl border-0 bg-white px-4 py-2.5 text-sm text-stone-900 shadow-sm ring-1 ring-inset ring-stone-300 focus:ring-2 focus:ring-inset focus:ring-amber-500"
                                                            />
                                                        </div>

                                                        <div class="sm:col-span-2">
                                                            <label for="task_project_{{ $task->id }}" class="block text-sm font-medium text-stone-900">Project</label>
                                                            <select
                                                                id="task_project_{{ $task->id }}"
                                                                name="project_id"
                                                                class="mt-2 block w-full rounded-xl border-0 bg-white px-4 py-2.5 text-sm text-stone-900 shadow-sm ring-1 ring-inset ring-stone-300 focus:ring-2 focus:ring-inset focus:ring-amber-500"
                                                            >
                                                                <option value="">No project</option>
                                                                @foreach ($projects as $project)
                                                                    <option value="{{ $project->id }}" @selected((string) old('project_id', $task->project_id) === (string) $project->id)>
                                                                        {{ $project->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="mt-4 flex flex-wrap items-center justify-end gap-3">
                                                        <button
                                                            type="submit"
                                                            class="inline-flex items-center rounded-xl bg-stone-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-stone-700"
                                                        >
                                                            Save changes
                                                        </button>
                                                    </div>
                                                </form>

                                                <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="flex justify-end">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button
                                                        type="submit"
                                                        class="inline-flex items-center rounded-xl bg-rose-50 px-4 py-2.5 text-sm font-semibold text-rose-700 ring-1 ring-inset ring-rose-200 transition hover:bg-rose-100"
                                                    >
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </section>
                </div>
            </main>
        </div>
    </body>
</html>
