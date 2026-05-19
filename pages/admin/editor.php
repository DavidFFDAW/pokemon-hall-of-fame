<?php
$db = Database::getInstance();
if (get_server_method() === 'POST') {
    $sql = $_POST['sql'] ?? '';
    try {
        $result = $db->query($sql);
        $error = $result->errorInfo();
        if ($error[0] !== '00000') {
            Flash::add('Error executing SQL: ' . $error[2], 'error');
        } else {
            Flash::add('SQL executed successfully.', 'success');
            redirect('/admin/editor');
        }
    } catch (PDOException $e) {
        Flash::add('Error executing SQL: ' . $e->getMessage(), 'error');
        redirect('/admin/editor');
    }
}

$statement = $db->prepare("SELECT name FROM sqlite_master WHERE type='table'");
$statement->execute();

$tables = $statement->fetchAll(PDO::FETCH_COLUMN);
// debug($tables);

?>

<style>
    .sql-editor {
        display: flex;
        gap: 1rem;
    }

    .sql-editor .sql-tables {
        width: 200px;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .sql-editor .sql-tables .sql-table {
        padding: 0.5rem;
        border: 1px solid #ccc;
    }

    .sql-editor .sql-editor-container {
        flex: 1;
    }

    label.label textarea[name="sql"] {
        width: 100%;
        min-height: 180px;
        font-family: monospace;
    }
</style>

<div class="sql-editor">
    <div class="sql-tables box">
        <?php foreach ($tables as $table): ?>
            <div class="sql-table">
                <?= e($table) ?>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="sql-editor-container box">
        <form action="" method="post">
            <label class="label">
                <span>SQL:</span>
                <textarea name="sql"></textarea>
            </label>

            <button type="submit">Ejecutar</button>
        </form>
    </div>
</div>