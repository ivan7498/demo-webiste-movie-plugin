<?php

/**
 * Predložak za Single Movie stranicu
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<div class="movie-single">
    <?php
    if (have_posts()) :
        while (have_posts()) : the_post(); ?>
            <!-- Title moved above -->
            <h1 class="movie-title"><?php the_title(); ?></h1>

            <div class="movie-header">
                <div class="movie-poster">
                    <?php the_post_thumbnail('large'); ?>
                </div>
                <div class="movie-info">
                    <div class="movie-excerpt">
                        <p><?php the_excerpt(); ?></p>
                    </div>
                    <div class="movie-author">
                        <p><strong>Author:</strong> <?php echo esc_html(get_post_meta(get_the_ID(), 'author', true)); ?></p>
                    </div>

                    <?php
                    // Fetch the Movie Quote
                    $movie_quote = get_post_meta(get_the_ID(), 'quote', true); // Ensure 'quote' matches the metabox key
                    if ($movie_quote) :
                    ?>
                        <div class="movie-quote">
                            <p><em>&ldquo;<?php echo esc_html($movie_quote); ?>&rdquo;</em></p>
                        </div>
                    <?php endif; ?>

                    <?php
                    // Fetch the Movie Trailer URL
                    $movie_trailer_url = get_post_meta(get_the_ID(), 'movie_trailer_url', true);
                    if ($movie_trailer_url) :
                    ?>
                        <div class="movie-trailer">
                            <a href="<?php echo esc_url($movie_trailer_url); ?>" target="_blank" class="button">Watch Trailer</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="movie-genres">
                <?php echo get_the_term_list(get_the_ID(), 'genre', '<span class="genre-tag">', '</span><span class="genre-tag">', '</span>'); ?>
            </div>

            <div class="movie-details">
                <p><strong>Description:</strong> <?php the_content(); ?></p>
            </div>
    <?php endwhile;
    else :
        echo '<p>No movie found.</p>';
    endif;
    ?>
</div>

<?php
get_footer();

// End of single-movie.php
