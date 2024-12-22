<?php

// Definiranje konstante za dodatak
define('MOVIE_PLUGIN_NAMESPACE', 'movie-plugin/v1');
define('MOVIE_POST_TYPE', 'movie');



/**
 * Plugin Name: Movie plugin
 * Description: Plugin for managing movies and genres with advanced features. 
 * Version: 1.4
 * Author: Ivan K. Uskok 
 */

/**
 * Register the custom post type "Movie". 
 * This function creates the MOVIE_POST_TYPE post type with associated taxonomies.
 */
function mp_register_movie_post_type()
{
    $args = array(
        'labels' => array(
            'name'               => 'Movies',
            'singular_name'      => 'Movie',
            'add_new'            => 'Add New',
            'add_new_item'       => 'Add New Movie',
            'edit_item'          => 'Edit Movie',
            'new_item'           => 'New Movie',
            'view_item'          => 'View Movie',
            'search_items'       => 'Search Movies',
            'not_found'          => 'No movies found',
            'not_found_in_trash' => 'No movies found in Trash',
            'menu_name'          => 'Movies',
        ),
        'public'               => true,
        'has_archive'          => true,
        'rewrite'              => array('slug' => 'movies'),
        'show_in_rest'         => true,
        'supports'             => array('title', 'editor', 'thumbnail', 'excerpt'),
        'taxonomies'           => array('genre'),
    );
    register_post_type(MOVIE_POST_TYPE, $args);
}
add_action('init', 'mp_register_movie_post_type');

// Register the "Genre" taxonomy
function mp_register_genre_taxonomy()
{
    $args = array(
        'labels' => array(
            'name'              => 'Genres',
            'singular_name'     => 'Genre',
            'search_items'      => 'Search Genres',
            'all_items'         => 'All Genres',
            'parent_item'       => 'Parent Genre',
            'parent_item_colon' => 'Parent Genre:',
            'edit_item'         => 'Edit Genre',
            'update_item'       => 'Update Genre',
            'add_new_item'      => 'Add New Genre',
            'new_item_name'     => 'New Genre Name',
            'menu_name'         => 'Genres',
        ),
        'hierarchical'        => true,
        'show_ui'             => true,
        'show_admin_column'   => true,
        'query_var'           => true,
        'rewrite'             => array('slug' => 'genre'),
        'show_in_rest'        => true,
    );
    register_taxonomy('genre', array(MOVIE_POST_TYPE), $args);
}
add_action('init', 'mp_register_genre_taxonomy');

// Korištenje predložaka od strane dodatka
function mp_movie_templates($template)
{
    if ('is_singular'(MOVIE_POST_TYPE)) {
        $plugin_template = plugin_dir_path(__FILE__) . 'templates/single-movie.php';
        if (file_exists($plugin_template)) {
            return $plugin_template;
        }
    }

    if (is_post_type_archive(MOVIE_POST_TYPE)) {
        $plugin_template = plugin_dir_path(__FILE__) . 'templates/archive-movie.php';
        if (file_exists($plugin_template)) {
            return $plugin_template;
        }
    }

    return $template;
}
add_filter('template_include', 'mp_movie_templates');

// Enqueue stilovi i skripte
function mp_enqueue_assets()
{
    wp_enqueue_style(
        'movie-plugin-style',
        plugin_dir_url(__FILE__) . 'templates/assets/style.css',
        array(),
        '1.0',
        'all'
    );

    wp_enqueue_style(
        'swiper-css',
        'https://unpkg.com/swiper/swiper-bundle.min.css',
        array(),
        null,
        'all'
    );

    wp_enqueue_script(
        'swiper-js',
        'https://unpkg.com/swiper/swiper-bundle.min.js',
        array(),
        null,
        true
    );

    wp_enqueue_script(
        'movie-filter-js',
        plugin_dir_url(__FILE__) . 'templates/assets/movie-filter.js',
        array('jquery'),
        '1.0',
        true
    );
}
add_action('wp_enqueue_scripts', 'mp_enqueue_assets');


