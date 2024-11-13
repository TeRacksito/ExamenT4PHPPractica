<?php
include "top.php";
require_once "QueryBuilder.php";

?>
<section id="create">
    <h2>Nueva categoría</h2>
    <nav>
        <p><a href="film.php">Volver</a></p>
    </nav>

    <?php if (empty($_GET['name'])) { ?>
        <form action="" autocomplete="off">
            <fieldset>
                <legend>Datos de la categoría</legend>
                <label for="name">Nombre</label>
                <input type="text" name="name" id="name" required>
                <p></p>
                <input type="reset" value="Limpiar">
                <input type="submit" value="Crear">
            </fieldset>
        </form>

        <?php } else {
        $name = $_GET['name'];
        $qb = new QueryBuilder();
        $success = $qb->execute("INSERT INTO category (name) VALUES (:name)", [':name' => $name]);

        if ($success) { ?>
            <div class="alert alert-success">La categoría se ha creado correctamente</div>
        <?php } else { ?>
            <div class="alert alert-error">Ha ocurrido un error al crear la categoría</div>
        <?php } ?>
    <?php } ?>
</section>
<?php include "bottom.php"; ?>