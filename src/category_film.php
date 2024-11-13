<?php
include "top.php";
require_once "QueryBuilder.php";

$qb = new QueryBuilder();
?>
<!--
    <div class="alert alert-success">¡Ejemplo mensaje de éxito!</div>
    <div class="alert alert-error">¡Ejemplo mensaje de error!</div>
    -->
<nav>
  <p><a href="film.php">Volver</a></p>
</nav>

<?php if (empty($_GET['film_id'])) { ?>
  <div class="alert alert-error">No se a seleccionado una película</div>
<?php } else { ?>
  <?php
  $film_id = $_GET['film_id'];

  $film = $qb->fetchOne("SELECT * FROM film WHERE film_id = :film_id", [':film_id' => $film_id]);

  if (!$film) { ?>
    <div class="alert alert-error">No se ha encontrado la película</div>
  <?php } else { ?>

    <?php
    if (!empty($_POST['categories'])) {
      try {
        $categories_ids = $_POST['categories'];

        $film_categories = $qb->fetchAll("SELECT category_id
                                        FROM film_category
                                        WHERE film_id = :film_id", [':film_id' => $film_id]);

        $qb->beginTransaction();

        foreach ($film_categories as $film_category) {
          if (!in_array($film_category->category_id, $categories_ids)) {
            $qb->execute(
              "DELETE FROM film_category
             WHERE film_id = :film_id AND category_id = :category_id",
              [':film_id' => $film_id, ':category_id' => $film_category->category_id]
            );
          } else {
            $key = array_search($film_category->category_id, $categories_ids);
            unset($categories_ids[$key]);
          }
        }

        foreach ($categories_ids as $category_id) {
          $qb->execute(
            "INSERT INTO film_category (film_id, category_id)
           VALUES (:film_id, :category_id)",
            [':film_id' => $film_id, ':category_id' => $category_id]
          );
        }

        $qb->commit();
      } catch (PDOException $e) {
        $qb->rollback();
    ?>
        <div class="alert alert-error">Ha ocurrido un error al actualizar las categorías</div>
    <?php
      }
    }

    $categories = $qb->fetchAll("SELECT category_id, name FROM category");

    $film_categories = array_map(
      fn($category) => $category->category_id,
      $qb->fetchAll("SELECT c.category_id, c.name
                     FROM category c
                     INNER JOIN film_category fc ON c.category_id = fc.category_id
                     WHERE fc.film_id = :film_id", [':film_id' => $film_id])
    );
    ?>

    <section id="films">
      <h2>Categorías de la pelicula: <?= $film->title ?></h2>
      <form action="category_film.php" action="post">
        <input type="hidden" name="film_id" value="<?= $film_id ?>">
        <ul>
          <?php foreach ($categories as $category) { ?>
            <li>
              <label>
                <input type="checkbox" name="categories[]" value="<?= $category->category_id ?>" <?php if (in_array($category->category_id, $film_categories)) echo "checked" ?>>
                <?= $category->name ?>
              </label>
            </li>
          <?php } ?>
        </ul>
        <p>
          <input type="submit" value="Actualizar">
        </p>
      </form>
      <section>
      <?php } ?>
    <?php } ?>
    <?php include "bottom.php"; ?>