// Registracija Gutenberg blokova
function mp_register_gutenberg_block()
{
    wp_register_script(
        'favorite-movie-quotes',
        plugin_dir_url(__FILE__) . 'blocks/favorite-movie-quotes.js',
        array('wp-blocks', 'wp-element', 'wp-editor', 'wp-components'),
        filemtime(plugin_dir_path(__FILE__) . 'blocks/favorite-movie-quotes.js')
    );

    register_block_type('movie-plugin/favorite-movie-quotes', array(
        'editor_script' => 'favorite-movie-quotes',
    ));
}
add_action('init', 'mp_register_gutenberg_block');

// Shortcode for movie list
function mp_movies_shortcode($atts)
{
    $atts = shortcode_atts(
        array(
            'genre' => '',
            'number' => 5,
        ),
        $atts,
        'movies_list'
    );

    $args = array(
        'post_type' => MOVIE_POST_TYPE,
        'posts_per_page' => $atts['number'],
    );

    if ($atts['genre'] && $atts['genre'] !== 'all') {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'genre',
                'field'    => 'slug',
                'terms'    => $atts['genre'],
            ),
        );
    }

    $query = new WP_Query($args);
    $output = '<div class="movie-grid">'; // Grid container

    if ($query->have_posts()) {
        while ($query->have_posts()) : $query->the_post();

            // Get meta fields (trailer, rating, etc.)
            $rating = get_post_meta(get_the_ID(), 'movie_rating', true);
            $trailer_url = get_post_meta(get_the_ID(), 'movie_trailer_url', true);

            $output .= '<div class="movie-item">'; // Individual movie card
            $output .= '<a href="' . get_permalink() . '" class="movie-thumbnail">';
            $output .= get_the_post_thumbnail(get_the_ID(), 'medium', array('class' => 'movie-poster'));
            $output .= '</a>';
            $output .= '<div class="movie-info">';
            $output .= '<h2 class="movie-title">' . get_the_title() . '</h2>';

            // Display the excerpt or a default message if the excerpt is missing
            if (has_excerpt()) {
                $output .= '<p class="movie-description">' . get_the_excerpt() . '</p>';
            } else {
                $output .= '<p class="movie-description">No description available.</p>';
            }

            // Prikaži buttone
            $output .= '<div class="movie-buttons">';
            $output .= '<a href="' . esc_url($trailer_url) . '" class="movie-button trailer" target="_blank">Trailer</a>';
            $output .= '<a href="' . get_permalink() . '" class="movie-button read-more">Read More</a>';
            $output .= '</div>'; // End buttons

            $output .= '</div>'; // End movie-info
            $output .= '</div>'; // End movie-item

        endwhile;
    } else {
        $output .= '<p>No movies found.</p>';
    }

    wp_reset_postdata();
    $output .= '</div>'; // End movie-grid

    return $output;
}

add_shortcode('movies_list', 'mp_movies_shortcode');





// Shortcode za movie slider
function mp_movie_slider_shortcode($atts)
{
    $atts = shortcode_atts(
        array(
            'number' => 5, // Default number of movies to display
        ),
        $atts,
        'movie_slider'
    );

    $args = array(
        'post_type' => MOVIE_POST_TYPE,
        'posts_per_page' => $atts['number'],
    );

    $query = new WP_Query($args);

    ob_start();
?>
    <div class="swiper-container">
        <div class="swiper-wrapper">
            <?php
            $slider_query = new WP_Query([
                'post_type' => 'movie',
                'posts_per_page' => 5,
            ]);

            if ($slider_query->have_posts()) :
                while ($slider_query->have_posts()) : $slider_query->the_post(); ?>
                    <div class="swiper-slide">
                        <div class="slider-item">
                            <div class="slider-background">
                                <?php the_post_thumbnail('large'); ?>
                            </div>
                            <div class="slider-overlay">
                                <div class="slider-poster">
                                    <?php the_post_thumbnail('thumbnail'); ?>
                                </div>
                                <div class="slider-info">
                                    <h3 class="slider-title"><?php the_title(); ?></h3>
                                    <p class="slider-description">Watch new trailer</p>
                                    <div class="slider-meta">

                                        <div class="slider-icons">
                                            <span class="slider-likes">❤️ 228</span>
                                            <span class="slider-views">💡 487</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            <?php endwhile;
                wp_reset_postdata();
            endif;
            ?>
        </div>
        <!-- Navigacija -->
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            new Swiper('.swiper-container', {
                loop: true,
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
            });
        });
    </script>
