<?php

/**
 * Template za Arhivu Filmova
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<section class="movie-archive">
    <h1>Filmovi</h1>




    <!-- Dropdown za filtriranje po žanrovima -->
    <select id="genre-filter">
        <option value="">Svi žanrovi</option>
        <?php
        // Dohvaćanje svih žanrova (taxonomy: genre)
        $genres = get_terms(['taxonomy' => 'genre', 'hide_empty' => true]);
        foreach ($genres as $genre) {
            echo "<option value='{$genre->slug}'>{$genre->name}</option>";
        }
        ?>
    </select>

    <!-- Lista filmova -->
    <ul id="movie-list">
        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                <li class="movie-card" data-genre="<?php
                                                    // Dohvaćanje slugova žanrova
                                                    $terms = get_the_terms(get_the_ID(), 'genre');
                                                    if ($terms) {
                                                        $genre_slugs = array_map(function ($term) {
                                                            return $term->slug;
                                                        }, $terms);
                                                        echo implode(' ', $genre_slugs);
                                                    }
                                                    ?>">
                    <!-- Naslov filma -->
                    <h3><?php the_title(); ?></h3>

                    <!-- Thumbnail filma -->
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="movie-thumbnail">
                            <?php the_post_thumbnail('medium'); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Kratak opis -->
                    <p><?php the_excerpt(); ?></p>
                </li>
            <?php endwhile;
        else : ?>
            <p>Nema filmova za prikaz.</p>
        <?php endif; ?>


    </ul>
</section>

<!-- Dodavanje JavaScript koda za filtriranje -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const genreFilter = document.getElementById('genre-filter');
        const allMovies = document.querySelectorAll('.movie-card');

        genreFilter.addEventListener('change', function() {
            const selectedGenre = genreFilter.value;

            allMovies.forEach(movie => {
                const movieGenres = movie.dataset.genre.split(' ');
                if (!selectedGenre || movieGenres.includes(selectedGenre)) {
                    movie.style.display = 'block';
                } else {
                    movie.style.display = 'none';
                }
            });
        });
    });
</script>

<?php get_footer(); ?>