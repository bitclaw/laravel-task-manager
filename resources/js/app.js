import './bootstrap';

const reorderList = document.querySelector('[data-task-reorder-list]');

if (reorderList) {
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute('content');

    let draggedItem = null;
    let isPersisting = false;

    const syncPriorityBadges = () => {
        reorderList.querySelectorAll('[data-task-item]').forEach((item, index) => {
            const badge = item.querySelector('[data-priority-badge]');

            if (badge) {
                badge.textContent = `#${index + 1}`;
            }
        });
    };

    const taskIds = () =>
        Array.from(reorderList.querySelectorAll('[data-task-item]')).map((item) =>
            Number(item.dataset.taskId),
        );

    const persistOrder = async () => {
        if (isPersisting) {
            return;
        }

        isPersisting = true;

        try {
            await window.axios.post(
                reorderList.dataset.reorderEndpoint,
                {
                    project_id: reorderList.dataset.projectId || null,
                    task_ids: taskIds(),
                },
                {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                    },
                },
            );
        } catch (error) {
            window.alert('Could not save the new task order. The page will reload.');
            window.location.reload();
        } finally {
            isPersisting = false;
        }
    };

    const dragAfterElement = (container, y) => {
        const draggableItems = [
            ...container.querySelectorAll('[data-task-item]:not(.is-dragging)'),
        ];

        return draggableItems.reduce(
            (closest, child) => {
                const box = child.getBoundingClientRect();
                const offset = y - box.top - box.height / 2;

                if (offset < 0 && offset > closest.offset) {
                    return { offset, element: child };
                }

                return closest;
            },
            { offset: Number.NEGATIVE_INFINITY, element: null },
        ).element;
    };

    reorderList.querySelectorAll('[data-task-item]').forEach((item) => {
        item.addEventListener('dragstart', () => {
            draggedItem = item;
            item.classList.add('is-dragging', 'opacity-60', 'ring-2', 'ring-amber-400');
        });

        item.addEventListener('dragend', async () => {
            item.classList.remove('is-dragging', 'opacity-60', 'ring-2', 'ring-amber-400');
            draggedItem = null;
            syncPriorityBadges();
            await persistOrder();
        });
    });

    reorderList.addEventListener('dragover', (event) => {
        event.preventDefault();

        if (!draggedItem) {
            return;
        }

        const nextItem = dragAfterElement(reorderList, event.clientY);

        if (!nextItem) {
            reorderList.appendChild(draggedItem);
            return;
        }

        reorderList.insertBefore(draggedItem, nextItem);
    });
}