<?php
    return ob_get_clean();
}
add_shortcode('movie_slider', 'mp_movie_slider_shortcode');

// Create custom REST API endpoint
function mp_register_movie_filter_endpoint()
{
    if (!function_exists('mp_filter_movies_callback')) {
        function mp_filter_movies_callback($data)
        {
            // Sanitize and retrieve input parameters
            $genre = isset($data['genre']) ? sanitize_text_field($data['genre']) : '';
            $number = isset($data['number']) ? intval($data['number']) : 5;

            // Prepare query arguments
            $args = array(
                'post_type' => MOVIE_POST_TYPE,
                'posts_per_page' => $number,
            );

            if ($genre) {
                $args['tax_query'] = array(
                    array(
                        'taxonomy' => 'genre',
                        'field'    => 'slug',
                        'terms'    => $genre,
                    ),
                );
            }

            // Query movies
            $query = new WP_Query($args);
            $movies = array();

            if ($query->have_posts()) {
                while ($query->have_posts()) {
                    $query->the_post();
                    $movies[] = array(
                        'title'       => get_the_title(),
                        'thumbnail'   => get_the_post_thumbnail_url(get_the_ID(), 'full'),
                        'description' => get_the_excerpt(),
                        'genre'       => wp_get_post_terms(get_the_ID(), 'genre', array('fields' => 'names')),
                    );
                }
                'wp_reset_postdata'();
            }

            return 'rest_ensure_response'($movies);
        }



        'register_rest_route'(MOVIE_PLUGIN_NAMESPACE, '/filter-movies/', array(
            'methods' => 'GET',
            'callback' => 'mp_filter_movies_callback',
            'permission_callback' => '__return_true',
        ));
    }
    add_action('rest_api_init', 'mp_register_movie_filter_endpoint');

    if (!function_exists('mp_filter_movies_callback')) {
        function mp_filter_movies_callback($data)
        {
            $genre = isset($data['genre']) ? sanitize_text_field($data['genre']) : '';
            $number = isset($data['number']) ? intval($data['number']) : 5;

            $args = array(
                'post_type' => MOVIE_POST_TYPE,
                'posts_per_page' => $number,
            );

            if ($genre) {
                $args['tax_query'] = array(
                    array(
                        'taxonomy' => 'genre',
                        'field'    => 'slug',
                        'terms'    => $genre,
                    ),
                );
            }

            $query = new WP_Query($args);
            $movies = array();

            if ($query->have_posts()) {
                while ($query->have_posts()) : $query->the_post();
                    $movies[] = array(
                        'title' => get_the_title(),
                        'link' => 'get_permalink'(),
                        'thumbnail' => 'get_the_post_thumbnail_url'(),
                        'description' => 'wp_trim_words'(get_the_content(), 20),
                    );
                endwhile;
            }
            'wp_reset_postdata'();

            return $movies;
        }


        /**
         * Enqueue JavaScript for AJAX filtering on the front-end.
         * Provides localized variables for AJAX URL and nonce.
         */
        function mp_enqueue_ajax_script()
        {
            wp_enqueue_script(
                'mp-ajax-filter',
                'plugin_dir_url'(__FILE__) . 'assets/js/ajax-filter.js',
                array('jquery'),
                null,
                true
            );
            'wp_localize_script'('mp-ajax-filter', 'mp_ajax', array(
                'ajax_url' => 'admin_url'('admin-ajax.php')
            ));
        }
        add_action('wp_enqueue_scripts', 'mp_enqueue_ajax_script');

        /**
         * Handle AJAX request for movie filtering.
         * Processes 'genre' and 'number' parameters.
         * Returns filtered movies in JSON format.
         */
        function mp_filter_movies_ajax()
        {
            $genre = isset($_POST['genre']) ? sanitize_text_field($_POST['genre']) : '';
            $number = isset($_POST['number']) ? intval($_POST['number']) : 5;

            $args = array(
                'post_type' => MOVIE_POST_TYPE,
                'posts_per_page' => $number,
            );

            if ($genre) {
                $args['tax_query'] = array(
                    array(
                        'taxonomy' => 'genre',
                        'field'    => 'slug',
                        'terms'    => $genre,
                    ),
                );
            }

            $query = new WP_Query($args);
            $movies = array();

            if ($query->have_posts()) {
                while ($query->have_posts()) {
                    $query->the_post();
                    $movies[] = array(
                        'title'       => get_the_title(),
                        'thumbnail'   => 'get_the_post_thumbnail_url'('get_the_ID'(), 'full'),
                        'description' => get_the_excerpt(),
                        'genre'       => wp_get_post_terms(get_the_ID(), 'genre', array('fields' => 'names')),
                    );
                }
                'wp_reset_postdata'();
            }

            'wp_send_json'($movies);
        }
        add_action('wp_ajax_filter_movies', 'mp_filter_movies_ajax');
        add_action('wp_ajax_nopriv_filter_movies', 'mp_filter_movies_ajax');


        /**
         * Register the custom taxonomy "Genre".
         * This function creates the "genre" taxonomy for the Movie post type.
         */
        function mp_register_movie_taxonomy()
        {
            $args = array(
                'labels' => array(
                    'name'          => 'Genres',
                    'singular_name' => 'Genre',
                    'search_items'  => 'Search Genres',
                    'all_items'     => 'All Genres',
                    'edit_item'     => 'Edit Genre',
                    'update_item'   => 'Update Genre',
                    'add_new_item'  => 'Add New Genre',
                    'new_item_name' => 'New Genre Name',
                    'menu_name'     => 'Genres',
                ),
                'hierarchical'  => true,
                'public'        => true,
                'show_in_rest'  => true,
                'rewrite'       => array('slug' => 'genres'),
            );

            register_taxonomy('genre', array(MOVIE_POST_TYPE), $args);
        }
        add_action('init', 'mp_register_movie_taxonomy');
    }
}




