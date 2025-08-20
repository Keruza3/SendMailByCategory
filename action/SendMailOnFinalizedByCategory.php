<?php

namespace Kanboard\Plugin\SendMailByCategory\Action;

use Kanboard\Action\Base;

/**
 * SendMailOnFinalizedByCategory
 *
 * Dispara al mover una tarea a una columna; si la columna coincide (ej. "Finalizado"),
 * mira la categoría de la tarea y envía emails según un mapeo categoría->emails.
 */
class SendMailOnFinalizedByCategory extends Base
{
    public function getDescription()
    {
        return t('Send email to recipients based on task category when moved to a specific column');
    }

    // Evento compatible: mover tarea de columna (kanboard emite 'task.move.column')
    public function getCompatibleEvents()
    {
        return [
            'task.move.column',
        ];
    }

    // Parámetros configurables desde la UI del proyecto
    public function getActionRequiredParameters()
    {
        return [
            // Nombre EXACTO de la columna destino (ej: "Finalizado")
            'target_column' => t('Target column name (e.g. "Finalizado")'),
        ];
    }

    // Lo que esperamos del evento
    public function getEventRequiredParameters()
    {
        return [
            'task_id',
            'task' => [
                'id',
                'title',
                'project_id',
                'category_id',
                'category_name',
            ],
            // según versión, kanboard manda dst_column_id o column_id
            'column_id',
            'dst_column_id',
        ];
    }

    // CORE: qué hace la acción cuando ocurre el evento
    public function doAction(array $data)
    {
        // 1) Columnas: obtenemos el nombre de la columna destino
        $dstColumnId = $data['dst_column_id'] ?? ($data['column_id'] ?? null);
        if ($dstColumnId === null) {
            return false;
        }

        $column = $this->columnModel->getById($dstColumnId);
        if (empty($column) || empty($column['title'])) {
            return false;
        }

        $targetColumnName = trim($this->getParam('target_column'));
        if ($targetColumnName === '') {
            return false;
        }

        // ¿La columna destino es la que configuramos? Si no, no hacemos nada
        if (mb_strtolower($column['title']) !== mb_strtolower($targetColumnName)) {
            return false;
        }

        // 2) Categoría de la tarea
        $categoryId   = (int) ($data['task']['category_id'] ?? 0);
        $categoryName = (string) ($data['task']['category_name'] ?? '');

        // 3) Mapeo categoría -> lista de emails (EDITÁ ESTO A TU GUSTO)
        // Podés mapear por ID o por nombre. Se evalúan ambos.
        $mapById = [
            // 123 => ['compras@empresa.com', 'direccion@empresa.com'],
        ];

        $mapByName = [
            // 'Compras'    => ['compras@empresa.com'],
            // 'Dirección'  => ['direccion@empresa.com'],
            // 'Sistemas'   => ['sistemas@empresa.com'],
        ];

        $recipients = [];

        if ($categoryId && isset($mapById[$categoryId])) {
            $recipients = array_merge($recipients, (array) $mapById[$categoryId]);
        }

        if (!empty($categoryName) && isset($mapByName[$categoryName])) {
            $recipients = array_merge($recipients, (array) $mapByName[$categoryName]);
        }

        // Si no hay destinatarios configurados para esa categoría, no hacemos nada
        if (empty($recipients)) {
            return false;
        }

        // 4) Armamos el email
        $task      = $this->taskFinderModel->getById($data['task_id']);
        if (empty($task)) {
            return false;
        }

        // URL a la tarea
        // Si tenés KANBOARD_URL definida, la respetamos. Si no, usamos la configuración application_url.
        $base = defined('KANBOARD_URL') && KANBOARD_URL ? KANBOARD_URL : ($this->configModel->get('application_url') ?: '');
        $base = rtrim($base, '/');
        $taskUrl = $base . '/?controller=TaskViewController&action=show&task_id=' . $task['id'];

        $subject = sprintf('[%s] Tarea finalizada por categoría: %s — #%d %s',
            $this->projectModel->getById($task['project_id'])['name'] ?? 'Kanboard',
            $categoryName ?: ('ID ' . $categoryId),
            $task['id'],
            $task['title']
        );

        $lines = [];
        $lines[] = sprintf('Proyecto: %s', $this->projectModel->getById($task['project_id'])['name'] ?? 'N/D');
        $lines[] = sprintf('Tarea: #%d — %s', $task['id'], $task['title']);
        $lines[] = sprintf('Columna: %s', $column['title']);
        $lines[] = sprintf('Categoría: %s', $categoryName ?: ('ID ' . $categoryId));
        $lines[] = sprintf('Asignado a: %s', $this->userModel->getFullname($task['owner_id']));
        $lines[] = '';
        $lines[] = 'Abrir tarea: ' . $taskUrl;

        $body = implode("\n", $lines);

        // 5) Enviar
        $sent = true;
        foreach (array_unique($recipients) as $email) {
            $email = trim($email);
            if ($email !== '') {
                // emailClient->send(dirección, asunto, cuerpo)
                $ok = $this->emailClient->send($email, $subject, $body);
                $sent = $sent && $ok;
            }
        }

        return $sent;
    }

    // Validación simple de parámetros de la acción
    public function hasRequiredCondition()
    {
        return true;
    }
}
