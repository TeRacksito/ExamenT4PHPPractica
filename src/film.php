<?php
include "top.php";
require_once "QueryBuilder.php";

$qb = new QueryBuilder();
?>
<!--
<div class="alert alert-success">¡Ejemplo mensaje de éxito!</div>
<div class="alert alert-error">¡Ejemplo mensaje de error!</div>
-->

<section id="films">
  <h2>Peliculas</h2>
  <form action="film.php" method="get">

    <?php if (!empty($_GET['delete'])) {
      $category_id = $_GET['category'] ?? null;

      if (!$category_id) { ?>
        <div class="alert alert-error">No hay categoría seleccionada. Para eliminar una categoría, primero debes seleccionarla.</div>
        <?php } else {
        try {
          $success = $qb->execute("DELETE FROM category WHERE category_id = :category_id", [':category_id' => $category_id]);
          if ($success) { ?>
            <div class="alert alert-success">La categoría se ha eliminado correctamente</div>
          <?php } else { ?>
            <div class="alert alert-error">Ha ocurrido un error al eliminar la categoría</div>
          <?php }
        } catch (PDOException $e) { ?>
          <div class="alert alert-error">Ha ocurrido un error al eliminar la categoría. Asegúrate de que no hay películas en esta categoría.
            <a href="film.php?search=1&category=<?= $category_id ?>">Ver películas en esta categoría.</a>
          </div>
    <?php }
      }
    } ?>

    <fieldset>
      <legend>Categorías</legend>
      <select name="category" id="">
        <option selected disabled>Elige una categoría</option>

        <?php
        $query = "SELECT category_id, name FROM category";
        foreach ($qb->fetchIterator($query) as $category): ?>
          <option value='<?= $category->category_id ?>'><?= $category->name ?></option>
        <?php endforeach; ?>
      </select>
      <input type="submit" name="search" value="buscar">
      <input type="submit" name="delete" value="eliminar">
    </fieldset>
  </form>
  <nav>
    <fieldset>
      <legend>Acciones</legend>
      <a href="create.php">
        <button>Crear Categoria</button>
      </a>
    </fieldset>
  </nav>

  <?php if (!empty($_GET['search'])) : ?>

    <?php
    $category_id = $_GET['category'] ?? null;
    $query = "SELECT f.film_id, f.title, f.release_year, f.length
                  FROM film f
                  INNER JOIN film_category fc ON f.film_id = fc.film_id
                  WHERE fc.category_id = :category_id";

    $films = $qb->fetchAll($query, [':category_id' => $category_id]);

    if (empty($films)) { ?>
      <div class="alert alert-error">No hay películas en esta categoría</div>
    <?php } else { ?>
      <table>
        <thead>
          <tr>
            <th>Título</th>
            <th>Año</th>
            <th>Duración</th>
            <th></th>
          </tr>
        </thead>
        <tbody>

          <?php foreach ($films as $film) : ?>
            <tr>
              <td><?= $film->title ?></td>
              <td class="center"><?= $film->release_year ?></td>
              <td class="center"><?= $film->length ?></td>
              <td class="actions">
                <a class="button" href="category_film.php?film_id=<?= $film->film_id ?>">
                  <button>Cambiar categorías</button>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php } ?>
  <?php endif; ?>
</section>
<?php include "bottom.php"; ?>