// Dodavanje metabox za Movie Trailer
function add_movie_trailer_metabox()
{
    add_meta_box(
        'movie_trailer_metabox', // ID of the metabox
        'Movie Trailer',         // Title of the metabox
        'movie_trailer_metabox_callback', // Callback function to display the metabox
        'movie',                 // Post type where the metabox appears
        'side',                  // Context: side, normal, or advanced
        'high'                   // Priority: high or default
    );
}
add_action('add_meta_boxes', 'add_movie_trailer_metabox');

// Metabox callback function
function movie_trailer_metabox_callback($post)
{
    // Retrieve existing value from post meta
    $movie_trailer_url = get_post_meta($post->ID, 'movie_trailer_url', true);

    // Display the input field
?>
    <p>
        <label for="movie_trailer_url">Trailer URL:</label>
        <input type="url" id="movie_trailer_url" name="movie_trailer_url" value="<?php echo esc_url($movie_trailer_url); ?>" placeholder="https://example.com/trailer" style="width: 100%;">
    </p>
<?php
}

// Save the metabox data
function save_movie_trailer_metabox($post_id)
{
    // Verify nonce and autosave check
    if (!isset($_POST['movie_trailer_nonce']) || !wp_verify_nonce($_POST['movie_trailer_nonce'], 'save_movie_trailer')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Save or update the trailer URL
    if (isset($_POST['movie_trailer_url'])) {
        update_post_meta($post_id, 'movie_trailer_url', esc_url_raw($_POST['movie_trailer_url']));
    }
}
add_action('save_post', 'save_movie_trailer_metabox');

// Dodavanje metaboxa za Movie post type
function add_movie_metabox()
{
    add_meta_box(
        'movie_details',          // ID
        'Movie Details',          // Naslov
        'render_movie_metabox',   // Callback funkcija
        'movie',                  // Post tip
        'normal',                 // Kontekst
        'high'                    // Prioritet
    );
}
add_action('add_meta_boxes', 'add_movie_metabox');

// Prikaz metaboxa u editoru
function render_movie_metabox($post)
{
    // Dohvaćanje postojeće vrijednosti metapodataka
    $quote = get_post_meta($post->ID, 'quote', true);
    $author = get_post_meta($post->ID, 'author', true);
?>
    <p>
        <label for="movie_quote">Quote:</label>
        <input type="text" id="movie_quote" name="movie_quote" value="<?php echo esc_attr($quote); ?>" style="width:100%;" />
    </p>
    <p>
        <label for="movie_author">Autor:</label>
        <input type="text" id="movie_author" name="movie_author" value="<?php echo esc_attr($author); ?>" style="width:100%;" />
    </p>
<?php
}

// Spremanje podataka iz metaboxa
function save_movie_meta($post_id)
{
    // Provjera i spremanje 'quote'
    if (array_key_exists('movie_quote', $_POST)) {
        update_post_meta($post_id, 'quote', sanitize_text_field($_POST['movie_quote']));
    }
    // Provjera i spremanje 'author'
    if (array_key_exists('movie_author', $_POST)) {
        update_post_meta($post_id, 'author', sanitize_text_field($_POST['movie_author']));
    }
}
add_action('save_post', 'save_movie_meta');


/**
 * Register REST API endpoint for filtering movies.
 */
add_action('rest_api_init', function () {
    register_rest_route(MOVIE_PLUGIN_NAMESPACE, '/movies', [
        'methods'  => 'GET',
        'callback' => 'mp_filter_movies',
        'permission_callback' => '__return_true',
    ]);
});

function mp_filter_movies($data)
{
    $genre = $data->get_param('genre');
    $args = [
        'post_type'      => MOVIE_POST_TYPE,
        'posts_per_page' => -1,
    ];

    if ($genre) {
        $args['tax_query'] = [
            [
                'taxonomy' => 'genre',
                'field'    => 'slug',
                'terms'    => $genre,
            ],
        ];
    }

    $query = new WP_Query($args);
    $movies = [];

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $movies[] = [
                'id'    => get_the_ID(),
                'title' => get_the_title(),
                'link'  => get_permalink(),
                'thumbnail' => get_the_post_thumbnail_url(get_the_ID(), 'medium'),
                'excerpt' => get_the_excerpt(),
            ];
        }
        wp_reset_postdata();
    }

    return rest_ensure_response($movies);
}

/** video field */

function mp_localize_movie_filter_script()
{
    if (is_post_type_archive('movie')) {
        wp_localize_script('movie-filter', 'wpApiSettings', [
            'root'      => esc_url_raw(rest_url()),
            'namespace' => 'movie-plugin/v1',
        ]);
    }
}
add_action('wp_enqueue_scripts', 'mp_localize_movie_filter_script');

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
        'key' => 'group_movie_video',
        'title' => 'Video za Film',
        'fields' => array(
            array(
                'key' => 'field_movie_video',
                'label' => 'Video',
                'name' => 'movie_video',
                'type' => 'url',
                'instructions' => 'Unesite URL videa za film.',
                'required' => 0,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'movie',
                ),
            ),
        ),
    ));
}
